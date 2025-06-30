<?php
include __DIR__ . '/../includes/koneksi.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Ambil nama file foto
    $result = $conn->query("SELECT photo FROM employees WHERE id = $id");
    $employee = $result->fetch_assoc();

    if ($employee) {
        // Hapus file foto jika ada
        if (!empty($employee['photo'])) {
            $photo_path = "../assets/uploads/" . $employee['photo'];
            if (file_exists($photo_path)) {
                unlink($photo_path);
            }
        }

        // Hapus data dari database
        $conn->query("DELETE FROM employees WHERE id = $id");
    }
}

// Redirect kembali ke daftar employee
header("Location: employee_list.php");
exit;
?>
