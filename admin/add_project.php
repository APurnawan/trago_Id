<?php
include __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_project = $_POST['no_project'];
    $description = $_POST['description_project'];
    $location = $_POST['location_project'];

    $stmt = $conn->prepare("INSERT INTO projects (no_project, descripsi_project, location_project) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $no_project, $description, $location);
    $stmt->execute();

    header("Location: project_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Project</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-5">
<div class="container">
    <h3 class="mb-4">Add Project</h3>
    <form method="POST" class="bg-white p-4 border rounded shadow-sm">
        <div class="form-group">
            <label>No Project</label>
            <input type="text" name="no_project" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description_project" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location_project" class="form-control" required>
        </div>
        <button class="btn btn-success" type="submit">Save</button>
        <a href="project_list.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
