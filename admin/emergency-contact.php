<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Emergency Contacts</title>
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

    <main class="main-content">

        <div class="dashboard-wrapper">

            <h2 class="dashboard-title">EMERGENCY CONTACTS</h2>

            <div class="dash-card">

                <table class="table">

                    <thead>
                        <tr>
                            <th>Agency</th>
                            <th>Contact Number</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>MDRRMO</td>
                            <td>09123456789</td>
                        </tr>

                        <tr>
                            <td>PNP</td>
                            <td>09987654321</td>
                        </tr>

                        <tr>
                            <td>BFP</td>
                            <td>09112223344</td>
                        </tr>
                    </tbody>

                </table>

            </div>

        </div>

    </main>
</body>

</html>