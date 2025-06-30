<?php
include __DIR__ . '/../includes/koneksi.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM projects WHERE id = $id");
$project = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_project = $_POST['no_project'];
    $description = $_POST['description_project'];
    $location = $_POST['location_project'];

    $stmt = $conn->prepare("UPDATE projects SET no_project=?, descripsi_project=?, location_project=? WHERE id=?");
    $stmt->bind_param("sssi", $no_project, $description, $location, $id);
    $stmt->execute();

    header("Location: project_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Project</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-5">
<div class="container">
    <h3 class="mb-4">Edit Project</h3>
    <form method="POST" class="bg-white p-4 border rounded shadow-sm">
        <div class="form-group">
            <label>No Project</label>
            <input type="text" name="no_project" class="form-control" value="<?= $project['no_project'] ?>" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description_project" class="form-control" required><?= $project['descripsi_project'] ?></textarea>
        </div>
        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location_project" class="form-control" value="<?= $project['location_project'] ?>" required>
        </div>
        <button class="btn btn-primary" type="submit">Update</button>
        <a href="project_list.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
