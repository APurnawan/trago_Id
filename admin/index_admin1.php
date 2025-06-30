<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employee Field Monitoring - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/employee_monitoring/assets/css/style.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar_admin.php'; ?>

            <div class="col-md-10 p-4">
                <h2 class="mb-4">Employee Field Monitoring Dashboard</h2>

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
                        <!-- dst -->
                    </select>
                    <button class="btn btn-primary">Apply</button>
                </div>
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5>Employee Onsite</h5>
                                <h3>4</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-secondary">
                            <div class="card-body">
                                <h5>Employee Offsite</h5>
                                <h3>4</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-secondary">
                            <div class="card-body">
                                <h5>Employee Leave</h5>
                                <h3>4</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Projects -->
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
                                <tr>
                                    <td>1</td>
                                    <td>Survey Topografi</td>
                                    <td>Bandung</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Geo Marine</td>
                                    <td>Surabaya</td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Pagination khusus untuk Project -->
                        <nav>
                            <ul class="pagination">
                                <li class="page-item disabled"><a class="page-link" href="#">Prev</a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
                    </div>

                    <!-- Employee Status -->
                    <div class="col-md-6">
                        <h4>List of Employee Status</h4>
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Lisa</td>
                                    <td>Onsite</td>
                                </tr>
                                <tr>
                                    <td>Budi</td>
                                    <td>Offsite</td>
                                </tr>
                            </tbody>
                        </table>
                        <!--  Pagination khusus untuk Employee Status -->
                        <nav>
                            <ul class="pagination">
                                <li class="page-item disabled"><a class="page-link" href="#">Prev</a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>


                <!-- News Section -->
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
                        echo "<p>Gagal memuat RSS feed. Periksa allow_url_fopen di php.ini atau gunakan RSS lain.</p>";
                    }
                    ?>
                </div>



            </div>
        </div>
    </div>

</body>

</html>