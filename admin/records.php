<?php
session_start();

include '../config/db.php';

/*
|--------------------------------------------------------------------------
| INCIDENT RECORDS
|--------------------------------------------------------------------------
|
| ACTIVE INCIDENTS
|   resolved_at IS NULL
|
| ARCHIVED INCIDENTS
|   resolved_at IS NOT NULL
|
| IMPORTANT:
| We use resolved_at as the archive marker instead of setting
| status = 'Resolved', because the current PostgreSQL enum does
| not accept "Resolved".
|
*/


// ============================================================
// RESOLVE INCIDENT
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resolve_incident'])) {

    $incidentId = $_POST['incident_id'] ?? null;

    if ($incidentId) {

        try {

            /*
             * Mark the incident as resolved by recording
             * the date/time it was resolved.
             *
             * We DO NOT modify the status enum.
             */

            $stmt = $pdo->prepare("
                UPDATE incidents
                SET resolved_at = NOW()
                WHERE id = ?
                  AND resolved_at IS NULL
            ");

            $stmt->execute([$incidentId]);

        } catch (PDOException $e) {

            die("Error resolving incident: " . $e->getMessage());

        }
    }

    // Prevent form resubmission
    header("Location: records.php");
    exit;
}


// ============================================================
// CURRENT VIEW
// ============================================================

$view = $_GET['view'] ?? 'active';

if (!in_array($view, ['active', 'archive'], true)) {
    $view = 'active';
}


// ============================================================
// FILTER VALUES
// ============================================================

$categoryFilter  = trim($_GET['category'] ?? '');
$priorityFilter  = trim($_GET['priority'] ?? '');
$extremityFilter = trim($_GET['extremity'] ?? '');
$statusFilter    = trim($_GET['status'] ?? '');


// ============================================================
// PRIORITY OPTIONS
// ============================================================

$priorityOptions = [
    'Critical',
    'High',
    'Medium',
    'Low'
];


// ============================================================
// EXTREMITY OPTIONS
// ============================================================

/*
 * The database field remains "severity".
 *
 * The user interface displays it as "Extremity".
 *
 * Existing values:
 *   Extreme
 *   Severe
 *   Moderate
 *   Minor
 */

$extremityOptions = [
    'Extreme',
    'Severe',
    'Moderate',
    'Minor'
];


// ============================================================
// FETCH INCIDENT CATEGORIES
// ============================================================

