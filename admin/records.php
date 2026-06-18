<?php
session_start();

include '../config/db.php';


// ==========================
// FETCH INCIDENT RECORDS
// ==========================

$stmt = $pdo->prepare("
    SELECT *
    FROM incidents
    ORDER BY created_at DESC
");

$stmt->execute();

$incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html>

<head>

    <title>Records</title>


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />


    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/png" href="assets/log.png">


</head>


<body>


    <?php include 'sidebar.php'; ?>



    <main class="main-content">


        <div class="dashboard-wrapper">


            <h2 class="dashboard-title">
                INCIDENT RECORDS
            </h2>




            <div class="dash-card">


                <div class="table-responsive">


                    <table class="table table-bordered table-striped">


                        <thead>

                            <tr>

                                <th>ID</th>

                                <th>Incident</th>

                                <th>Location</th>

                                <th>Status</th>

                                <th>Date</th>

                            </tr>


                        </thead>



                        <tbody>



                            <?php if (!empty($incidents)): ?>



                                <?php foreach ($incidents as $incident): ?>


                                    <tr>


                                        <td>
                                            <?= $incident['id']; ?>
                                        </td>



                                        <td>
                                            <?= htmlspecialchars($incident['category'] ?? 'Unknown'); ?>
                                        </td>


                                        <td>
                                            <?= htmlspecialchars($incident['location']); ?>
                                        </td>



                                        <td>
                                            <?= htmlspecialchars($incident['status'] ?? 'Pending'); ?>
                                        </td>



                                        <td>
                                            <?= htmlspecialchars($incident['created_at']); ?>
                                        </td>



                                    </tr>



                                <?php endforeach; ?>



                            <?php else: ?>


                                <tr>

                                    <td colspan="5" class="text-center">

                                        No records available.

                                    </td>

                                </tr>



                            <?php endif; ?>



                        </tbody>



                    </table>


                </div>


            </div>


        </div>


    </main>



</body>

</html>