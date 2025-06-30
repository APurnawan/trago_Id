<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Field Staff', 'Office Staff'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data user dari session
$user = $_SESSION['user'];
$user_id = $user['id'];
$employee_id = $user['employee_id'];
$role = $user['role'];

//  Handle kirim komentar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $log_id = $_POST['log_id'];
    $content = trim($_POST['comment_content']);
    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO comments (log_id, user_id, role, content) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $log_id, $user_id, $role, $content);
        $stmt->execute();
    }
    header("Location: my_activity.php");
    exit;
}

//  Handle tambah log baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] === 'save_log') {
    $log_date = $_POST['log_date'];
    $log_time = $_POST['log_time'];
    $status = $_POST['status'];
    $project_id = $_POST['project_id'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $notes = $_POST['notes'];
    $photo_paths = [];

    $upload_dir = '../assets/uploads/';
    foreach (['photo1', 'photo2'] as $photo_input) {
        if (!empty($_FILES[$photo_input]['name'])) {
            $photo_name = time() . '_' . basename($_FILES[$photo_input]['name']);
            $target_path = $upload_dir . $photo_name;
            if (move_uploaded_file($_FILES[$photo_input]['tmp_name'], $target_path)) {
                $photo_paths[] = 'assets/uploads/' . $photo_name;
            }
        }
    }

    $photo_url = implode(',', $photo_paths);

    $stmt = $conn->prepare("INSERT INTO field_logs 
        (employee_id, status, project_id, log_date, log_time, latitude, longitude, photo_url, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "isissddss",
        $employee_id,
        $status,
        $project_id,
        $log_date,
        $log_time,
        $latitude,
        $longitude,
        $photo_url,
        $notes
    );
    $stmt->execute();

    header("Location: my_activity.php");
    exit;
}

//  Ambil data log untuk user ini
$logs = $conn->query("SELECT * FROM field_logs WHERE employee_id = $employee_id ORDER BY log_date DESC, log_time DESC");

//  Fungsi ambil komentar
function getCommentsByLog($conn, $log_id)
{
    $stmt = $conn->prepare("SELECT c.*, u.email FROM comments c JOIN users u ON c.user_id = u.id WHERE log_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("i", $log_id);
    $stmt->execute();
    return $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Activity</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/employee_monitoring/assets/css/user.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 p-0">
                <?php include '../includes/sidebar_field.php'; ?>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 bg-light p-4">
                <h3 class="mb-4">My Activity Log</h3>

                <form method="POST" enctype="multipart/form-data" class="mb-5">
                    <input type="hidden" name="action" value="save_log">

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Date</label>
                            <input type="date" name="log_date" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Time</label>
                            <input type="time" name="log_time" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Project</label>
                            <select name="project_id" class="form-control" required>
                                <option value="">-- Select Project --</option>
                                <?php
                                $projects = $conn->query("SELECT id, no_project, descripsi_project FROM projects");
                                while ($proj = $projects->fetch_assoc()) {
                                    echo "<option value='{$proj['id']}'>{$proj['no_project']} - {$proj['descripsi_project']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="">-- Select Status --</option>
                                <option value="Onsite">Onsite</option>
                                <option value="Offsite">Offsite</option>
                                <option value="Leave">Leave</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- MAP -->
                        <div class="col-md-6">
                            <h5>Map</h5>
                            <div style="height: 300px;">
                                <iframe id="map-frame"
                                    src="https://maps.google.com/maps?q=-6.200,106.816&z=15&output=embed"
                                    style="width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
                            </div>
                        </div>

                        <!-- Photo Preview -->
                        <div class="col-md-6">
                            <h5>Photo Preview</h5>
                            <div class="photo-preview" style="height: 300px;">
                                <img id="preview1" src="https://via.placeholder.com/300" class="img-fluid w-100 h-100"
                                    style="object-fit: cover; border: 1px solid #ccc;">
                            </div>
                        </div>
                    </div>

                    <!-- Upload & Location -->
                    <div class="form-row mt-4">
                        <div class="form-group col-md-6">
                            <label>Upload Photo 1</label>
                            <input type="file" name="photo1" id="photo1" class="form-control-file" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Latitude</label>
                            <input type="text" name="latitude" id="latitude" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Longitude</label>
                            <input type="text" name="longitude" id="longitude" class="form-control" readonly>
                        </div>
                    </div>
                    <!-- Notes -->
                    <div class="form-group mt-3">
                        <label>Activity Notes</label>
                        <textarea name="notes" rows="3" class="form-control"
                            placeholder="Describe today's activity..."></textarea>
                    </div>

                    <!-- Command dari Manager -->
                    <div class="form-group">
                        <label>Manager Command</label>
                        <textarea class="form-control" name="command" rows="2"
                            placeholder="(Visible if any from manager)" readonly></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Save Activity</button>
                </form>

                <!-- History Table -->
                <h4>Activity History</h4>
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <thead class="thead-dark">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Lat</th>
                                <th>Long</th>
                                <th>Photos</th>
                                <th>Notes</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </thead>
                    <tbody>
                        <?php while ($log = $logs->fetch_assoc()): ?>
                            <tr>
                                <td><?= $log['log_date'] ?></td>
                                <td><?= $log['log_time'] ?></td>
                                <td><?= $log['latitude'] ?></td>
                                <td><?= $log['longitude'] ?></td>
                                <td>
                                    <?php
                                    $photos = explode(',', $log['photo_url']);
                                    foreach ($photos as $photo) {
                                        if (!empty($photo)) {
                                            echo "<img src='/employee_monitoring/$photo' width='60' class='mr-1 mb-1'>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?= nl2br(htmlspecialchars($log['notes'])) ?></td>
                                <td>
                                    <a href="edit_myactivity.php?id=<?= $log['id'] ?>"
                                        class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete_myactivity.php?id=<?= $log['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus data ini?')">Delete</a>
                                </td>
                            </tr>
                            <!-- Komentar dan Balasan -->
                            <tr>
                                <td colspan="7">
                                    <strong>Comments:</strong><br>
                                    <?php
                                    $comments = getCommentsByLog($conn, $log['id']);
                                    while ($c = $comments->fetch_assoc()) {
                                        $align = $c['role'] == 'Manager' ? 'text-left' : 'text-right';
                                        $badge = $c['role'] == 'Manager' ? 'badge-primary' : 'badge-secondary';

                                        echo "<div class='border p-2 mb-2 $align'>";
                                        echo "<span class='badge $badge'>{$c['role']}</span> ";
                                        echo "<small class='text-muted'>{$c['created_at']}</small><br>";
                                        echo nl2br(htmlspecialchars($c['content']));
                                        echo "</div>";
                                    }
                                    ?>

                                    <!-- Form Balas -->
                                    <form method="POST" class="mt-2">
                                        <input type="hidden" name="log_id" value="<?= $log['id'] ?>">
                                        <textarea name="comment_content" rows="2" class="form-control mb-1"
                                            placeholder="Write a reply..."></textarea>
                                        <button name="submit_comment" class="btn btn-sm btn-success">Reply</button>
                                    </form>

                                <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- JS Preview -->
    <script>
        const preview = (inputId, imgId) => {
            document.getElementById(inputId).addEventListener('change', function (e) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById(imgId).src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            });
        };
        preview("photo1", "preview1");

        document.addEventListener("DOMContentLoaded", function () {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    document.getElementById("latitude").value = position.coords.latitude;
                    document.getElementById("longitude").value = position.coords.longitude;

                    // Update map
                    document.getElementById("map-frame").src =
                        `https://maps.google.com/maps?q=${position.coords.latitude},${position.coords.longitude}&z=15&output=embed`;
                }, function (error) {
                    alert("Gagal mendapatkan lokasi: " + error.message);
                });
            } else {
                alert("Browser tidak mendukung geolocation.");
            }
        });


    </script>
</body>

</html>