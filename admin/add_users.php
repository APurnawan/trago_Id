<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
include __DIR__ . '/../includes/koneksi.php';

// Ambil daftar employee
$employees = $conn->query("SELECT id, name FROM employees");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password
    $role = $_POST['role'];
    $employee_id = $_POST['employee_id'];

    $stmt = $conn->prepare("INSERT INTO users (email, password, role, employee_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $email, $password, $role, $employee_id);
    $stmt->execute();

    header("Location: user_list.php"); // ganti sesuai nama list-nya
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-5">
    <div class="container">
        <h3 class="mb-4">Add New User</h3>
        <form method="POST" class="bg-white p-4 border rounded shadow-sm">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required class="form-control">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required class="form-control">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="Manager">Manager</option>
                    <option value="Office Staff">Office Staff</option>
                    <option value="Field Staff">Field Staff</option>
                </select>
            </div>
            <div class="form-group">
                <label>Assign to Employee</label>
                <select name="employee_id" class="form-control">
                    <?php while ($emp = $employees->fetch_assoc()): ?>
                        <option value="<?= $emp['id'] ?>"><?= $emp['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button class="btn btn-success" type="submit">Save</button>
            <a href="user_list.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
