<?php
include __DIR__ . '/../includes/koneksi.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM employees WHERE id = $id");
$employee = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['employee_code'];
    $name = $_POST['name'];
    $dob = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone = $_POST['phone_number'];
    $join = $_POST['join_date'];
    $photo = $employee['photo'];

    if ($_FILES['photo']['name']) {
        $photo = basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], "../assets/uploads/$photo");
    }

    $stmt = $conn->prepare("UPDATE employees SET employee_code=?, name=?, date_of_birth=?, gender=?, address=?, phone_number=?, join_date=?, photo=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $code, $name, $dob, $gender, $address, $phone, $join, $photo, $id);
    $stmt->execute();

    header("Location: employee_list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">Edit Employee</h2>
 <form method="POST" enctype="multipart/form-data" class="bg-white p-4 border rounded shadow-sm">
    <div class="form-group">
        <label>Employee Code</label>
        <input type="text" name="employee_code" class="form-control" value="<?= $employee['employee_code'] ?>" required>
    </div>
    <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="<?= $employee['name'] ?>" required>
    </div>
    <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="date_of_birth" class="form-control" value="<?= $employee['date_of_birth'] ?>">
    </div>
    <div class="form-group">
        <label>Gender</label>
        <select name="gender" class="form-control">
            <option value="Male" <?= $employee['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $employee['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
        </select>
    </div>
    <div class="form-group">
        <label>Address</label>
        <textarea name="address" class="form-control"><?= $employee['address'] ?></textarea>
    </div>
    <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="phone_number" class="form-control" value="<?= $employee['phone_number'] ?>">
    </div>
    <div class="form-group">
        <label>Join Date</label>
        <input type="date" name="join_date" class="form-control" value="<?= $employee['join_date'] ?>">
    </div>
    <div class="form-group">
        <label>Photo</label>
        <input type="file" name="photo" class="form-control-file">
        <?php if (!empty($employee['photo'])): ?>
            <br>
            <img src="../assets/uploads/<?= $employee['photo'] ?>" width="80">
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-success">Update</button>
    <a href="employee_list.php" class="btn btn-secondary">Cancel</a>
</form>

    </div>
</body>

</html>