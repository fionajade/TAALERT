<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Map Monitoring</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" type="image/png" href="assets/log.png">
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <div class="dashboard-wrapper">

            <h2 class="dashboard-title">MAP MONITORING</h2>

            <div class="dash-card p-2">

                <iframe
                    src="mapping/elections-2025/index.html"
                    width="100%"
                    height="700"
                    style="border:none;border-radius:12px;">
                </iframe>

            </div>

        </div>

    </main>

</body>

</html>