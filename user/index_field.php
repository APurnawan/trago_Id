<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Field Staff', 'Office Staff'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data user dari session
$user_id = $_SESSION['user']['id'];
$employee_id = $_SESSION['user']['employee_id'];

// Hitung status
$count = $conn->query("
    SELECT status, COUNT(*) as total 
    FROM field_logs 
    WHERE employee_id = $employee_id 
    GROUP BY status
");
$status_counts = ['Onsite' => 0, 'Offsite' => 0, 'Leave' => 0];
while ($row = $count->fetch_assoc()) {
    $status_counts[$row['status']] = $row['total'];
}

// Ambil semua proyek
$project_query = $conn->query("SELECT * FROM projects");

// Ambil aktivitas terakhir
$activity_query = $conn->query("
    SELECT f.log_date, p.no_project, f.notes 
    FROM field_logs f 
    LEFT JOIN projects p ON f.project_id = p.id 
    WHERE f.employee_id = $employee_id 
    ORDER BY f.log_date DESC 
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Field Staff Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/employee_monitoring/assets/css/user.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <?php include '../includes/sidebar_field.php'; ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <h4>Welcome, <?= $_SESSION['user']['email'] ?></h4>

                <!-- Filter Tahun dan Bulan -->
                <div class="d-flex align-items-center mb-4">
                    <label class="mr-2">Filter by:</label>
                    <select class="form-control mr-2" style="width: 150px;">
                        <option>2025</option>
                        <option>2024</option>
                        <option>2023</option>
                    </select>
                    <select class="form-control mr-2" style="width: 150px;">
                        <option>All Months</option>
                        <option>January</option>
                        <option>February</option>
                        <option>March</option>
                    </select>
                    <button class="btn btn-primary">Apply</button>
                </div>

                <!-- Summary Box -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 border-right">
                                <h5 class="text-muted">Total Onsite</h5>
                                <div class="display-4 text-success font-weight-bold"><?= $status_counts['Onsite'] ?>
                                </div>
                            </div>
                            <div class="col-md-4 border-right">
                                <h5 class="text-muted">Total Offsite</h5>
                                <div class="display-4 text-info font-weight-bold"><?= $status_counts['Offsite'] ?></div>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-muted">Total Leave</h5>
                                <div class="display-4 text-warning font-weight-bold"><?= $status_counts['Leave'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project & Activity Table -->
                <div class="row">
                    <div class="col-md-6">
                        <h4>List of Projects</h4>
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Project Name</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($p = $project_query->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($p['no_project']) ?></td>
                                        <td><?= htmlspecialchars($p['location_project']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h4>List of Activity</h4>
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Date</th>
                                    <th>Project</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($act = $activity_query->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($act['log_date'])) ?></td>
                                        <td><?= htmlspecialchars($act['no_project'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($act['notes']) ?></td>
                                        <td><a href="my_activity.php" class="btn btn-sm btn-info">View</a></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- News -->
                <div class="mt-4">
                    <h4>News: Forbes Trending</h4>
                    <?php
                    $rss = new DOMDocument();
                    libxml_use_internal_errors(true);
                    $rssUrl = "https://rss.nytimes.com/services/xml/rss/nyt/Technology.xml";

                    if (@$rss->load($rssUrl)) {
                        $items = $rss->getElementsByTagName('item');
                        $limit = 5;
                        for ($i = 0; $i < $limit && $i < $items->length; $i++) {
                            $item = $items->item($i);
                            $title = $item->getElementsByTagName('title')->item(0)->nodeValue;
                            $link = $item->getElementsByTagName('link')->item(0)->nodeValue;
                            $date = $item->getElementsByTagName('pubDate')->item(0)->nodeValue;

                            echo "<p><strong><a href=\"$link\" target=\"_blank\">$title</a></strong><br>";
                            echo "<small><em>Posted on " . date("l, F d, Y", strtotime($date)) . "</em></small></p><hr>";
                        }
                    } else {
                        echo "<p>Gagal memuat RSS feed. Coba RSS lainnya atau cek koneksi internet.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>