<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Records</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Leaflet CSS for the Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>

<body>

    <main class="main-content">

        <div class="dashboard-wrapper">

            <h2 class="dashboard-title">INCIDENT RECORDS</h2>

            <div class="dash-card">

                <table class="table">

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
                        <tr>
                            <td colspan="5">
                                No records available.
                            </td>
                        </tr>
                    </tbody>

                </table>

            </div>

        </div>

    </main>

</body>

</html>