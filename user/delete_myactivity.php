<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Field Staff') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'];

// Optional: Hapus file foto dari server
$result = $conn->query("SELECT photo_url FROM field_logs WHERE id = $id");
$log = $result->fetch_assoc();
if ($log && !empty($log['photo_url'])) {
    $photos = explode(',', $log['photo_url']);
    foreach ($photos as $photo) {
        $filepath = "../" . $photo;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}

// Hapus log
$conn->query("DELETE FROM field_logs WHERE id = $id");

header("Location: my_activity.php");
exit;
?>