try {

    $categoryStmt = $pdo->prepare("
        SELECT DISTINCT category
        FROM incidents
        WHERE category IS NOT NULL
          AND category <> ''
        ORDER BY category ASC
    ");

    $categoryStmt->execute();

    $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {

    $categories = [];

}


// ============================================================
// BUILD INCIDENT QUERY
// ============================================================

try {

    $where = [];

    $params = [];


    // ========================================================
    // ACTIVE / ARCHIVE
    // ========================================================

    if ($view === 'active') {

        // Active incidents have not been resolved.
        $where[] = "resolved_at IS NULL";

    } else {

        // Archived incidents have a resolved date.
        $where[] = "resolved_at IS NOT NULL";

    }


    // ========================================================
    // CATEGORY FILTER
    // ========================================================

    if ($categoryFilter !== '') {

        $where[] = "category = ?";

        $params[] = $categoryFilter;

    }


    // ========================================================
    // PRIORITY FILTER
    // ========================================================

    if ($priorityFilter !== '') {

        $where[] = "priority_level = ?";

        $params[] = $priorityFilter;

    }


    // ========================================================
    // EXTREMITY FILTER
    // ========================================================

    if ($extremityFilter !== '') {

        $where[] = "severity = ?";

        $params[] = $extremityFilter;

    }


    // ========================================================
    // STATUS FILTER
    // ========================================================

    /*
     * IMPORTANT:
     *
     * We never compare the enum to "Resolved".
     *
     * Resolved:
     *     resolved_at IS NOT NULL
     *
     * Active:
     *     resolved_at IS NULL
     *
     * Other statuses use the existing database enum values.
     */

    if ($statusFilter !== '') {

        if ($statusFilter === 'Resolved') {

            $where[] = "resolved_at IS NOT NULL";

        } elseif ($statusFilter === 'Active') {

            $where[] = "resolved_at IS NULL";

        } else {

            $where[] = "status = ?";

            $params[] = $statusFilter;

        }

    }


    // ========================================================
    // WHERE CLAUSE
    // ========================================================

    $whereSQL = '';

    if (!empty($where)) {

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

    }


    // ========================================================
    // FETCH INCIDENTS
    // ========================================================

    $query = "
        SELECT *
        FROM incidents

        $whereSQL

        ORDER BY

            CASE priority_level

                WHEN 'Critical' THEN 1

                WHEN 'High' THEN 2

                WHEN 'Medium' THEN 3

                WHEN 'Low' THEN 4

                ELSE 5

            END,

            created_at DESC
    ";


    $stmt = $pdo->prepare($query);

    $stmt->execute($params);

    $incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {

    die("Error fetching incidents: " . $e->getMessage());

}

?>


<!DOCTYPE html>

<html lang="en">


<head>

    <meta charset="UTF-8">


    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <title>

        <?= $view === 'archive'
            ? 'Incident Archive'
            : 'Incident Records'
        ?>

    </title>


    <!-- ======================================================
         BOOTSTRAP CSS
    ======================================================= -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- ======================================================
         BOOTSTRAP ICONS
    ======================================================= -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
    >


    <!-- ======================================================
         LEAFLET CSS
    ======================================================= -->

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >


    <!-- ======================================================
         YOUR EXISTING CSS
    ======================================================= -->

    <link
        rel="stylesheet"
        href="assets/style.css"
    >


    <!-- ======================================================
         FAVICON
    ======================================================= -->

    <link
        rel="icon"
        type="image/png"
        href="assets/log.png"
    >

</head>


<body>


    <!-- ======================================================
         SIDEBAR
    ======================================================= -->

    <?php include 'sidebar.php'; ?>


    <!-- ======================================================
         MAIN CONTENT
    ======================================================= -->

    <main class="main-content">


        <div class="dashboard-wrapper">


            <!-- ==================================================
                 PAGE HEADER
            ================================================== -->

            <div class="records-header">


                <div>


                    <?php if ($view === 'archive'): ?>


                        <h2 class="dashboard-title mb-1">

                            INCIDENT ARCHIVE

                        </h2>


                        <div class="archive-info">

                            Resolved incidents are automatically
                            stored here.

                        </div>


                    <?php else: ?>


                        <h2 class="dashboard-title mb-1">

                            INCIDENT RECORDS

                        </h2>


                    <?php endif; ?>


                </div>


                <!-- ==================================================
                     HEADER BUTTON
                ================================================== -->

                <div class="records-header-actions">


                    <?php if ($view === 'active'): ?>


                        <a
                            href="records.php?view=archive"
                            class="archive-btn"
                        >

                            <i class="bi bi-archive"></i>

                            Archive

                        </a>


                    <?php else: ?>


                        <a
                            href="records.php"
                            class="active-btn"
                        >

                            <i class="bi bi-arrow-left"></i>

                            Active Incidents

                        </a>


                    <?php endif; ?>


                </div>


            </div>


            <!-- ==================================================
                 FILTER CARD
            ================================================== -->

            <div class="filter-card">


                <form
                    method="GET"
                    action="records.php"
                >


                    <!-- Preserve active/archive view -->

                    <input
                        type="hidden"
                        name="view"
                        value="<?= htmlspecialchars($view); ?>"
                    >


                    <div class="row g-3">


                        <!-- ==================================================
                             INCIDENT CATEGORY
                        ================================================== -->

                        <div class="col-md-3">


                            <label class="filter-label">

                                Incident

                            </label>


                            <select
                                name="category"
                                class="filter-select"
                            >


                                <option value="">

                                    All Incidents

                                </option>


                                <?php foreach ($categories as $category): ?>


                                    <option
                                        value="<?= htmlspecialchars($category); ?>"
                                        <?= $categoryFilter === $category
                                            ? 'selected'
                                            : ''; ?>
                                    >

                                        <?= htmlspecialchars($category); ?>

                                    </option>


                                <?php endforeach; ?>


                            </select>


                        </div>


                        <!-- ==================================================
                             PRIORITY
                        ================================================== -->

                        <div class="col-md-3">


                            <label class="filter-label">

                                Priority Level

                            </label>


                            <select
                                name="priority"
                                class="filter-select"
                            >


                                <option value="">

                                    All Priority Levels

                                </option>


                                <?php foreach ($priorityOptions as $priority): ?>


                                    <option
                                        value="<?= htmlspecialchars($priority); ?>"
                                        <?= $priorityFilter === $priority
                                            ? 'selected'
                                            : ''; ?>
                                    >

                                        <?= htmlspecialchars($priority); ?>

                                    </option>


                                <?php endforeach; ?>


                            </select>


                        </div>


                        <!-- ==================================================
                             EXTREMITY
                        ================================================== -->

                        <div class="col-md-3">


                            <label class="filter-label">

                                Extremity

                            </label>


                            <select
                                name="extremity"
                                class="filter-select"
                            >


                                <option value="">

                                    All Extremity Levels

                                </option>


                                <?php foreach ($extremityOptions as $extremity): ?>


                                    <option
                                        value="<?= htmlspecialchars($extremity); ?>"
                                        <?= $extremityFilter === $extremity
                                            ? 'selected'
                                            : ''; ?>
                                    >

                                        <?= htmlspecialchars($extremity); ?>

                                    </option>


                                <?php endforeach; ?>


                            </select>


                        </div>


                        <!-- ==================================================
                             STATUS
                        ================================================== -->

                        <div class="col-md-3">


                            <label class="filter-label">

                                Status

                            </label>


                            <select
                                name="status"
                                class="filter-select"
                            >


                                <?php if ($view === 'archive'): ?>


                                    <option
                                        value="Resolved"
                                        selected
                                    >

                                        Resolved

                                    </option>


                                <?php else: ?>


                                    <option value="">

                                        All Active Statuses

                                    </option>


                                    <option
                                        value="Pending"
                                        <?= $statusFilter === 'Pending'
                                            ? 'selected'
                                            : ''; ?>
                                    >

                                        Pending

                                    </option>


                                    <option
                                        value="In Progress"
                                        <?= $statusFilter === 'In Progress'
                                            ? 'selected'
                                            : ''; ?>
                                    >

                                        In Progress

                                    </option>


                                    <option
                                        value="Active"
                                        <?= $statusFilter === 'Active'
                                            ? 'selected'
                                            : ''; ?>
                                    >

                                        Active

                                    </option>


                                <?php endif; ?>


                            </select>


                        </div>


                        <!-- ==================================================
                             FILTER BUTTONS
                        ================================================== -->

                        <div class="col-12">


                            <div class="filter-actions">


                                <button
                                    type="submit"
                                    class="filter-btn"
                                >

                                    <i class="bi bi-funnel"></i>

                                    Apply Filters

                                </button>


                                <a
                                    href="records.php?view=<?= htmlspecialchars($view); ?>"
                                    class="clear-filter-btn"
                                >

                                    <i class="bi bi-x-circle"></i>

                                    Clear

                                </a>


                            </div>


                        </div>


                    </div>


                </form>


            </div>


            <!-- ==================================================
                 INCIDENT TABLE CARD
            ================================================== -->

            <div class="dash-card">


                <div class="table-responsive">


                    <table
                        class="table table-bordered table-striped incident-table"
                    >


                        <thead>


                            <tr>


                                <th>
                                    ID
                                </th>


                                <th>
                                    Incident
                                </th>


                                <th>
                                    Location
                                </th>


                                <th>
                                    Priority Level
                                </th>


                                <th>
                                    Extremity
                                </th>


                                <th>
                                    Status
                                </th>


                                <th>
                                    Date Reported
                                </th>


                                <?php if ($view === 'archive'): ?>


                                    <th>
                                        Date Resolved
                                    </th>


                                <?php else: ?>


                                    <th>
                                        Action
                                    </th>


                                <?php endif; ?>


                            </tr>


                        </thead>


                        <tbody>


                            <?php if (!empty($incidents)): ?>


                                <?php foreach ($incidents as $incident): ?>


                                    <?php

                                    // ==================================================
                                    // PRIORITY
                                    // ==================================================

                                    $priority =
                                        $incident['priority_level']
                                        ?? 'Medium';


                                    switch ($priority) {

                                        case 'Critical':

                                            $priorityClass =
                                                'priority-critical';

                                            break;


                                        case 'High':

                                            $priorityClass =
                                                'priority-high';

                                            break;


                                        case 'Medium':

                                            $priorityClass =
                                                'priority-medium';

                                            break;


                                        case 'Low':

                                            $priorityClass =
                                                'priority-low';

                                            break;


                                        default:

                                            $priorityClass =
                                                'priority-medium';

                                    }


                                    // ==================================================
                                    // EXTREMITY
                                    // ==================================================

                                    /*
                                     * Database field:
                                     * severity
                                     *
                                     * Display label:
                                     * Extremity
                                     */

                                    $extremity =
                                        $incident['severity']
                                        ?? 'Moderate';


                                    switch ($extremity) {

                                        case 'Extreme':

                                            $severityClass =
                                                'severity-extreme';

                                            break;


                                        case 'Severe':

                                            $severityClass =
                                                'severity-severe';

                                            break;


                                        case 'Moderate':

                                            $severityClass =
                                                'severity-moderate';

                                            break;


                                        case 'Minor':

                                            $severityClass =
                                                'severity-minor';

                                            break;


                                        default:

                                            $severityClass =
                                                'severity-moderate';

                                    }


                                    // ==================================================
                                    // STATUS
                                    // ==================================================

                                    /*
                                     * resolved_at determines whether
                                     * the incident is resolved.
                                     *
                                     * This prevents the PostgreSQL
                                     * enum error involving "Resolved".
                                     */

                                    if (
                                        !empty(
                                            $incident['resolved_at']
                                        )
                                    ) {

                                        $displayStatus =
                                            'Resolved';

                                        $statusClass =
                                            'status-resolved';

                                    } else {

                                        $displayStatus =
                                            $incident['status']
                                            ?? 'Pending';


                                        if (
                                            $displayStatus ===
                                            'Pending'
                                        ) {

                                            $statusClass =
                                                'status-pending';

                                        } elseif (
                                            $displayStatus ===
                                            'In Progress'
                                        ) {

                                            $statusClass =
                                                'status-progress';

                                        } else {

                                            $statusClass =
                                                'status-default';

                                        }

                                    }

                                    ?>


                                    <tr>


                                        <!-- ==================================================
                                             ID
                                        ================================================== -->

                                        <td>

                                            #<?= htmlspecialchars(
                                                $incident['id']
                                            ); ?>

                                        </td>


                                        <!-- ==================================================
                                             INCIDENT
                                        ================================================== -->

                                        <td>

                                            <strong>

                                                <?= htmlspecialchars(
                                                    $incident['category']
                                                    ?? 'Unknown'
                                                ); ?>

                                            </strong>

                                        </td>


                                        <!-- ==================================================
                                             LOCATION
                                        ================================================== -->

                                        <td>

                                            <?= htmlspecialchars(
                                                $incident['location']
                                                ?? 'Unknown'
                                            ); ?>

                                        </td>


                                        <!-- ==================================================
                                             PRIORITY LEVEL
                                        ================================================== -->

                                        <td>

                                            <span
                                                class="priority-badge <?= $priorityClass; ?>"
                                            >

                                                <?= htmlspecialchars(
                                                    $priority
                                                ); ?>

                                            </span>

                                        </td>


                                        <!-- ==================================================
                                             EXTREMITY
                                        ================================================== -->

                                        <td>

                                            <span
                                                class="severity-badge <?= $severityClass; ?>"
                                            >

                                                <?= htmlspecialchars(
                                                    $extremity
                                                ); ?>

                                            </span>

                                        </td>


                                        <!-- ==================================================
                                             STATUS
                                        ================================================== -->

                                        <td>

                                            <span
                                                class="status-badge <?= $statusClass; ?>"
                                            >

                                                <?= htmlspecialchars(
                                                    $displayStatus
                                                ); ?>

                                            </span>

                                        </td>


                                        <!-- ==================================================
                                             DATE REPORTED
                                        ================================================== -->

                                        <td>

                                            <?php if (
                                                !empty(
                                                    $incident['created_at']
                                                )
                                            ): ?>


                                                <?= date(
                                                    'M d, Y h:i A',
                                                    strtotime(
                                                        $incident['created_at']
                                                    )
                                                ); ?>


                                            <?php else: ?>


                                                N/A


                                            <?php endif; ?>

                                        </td>


                                        <!-- ==================================================
                                             ARCHIVE DATE
                                        ================================================== -->

                                        <?php if (
                                            $view === 'archive'
                                        ): ?>


                                            <td>

                                                <?php if (
                                                    !empty(
                                                        $incident[
                                                            'resolved_at'
                                                        ]
                                                    )
                                                ): ?>


                                                    <?= date(
                                                        'M d, Y h:i A',
                                                        strtotime(
                                                            $incident[
                                                                'resolved_at'
                                                            ]
                                                        )
                                                    ); ?>


                                                <?php else: ?>


                                                    N/A


                                                <?php endif; ?>


                                            </td>


                                        <?php else: ?>


                                            <!-- ==================================================
                                                 RESOLVE BUTTON
                                            ================================================== -->

                                            <td>


                                                <form
                                                    method="POST"
                                                    onsubmit="return confirmResolve();"
                                                >


                                                    <input
                                                        type="hidden"
                                                        name="incident_id"
                                                        value="<?= htmlspecialchars(
                                                            $incident['id']
                                                        ); ?>"
                                                    >


                                                    <button
                                                        type="submit"
                                                        name="resolve_incident"
                                                        class="resolve-btn"
                                                    >

                                                        <i
                                                            class="bi bi-check-circle"
                                                        ></i>

                                                        Resolve

                                                    </button>


                                                </form>


                                            </td>


                                        <?php endif; ?>


                                    </tr>


                                <?php endforeach; ?>


                            <?php else: ?>


                                <!-- ==================================================
                                     EMPTY TABLE
                                ================================================== -->

                                <tr>


                                    <td
                                        colspan="8"
                                        class="text-center empty-records"
                                    >


                                        <?php if (
                                            $view === 'archive'
                                        ): ?>


                                            <i
                                                class="bi bi-archive"
                                                style="font-size: 35px;"
                                            ></i>


                                            <div class="mt-2">

                                                No archived incidents.

                                            </div>


                                            <small>

                                                Resolved incidents will
                                                automatically appear here.

                                            </small>


                                        <?php else: ?>


                                            <i
                                                class="bi bi-check-circle"
                                                style="font-size: 35px;"
                                            ></i>


                                            <div class="mt-2">

                                                No active incident records.

                                            </div>


                                            <small>

                                                All reported incidents have
                                                been resolved or no incidents
                                                match your selected filters.

                                            </small>


                                        <?php endif; ?>


                                    </td>


                                </tr>


                            <?php endif; ?>


                        </tbody>


                    </table>


                </div>


            </div>


        </div>


    </main>


    <script>

        function confirmResolve() {

            return confirm(
                "Are you sure you want to mark this incident as RESOLVED?\n\n" +
                "The incident will automatically be moved to the Archive."
            );

        }

    </script>


</body>

</html>