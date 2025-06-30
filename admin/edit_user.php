<?php
include __DIR__ . '/../includes/koneksi.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE id = $id");
$user = $result->fetch_assoc();

// Ambil daftar employee dari tabel employees
$employees = $conn->query("SELECT id, name FROM employees");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $role = $_POST['role'];
    $employee_id = $_POST['employee_id'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET email=?, password=?, role=?, employee_id=? WHERE id=?");
        $stmt->bind_param("sssii", $email, $password, $role, $employee_id, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET email=?, role=?, employee_id=? WHERE id=?");
        $stmt->bind_param("ssii", $email, $role, $employee_id, $id);
    }

    $stmt->execute();
    header("Location: user_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-5">
    <div class="container">
        <h3 class="mb-4">Edit User</h3>
        <form method="POST" class="bg-white p-4 border rounded shadow-sm">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= $user['email'] ?>" required class="form-control">
            </div>
            <div class="form-group">
                <label>New Password (leave blank if not changing)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="Manager" <?= $user['role'] == 'Manager' ? 'selected' : '' ?>>Manager</option>
                    <option value="Office Staff" <?= $user['role'] == 'Office Staff' ? 'selected' : '' ?>>Office Staff</option>
                    <option value="Field Staff" <?= $user['role'] == 'Field Staff' ? 'selected' : '' ?>>Field Staff</option>
                </select>
            </div>
            <div class="form-group">
                <label>Assign to Employee</label>
                <select name="employee_id" class="form-control">
                    <?php while ($emp = $employees->fetch_assoc()): ?>
                        <option value="<?= $emp['id'] ?>" <?= $emp['id'] == $user['employee_id'] ? 'selected' : '' ?>>
                            <?= $emp['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button class="btn btn-success" type="submit">Update</button>
            <a href="user_list.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
