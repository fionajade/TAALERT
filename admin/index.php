<?php
$host = 'aws-1-ap-northeast-1.pooler.supabase.com';
$port = '6543';
$dbname = 'postgres';
$user = 'postgres.okwnknzodfvbsbgetvzh';
$password = 'taliresq67_';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

$reports = [];
$totalReports = 0;
$totalUsers = 0;
$error_message = null;

try {

    $pdo = new PDO(
        $dsn,
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Total registered users
    $stmt = $pdo->query("
        SELECT public.get_registered_users() AS total
    ");
    $totalUsers = $stmt->fetch()['total'];

    // Total reports
    $stmt = $pdo->query("
        SELECT COUNT(*) AS total
        FROM reports
    ");
    $totalReports = $stmt->fetch()['total'];

    // Recent reports
    $stmt = $pdo->query("
    SELECT
        id,
        created_at,
        type,
        location,
        description,
        photo_url
    FROM reports
    ORDER BY created_at DESC
    LIMIT 10
");

    $reports = $stmt->fetchAll();

    //Reports by type for the donut chart
    $stmt = $pdo->query("
    SELECT
        type,
        COUNT(*) AS total
    FROM reports
    GROUP BY type
    ORDER BY total DESC
");

    $incidentTypes = $stmt->fetchAll();

} catch (PDOException $e) {
    $error_message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAALERT Dashboard</title>
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

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navigation Area -->
        <header class="top-bar">
            <h1 class="top-title">TAALERT</h1>
            <div class="top-icons">
                <i class="bi bi-bell"></i>
                <i class="bi bi-person"></i>
            </div>
        </header>

        <!-- White Dashboard Area -->
        <div class="dashboard-wrapper">
            <h2 class="dashboard-title">DASHBOARD</h2>

            <!-- MASTER CSS GRID -->
            <div class="master-grid">
                <!-- ROW 1 (Stats) -->
                <div class="dash-card stat-card span-3">
                    <h3 class="card-title">TOTAL REPORTS</h3>
                    <p class="stat-number">
                        <?php echo $totalReports; ?>
                    </p>
                </div>
                <div class="dash-card stat-card span-3">
                    <h3 class="card-title">ACTIVE INCIDENTS</h3>
                    <p class="stat-number">0</p>
                </div>
                <div class="dash-card stat-card span-3">
                    <h3 class="card-title">REGISTERED USERS</h3>
                    <p class="stat-number">
                        <?php echo $totalUsers; ?>
                    </p>
                </div>
                <div class="dash-card stat-card span-3">
                    <h3 class="card-title">ALERTS SENT TODAY</h3>
                    <p class="stat-number">0</p>
                </div>

                <!-- ROW 2 (Table, Chart, Form) -->
                <div class="dash-card span-6">
                    <div class="card-header-flex">
                        <h3 class="card-title">RECENT INCIDENT REPORTS</h3>
                        <a href="#" class="view-all">View all</a>
                    </div>
                    <div class="inner-box mt-1">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>TYPE</th>
                                        <th>LOCATION</th>
                                        <th>STATUS</th>
                                        <th>DATE</th>
                                    </tr>
                                </thead>
                                <tbody id="incidentTableBody">

                                    <?php if ($error_message): ?>

                                        <tr>
                                            <td colspan="5">
                                                Database Error:
                                                <?php echo htmlspecialchars($error_message); ?>
                                            </td>
                                        </tr>

                                    <?php elseif (count($reports) === 0): ?>

                                        <tr>
                                            <td colspan="5" class="no-data-msg">
                                                No reports found.
                                            </td>
                                        </tr>

                                    <?php else: ?>

                                        <?php foreach ($reports as $report): ?>

                                            <tr>
                                                <td><?php echo htmlspecialchars($report['id']); ?></td>

                                                <td>
                                                    <?php echo htmlspecialchars($report['type']); ?>
                                                </td>

                                                <td>
                                                    <?php echo htmlspecialchars($report['location']); ?>
                                                </td>

                                                <td>
                                                    Reported
                                                </td>

                                                <td>
                                                    <?php echo date(
                                                        'M d, Y',
                                                        strtotime($report['created_at'])
                                                    ); ?>
                                                </td>
                                            </tr>

                                        <?php endforeach; ?>

                                    <?php endif; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="dash-card span-3">
                    <h3 class="card-title mb-2">INCIDENT BY TYPE</h3>
                    <div class="chart-container">
                        <canvas id="donutChart"></canvas>
                    </div>
                    <div class="donut-legend">

                        <?php
                        $colors = [
                            '#d9534f',
                            '#f0ad4e',
                            '#5cb85c',
                            '#5bc0de',
                            '#9370DB',
                            '#FF7F50',
                            '#20B2AA',
                            '#708090'
                        ];

                        foreach ($incidentTypes as $index => $item):
                            ?>

                            <div class="legend-item">
                                <div class="dot" style="background-color: <?php echo $colors[$index % count($colors)]; ?>">
                                </div>
                                <?php echo htmlspecialchars($item['type']); ?>
                                (<?php echo $item['total']; ?>)
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>

                <div class="dash-card span-3">
                    <h3 class="card-title mb-3">CREATE NEW ALERT</h3>

                    <div class="form-scroll-wrapper">
                        <label class="form-label mt-0">ALERT TITLE</label>
                        <input type="text" class="form-control" placeholder="Enter alert title">

                        <label class="form-label">MESSAGE</label>
                        <textarea class="form-control" rows="4" placeholder="Type your message here..."
                            style="resize: none; min-height: 80px;"></textarea>

                        <label class="form-label">TARGET AREA</label>
                        <select class="form-select" style="color: rgba(255,255,255,0.5);">
                            <option style="color: #000;">Select area</option>
                        </select>

                        <button class="btn-send">SEND ALERT</button>
                    </div>
                </div>

                <!-- ROW 3 (Map, Reports Graph) -->
                <div class="dash-card span-6">
                    <div class="card-header-flex">
                        <h3 class="card-title">MAP MONITORING</h3>
                        <a href="#" class="view-all">View Full Map</a>
                    </div>
                    <!-- Map Container -->
                    <div id="monitoringMap"
                        style="border-radius: 8px; flex-grow: 1; min-height: 0; margin-top: 5px; z-index: 1;"></div>
                    <div class="donut-legend" style="justify-content: flex-start; margin-top: 10px;">
                        <div class="legend-item">
                            <div class="dot" style="background-color: var(--accent-red);"></div> Critical
                        </div>
                        <div class="legend-item">
                            <div class="dot" style="background-color: var(--accent-yellow);"></div> On-Going
                        </div>
                        <div class="legend-item">
                            <div class="dot" style="background-color: var(--accent-blue);"></div> Reported
                        </div>
                        <div class="legend-item">
                            <div class="dot" style="background-color: var(--accent-green);"></div> Resolved
                        </div>
                    </div>
                </div>

                <div class="dash-card span-6">
                    <h3 class="card-title mb-2">REPORTS OVER TIME</h3>
                    <div class="inner-box mt-1 p-2 border-opacity-25">
                        <div class="chart-container">
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>


    <!-- Leaflet JS for the Map -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const incidentData = <?php echo json_encode($incidentTypes); ?>;
    </script>
    <script>
        Chart.defaults.color = '#ffffff';
        Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";

// ----------------------------
// Donut Chart
// ----------------------------

const labels = incidentData.map(item => item.type);
const values = incidentData.map(item => Number(item.total));

const chartColors = [
    '#d9534f',
    '#f0ad4e',
    '#5cb85c',
    '#5bc0de',
    '#9370DB',
    '#FF7F50',
    '#20B2AA',
    '#708090'
];

const total = values.reduce((a,b)=>a+b,0);

const ctxDonut = document
.getElementById("donutChart")
.getContext("2d");

new Chart(ctxDonut,{
    type:"doughnut",

    data:{
        labels:labels,
        datasets:[{
            data:values,
            backgroundColor:chartColors.slice(0,labels.length),
            borderWidth:0
        }]
    },

    options:{
        responsive:true,
        maintainAspectRatio:false,
        cutout:"75%",
        plugins:{
            legend:{
                display:false
            }
        }
    },

    plugins:[{

        id:"textCenter",

        beforeDraw(chart){

            const {ctx,width,height}=chart;

            ctx.save();

            ctx.fillStyle="#fff";
            ctx.textAlign="center";

            ctx.font="bold 34px Arial";
            ctx.fillText(total,width/2,height/2);

            ctx.font="13px Arial";
            ctx.fillText("TOTAL",width/2,height/2+20);

            ctx.restore();

        }

    }]
});

        // Line Chart
        const ctxLine = document.getElementById('lineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['May 4', 'May 5', 'May 6', 'May 7', 'May 8', 'May 9', 'May 10'],
                datasets: [{
                    label: 'Reports',
                    data: [0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#ffffff',
                    borderWidth: 1,
                    pointBackgroundColor: '#ffffff',
                    pointRadius: 0,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                layout: { padding: { left: 5, right: 15, top: 10, bottom: 5 } },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20,
                        grid: { color: 'rgba(255,255,255,0.1)', drawBorder: false },
                        border: { display: false },
                        ticks: { font: { size: 10 }, padding: 8, maxTicksLimit: 5 }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        border: { display: true, color: 'rgba(255,255,255,0.3)' },
                        ticks: { display: false }
                    }
                }
            }
        });

        // Sidebar logic
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function (e) {
                if (this.parentElement.classList.contains('bottom-nav')) return;
                document.querySelectorAll('.nav-menu .nav-item').forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Map Integration (Leaflet.js)

        const monitoringMap = L.map('monitoringMap').setView([14.0940, 121.0197], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(monitoringMap);

        const colors = {
            critical: '#c92a2a',
            ongoing: '#f5b041',
            reported: '#3498db',
            resolved: '#2ecc71'
        };

        function createMarker(lat, lng, colorCode, popupText) {
            L.circleMarker([lat, lng], {
                radius: 8,
                fillColor: colorCode,
                color: '#ffffff',
                weight: 2,
                fillOpacity: 1
            }).addTo(monitoringMap).bindPopup(popupText);
        }

        createMarker(14.0940, 121.0197, colors.critical, "<b>Talisay Center</b><br>Critical Volcanic Tremor");
        createMarker(14.0850, 121.0300, colors.ongoing, "<b>Aya, Talisay</b><br>Road Blockage On-Going");
        createMarker(14.1000, 121.0000, colors.reported, "<b>Banga, Talisay</b><br>Ashfall Reported");
        createMarker(14.0900, 120.9800, colors.resolved, "<b>Sampaloc</b><br>Gas Emission Resolved");

        setTimeout(() => {
            monitoringMap.invalidateSize();
        }, 100);
    </script>
</body>

</html>