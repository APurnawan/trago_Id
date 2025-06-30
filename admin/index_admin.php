<?php
session_start();
include __DIR__ . '/../includes/koneksi.php';

$tahun = $_GET['tahun'] ?? date('Y');
$bulan = $_GET['bulan'] ?? '';
$search = $_GET['search'] ?? '';
$limit = 5;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;
$page_proj = $_GET['page_proj'] ?? 1;
$offset_proj = ($page_proj - 1) * $limit;

$filter_sql = "WHERE YEAR(log_date) = '$tahun'";
if (!empty($bulan)) {
    $filter_sql .= " AND MONTH(log_date) = '$bulan'";
}

if (!empty($search)) {
    $search_escaped = $conn->real_escape_string($search);
    $filter_sql .= " AND (
        e.name LIKE '%$search_escaped%' OR
        p.no_project LIKE '%$search_escaped%' OR
        f.status LIKE '%$search_escaped%'
    )";
}

$onsite = $conn->query("SELECT COUNT(*) as total FROM field_logs WHERE status='Onsite'")->fetch_assoc()['total'];
$offsite = $conn->query("SELECT COUNT(*) as total FROM field_logs WHERE status='Offsite'")->fetch_assoc()['total'];
$leave = $conn->query("SELECT COUNT(*) as total FROM field_logs WHERE status='Leave'")->fetch_assoc()['total'];

$total_projects = $conn->query("SELECT COUNT(*) AS total FROM projects")->fetch_assoc()['total'];
$total_pages_projects = ceil($total_projects / $limit);
$projects = $conn->query("SELECT no_project, location_project FROM projects ORDER BY id DESC LIMIT $limit OFFSET $offset_proj");

$total_logs_query = "SELECT COUNT(*) AS total FROM field_logs f 
    JOIN employees e ON e.id = f.employee_id
    LEFT JOIN projects p ON p.id = f.project_id
    $filter_sql";
$total_logs = $conn->query($total_logs_query)->fetch_assoc()['total'];
$total_pages_logs = ceil($total_logs / $limit);

$employee_status = $conn->query("
    SELECT 
        e.name, 
        f.status, 
        f.project_id, 
        f.notes, 
        f.log_date,            
        f.id as log_id, 
        p.no_project 
    FROM field_logs f
    JOIN employees e ON e.id = f.employee_id
    LEFT JOIN projects p ON p.id = f.project_id
    $filter_sql
    ORDER BY f.log_date DESC, f.log_time DESC
    LIMIT $limit OFFSET $offset
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employee Field Monitoring - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/employee_monitoring/assets/css/admin.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row no-gutters">
            <div class="col-md-2 sidebar">
                <?php include '../includes/sidebar_admin.php'; ?>
            </div>
            <div class="col-md-10">
                <div class="content-wrapper">
                    <h2 class="dashboard-title mb-4">Employee Field Monitoring Dashboard</h2>

                    <form method="GET" class="form-inline mb-4">
                        <label class="mr-2">Filter by:</label>
                        <select name="tahun" class="form-control mr-2">
                            <?php for ($y = 2023; $y <= date('Y'); $y++): ?>
                                <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="bulan" class="form-control mr-2">
                            <option value="">All Months</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $bulan == $m ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </form>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5>Employee Onsite</h5>
                                    <h3><?= $onsite ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-secondary">
                                <div class="card-body">
                                    <h5>Employee Offsite</h5>
                                    <h3><?= $offsite ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-white bg-warning">
                                <div class="card-body">
                                    <h5>Employee Leave</h5>
                                    <h3><?= $leave ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h4>List of Projects</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Project Name</th>
                                            <th>Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = $offset_proj + 1;
                                        while ($row = $projects->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td><?= $row['no_project'] ?></td>
                                                <td><?= $row['location_project'] ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php include 'pagination_projects.php'; ?>
                        </div>

                        <div class="col-md-6">
                            <h4>List of Employee Status</h4>
                            <?php if (!empty($search)): ?>
                                <div class="alert alert-info">Menampilkan hasil pencarian untuk:
                                    <strong><?= htmlspecialchars($search) ?></strong>
                                </div>
                            <?php endif; ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Job Number</th>
                                            <th>Note</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $employee_status->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['log_date']) ?></td>
                                                <td><?= htmlspecialchars($row['name']) ?></td>
                                                <td><?= htmlspecialchars($row['status']) ?></td>
                                                <td><?= htmlspecialchars($row['no_project'] ?? '-') ?></td>
                                                <td><?= nl2br(htmlspecialchars($row['notes'] ?? '-')) ?></td>
                                                <td><a href="../admin/view_log.php?id=<?= $row['log_id'] ?>"
                                                        class="btn btn-sm btn-info">View Log</a></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php include 'pagination_logs.php'; ?>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
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
        </div>
    </div>
</body>

</html>