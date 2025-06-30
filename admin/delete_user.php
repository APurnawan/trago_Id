<?php
include __DIR__ . '/../includes/koneksi.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $conn->query("DELETE FROM users WHERE id = $id");
}

header("Location: user_list.php");
exit;
