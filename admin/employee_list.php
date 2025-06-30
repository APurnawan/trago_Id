<?php
include __DIR__ . '/../includes/koneksi.php';

$result = $conn->query("SELECT * FROM employees");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employee Table</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/employee_monitoring/assets/css/admin.css">
</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar_admin.php'; ?>

            <div class="col-md-10 p-4">
                <h2 class="mb-4">Employee Table</h2>

                <div class="text-left mb-3">
                    <a href="add_employee.php" class="btn btn-primary">+ Add Employee</a>
                </div>
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Address</th>
                            <th>Phone Number</th>
                            <th>Join Date</th>
                            <th>Photo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['employee_code'] ?></td>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['email'] ?? '-' ?></td>
                                <td><?= $row['gender'] ?></td>
                                <td><?= $row['address'] ?></td>
                                <td><?= $row['phone_number'] ?></td>
                                <td><?= $row['join_date'] ?></td>
                                <td>
                                    <?php if ($row['photo']): ?>
                                        <img src="../assets/uploads/<?= $row['photo'] ?>" width="50" />
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_employee.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="delete_employee.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
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