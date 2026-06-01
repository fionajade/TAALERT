<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Safety Information</title>
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

            <h2 class="dashboard-title">SAFETY INFORMATION</h2>

            <div class="dash-card">

                <h4>Volcanic Eruption Preparedness</h4>

                <ul>
                    <li>Prepare an emergency go-bag.</li>
                    <li>Wear masks during ashfall.</li>
                    <li>Stay updated through official alerts.</li>
                    <li>Evacuate immediately when ordered.</li>
                </ul>

            </div>

        </div>

    </main>
</body>

</html>