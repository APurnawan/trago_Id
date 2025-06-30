<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Field Staff') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM field_logs WHERE id = $id");
$log = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $log_date = $_POST['log_date'];
    $log_time = $_POST['log_time'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $notes = $_POST['notes'];
    $project_id = $_POST['project_id'];

    // Update photo if uploaded
    $photo_url = $log['photo_url'];
    if (!empty($_FILES['photo1']['name'])) {
        $upload_dir = '../assets/uploads/';
        $photo_name = time() . '_' . basename($_FILES['photo1']['name']);
        $target_path = $upload_dir . $photo_name;
        if (move_uploaded_file($_FILES['photo1']['tmp_name'], $target_path)) {
            $photo_url = 'assets/uploads/' . $photo_name;
        }
    }

    $stmt = $conn->prepare("UPDATE field_logs SET log_date=?, log_time=?, latitude=?, longitude=?, photo_url=?, notes=?, project_id=? WHERE id=?");
    $stmt->bind_param("ssddssii", $log_date, $log_time, $latitude, $longitude, $photo_url, $notes, $project_id, $id);
    $stmt->execute();

    header("Location: my_activity.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Activity</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>

<body class="p-4">
    <div class="container">
        <h3>Edit Activity</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="log_date" class="form-control" value="<?= $log['log_date'] ?>" required>
            </div>
            <div class="form-group">
                <label>Time</label>
                <input type="time" name="log_time" class="form-control" value="<?= $log['log_time'] ?>" required>
            </div>
            <div class="form-group">
                <label>Project</label>
                <select name="project_id" class="form-control" required>
                    <option value="">-- Select Project --</option>
                    <?php
                    $projects = $conn->query("SELECT id, no_project FROM projects");
                    while ($proj = $projects->fetch_assoc()) {
                        $selected = $proj['id'] == $log['project_id'] ? 'selected' : '';
                        echo "<option value='{$proj['id']}' $selected>{$proj['no_project']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Latitude</label>
                <input type="text" name="latitude" class="form-control" value="<?= $log['latitude'] ?>" required>
            </div>
            <div class="form-group">
                <label>Longitude</label>
                <input type="text" name="longitude" class="form-control" value="<?= $log['longitude'] ?>" required>
            </div>
            <div class="form-group">
                <label>Upload Photo (leave blank if not changing)</label>
                <input type="file" name="photo1" class="form-control-file">
                <br>
                <img src="/employee_monitoring/<?= $log['photo_url'] ?>" width="200">
            </div>
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control"><?= $log['notes'] ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="my_activity.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>

</html>