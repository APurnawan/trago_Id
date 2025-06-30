<?php
include __DIR__ . '/../includes/koneksi.php';

$result = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Users Table</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/employee_monitoring/assets/css/admin.css">
</head>

<body class="p-4 bg-light">

    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar_admin.php'; ?>

            <div class="col-md-10 p-4">
                <h2 class="mb-4">Employee Table</h2>

                <div class="text-left mb-3">
                    <a href="add_users.php" class="btn btn-primary">+ Add Users</a>
                </div>
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td><?= $row['password'] ?? '-' ?></td>
                                <td><?= $row['role'] ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
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

</body>

</html>