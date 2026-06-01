<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Incident - TAALERT</title>

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

        <header class="top-bar">
            <h1 class="top-title">TAALERT</h1>

            <div class="top-icons">
                <i class="bi bi-bell"></i>
                <i class="bi bi-person"></i>
            </div>
        </header>

        <div class="dashboard-wrapper">

            <h2 class="dashboard-title">REPORT INCIDENT</h2>

            <div class="dash-card">

                <form action="save_incident.php" method="POST" enctype="multipart/form-data">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Incident Type</label>

                            <select name="incident_type" class="form-select" required>
                                <option value="">Select Incident Type</option>
                                <option>Volcanic Tremor</option>
                                <option>Ashfall</option>
                                <option>Gas Emission</option>
                                <option>Road Blockage</option>
                                <option>Flood</option>
                                <option>Landslide</option>
                                <option>Fire</option>
                                <option>Earthquake</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Severity Level</label>

                            <select name="severity" class="form-select" required>
                                <option value="">Select Severity</option>
                                <option>Critical</option>
                                <option>High</option>
                                <option>Medium</option>
                                <option>Low</option>
                            </select>
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location</label>

                        <input type="text"
                            name="location"
                            class="form-control"
                            placeholder="Enter location"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>

                        <textarea
                            name="description"
                            class="form-control"
                            rows="5"
                            placeholder="Describe the incident..."
                            required></textarea>
                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>

                            <input type="text"
                                name="latitude"
                                class="form-control"
                                placeholder="14.0940">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>

                            <input type="text"
                                name="longitude"
                                class="form-control"
                                placeholder="121.0197">
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Evidence</label>

                        <input type="file"
                            name="incident_image"
                            class="form-control"
                            accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i>
                        Submit Incident
                    </button>

                </form>

            </div>

        </div>

    </main>

</body>

</html>