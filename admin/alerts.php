<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Alerts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Leaflet CSS for the Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/png" href="assets/log.png">
</head>
<?php include 'sidebar.php'; ?>

<body>

    <main class="main-content">

        <div class="dashboard-wrapper">
            <h2 class="dashboard-title">ALERTS</h2>

            <div class="dash-card">
                <h3 class="card-title">Alert Management</h3>

                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Date Sent</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td colspan="4">No alerts found.</td>
                        </tr>
                    </tbody>
                </table>

            </div>

        </div>

    </main>
</body>

</html>