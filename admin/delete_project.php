<?php
include __DIR__ . '/../includes/koneksi.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Hapus dari tabel projects
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: project_list.php?success=deleted");
    } else {
        echo "Gagal menghapus data.";
    }
} else {
    echo "ID tidak ditemukan.";
}
?>
