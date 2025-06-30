<?php
include __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['employee_code'];
    $name = $_POST['name'];
    $dob = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone = $_POST['phone_number'];
    $join = $_POST['join_date'];
    $photo = '';

    if (!empty($_FILES['photo']['name'])) {
        $photo = basename($_FILES['photo']['name']);
        echo "Upload ke: " . realpath("../assets/uploads/$photo");
        if (move_uploaded_file($_FILES['photo']['tmp_name'], "../assets/uploads/$photo")) {
            echo "Upload sukses ke ../assets/uploads/$photo<br>";
        } else {
            echo "Upload gagal!<br>";
            var_dump(error_get_last());
        }
    }

    $stmt = $conn->prepare("INSERT INTO employees (employee_code, name, date_of_birth, gender, address, phone_number, join_date, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $code, $name, $dob, $gender, $address, $phone, $join, $photo);
    $stmt->execute();

    header("Location: employee_list.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $log_id = $_POST['log_id'];
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    $content = trim($_POST['content']);
    $parent_comment_id = !empty($_POST['parent_comment_id']) ? $_POST['parent_comment_id'] : null;

    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO comments (log_id, user_id, role, parent_comment_id, content) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisis", $log_id, $user_id, $role, $parent_comment_id, $content);
        $stmt->execute();
    }

    header("Location: view_log.php?id=$log_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Employee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/employee_monitoring/assets/css/style.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Add Employee</h2>

        <form method="POST" enctype="multipart/form-data" class="bg-white p-4 border rounded shadow-sm">
            <div class="form-group">
                <label>Employee Code</label>
                <input type="text" name="employee_code" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control">
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" class="form-control">
            </div>
            <div class="form-group">
                <label>Join Date</label>
                <input type="date" name="join_date" class="form-control">
            </div>
            <div class="form-group">
                <label>Photo</label>
                <input type="file" name="photo" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-success">Save</button>
            <a href="employee_list.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>

</html>