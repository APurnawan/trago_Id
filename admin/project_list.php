<?php
include __DIR__ . '/../includes/koneksi.php';

$result = $conn->query("SELECT * FROM projects");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Project Table</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/employee_monitoring/assets/css/admin.css">
</head>

<body class="p-4 bg-light">

  <div class="container-fluid">
    <div class="row">
      <?php include '../includes/sidebar_admin.php'; ?>

      <div class="col-md-10 p-4">
        <h2 class="mb-4">Project Table</h2>

        <div class="text-left mb-3">
          <a href="add_project.php" class="btn btn-primary">+ Add Project</a>
        </div>

        <table class="table table-bordered table-striped">
          <thead class="thead-dark">
            <tr>
              <th>ID</th>
              <th>No Project</th>
              <th>Description</th>
              <th>Location</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['no_project'] ?></td>
                <td><?= $row['descripsi_project'] ?></td>
                <td><?= $row['location_project'] ?></td>
                <td>
                  <a href="edit_project.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                  <a href="delete_project.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                    onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <div class="d-flex justify-content-between">
          <span>Showing 1-10 of total</span>
          <nav>
            <ul class="pagination pagination-sm">
              <li class="page-item disabled"><a class="page-link" href="#">Prev</a></li>
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>

</body>

</html>