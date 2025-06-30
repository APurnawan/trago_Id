<?php
session_start();
include '../includes/koneksi.php';

//  Cek apakah user sudah login dan berperan sebagai Manager
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Manager') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data dari session
$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

//  Simpan komentar (reply atau komentar utama)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $log_id = $_POST['log_id'];
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

// Simpan command (perintah khusus)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_command'])) {
    $command = $_POST['command'];
    $log_id = $_POST['log_id'];

    $stmt = $conn->prepare("UPDATE field_logs SET command=? WHERE id=?");
    $stmt->bind_param("si", $command, $log_id);
    $stmt->execute();

    header("Location: view_log.php?id=$log_id");
    exit;
}

//  Ambil ID log dari URL
$log_id = $_GET['id'] ?? null;
if (!$log_id) {
    echo "No log ID provided.";
    exit;
}

//  Ambil detail log
$stmt = $conn->prepare("
    SELECT f.*, e.name AS employee_name, p.no_project
    FROM field_logs f
    JOIN employees e ON e.id = f.employee_id
    LEFT JOIN projects p ON f.project_id = p.id
    WHERE f.id = ?
");
$stmt->bind_param("i", $log_id);
$stmt->execute();
$log = $stmt->get_result()->fetch_assoc();

if (!$log) {
    echo "Log not found.";
    exit;
}

//  Ambil komentar
$stmt = $conn->prepare("SELECT c.*, u.email FROM comments c JOIN users u ON c.user_id = u.id WHERE c.log_id = ? ORDER BY c.created_at ASC");
$stmt->bind_param("i", $log_id);
$stmt->execute();
$comments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Log - <?= htmlspecialchars($log['employee_name']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/employee_monitoring/assets/css/admin.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar_admin.php'; ?>
            <div class="container py-5">
                <h3>Log Detail - <?= htmlspecialchars($log['employee_name']) ?></h3>
                <p><strong>Date:</strong> <?= $log['log_date'] ?> | <strong>Time:</strong> <?= $log['log_time'] ?></p>
                <p><strong>Status:</strong> <?= $log['status'] ?? '-' ?> | <strong>Project:</strong>
                    <?= $log['no_project'] ?? '-' ?></p>
                <p><strong>Latitude:</strong> <?= $log['latitude'] ?> | <strong>Longitude:</strong>
                    <?= $log['longitude'] ?></p>

                <div class="row">
                    <!-- MAP -->
                    <div class="col-md-6">
                        <h5>Map</h5>
                        <div style="border:1px solid #ccc; height: 250px; overflow: hidden;">
                            <iframe
                                src="https://maps.google.com/maps?q=<?= $log['latitude'] ?>,<?= $log['longitude'] ?>&z=15&output=embed"
                                width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen>
                            </iframe>
                        </div>
                    </div>

                    <!-- Photo Preview -->
                    <div class="col-md-6">
                        <h5>Photo Preview</h5>
                        <div class="row">
                            <?php
                            $photos = explode(',', $log['photo_url']);
                            if (!empty($photos[0])) {
                                echo '<div class="col-6 mb-2"><img src="/employee_monitoring/' . $photos[0] . '" class="img-fluid rounded border"></div>';
                            }
                            if (!empty($photos[1])) {
                                echo '<div class="col-6 mb-2"><img src="/employee_monitoring/' . $photos[1] . '" class="img-fluid rounded border"></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="form-group mt-4">
                    <label><strong>Activity Notes:</strong></label>
                    <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($log['notes']) ?></textarea>
                </div>

                <div class="mt-4">
                    <h5>Discussion</h5>
                    <?php
                    function showComments($conn, $log_id, $parent_id = null, $indent = 0)
                    {
                        $query = "SELECT * FROM comments WHERE log_id = ? AND parent_comment_id ";
                        $query .= is_null($parent_id) ? "IS NULL" : "= ?";
                        $query .= " ORDER BY created_at ASC";

                        $stmt = $conn->prepare($query);
                        if (is_null($parent_id)) {
                            $stmt->bind_param("i", $log_id);
                        } else {
                            $stmt->bind_param("ii", $log_id, $parent_id);
                        }
                        $stmt->execute();
                        $result = $stmt->get_result();

                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='mb-2' style='margin-left: {$indent}px'>";
                            echo "<strong>{$row['role']}</strong> <small class='text-muted'>{$row['created_at']}</small>";
                            echo "<p class='mb-1'>" . nl2br(htmlspecialchars($row['content'])) . "</p>";

                            // Balas komentar
                            echo "<form method='POST' class='mb-2'>";
                            echo "<input type='hidden' name='log_id' value='{$log_id}'>";
                            echo "<input type='hidden' name='parent_comment_id' value='{$row['id']}'>";
                            echo "<textarea name='content' rows='2' class='form-control mb-1' placeholder='Reply...'></textarea>";
                            echo "<button class='btn btn-sm btn-secondary' type='submit'>Reply</button>";
                            echo "</form>";

                            echo "<hr>";

                            // Panggil rekursif untuk reply-nya
                            showComments($conn, $log_id, $row['id'], $indent + 30);
                            echo "</div>";
                        }
                    }

                    showComments($conn, $log['id']);
                    ?>

                    <!-- Tambah Komentar Baru -->
                    <h6 class="mt-4">Add New Comment</h6>
                    <form method="POST">
                        <input type="hidden" name="log_id" value="<?= $log['id'] ?>">
                        <textarea name="content" rows="3" class="form-control" placeholder="Write your comment here..."
                            required></textarea>
                        <button class="btn btn-primary mt-2" type="submit">Send</button>
                    </form>
                </div>

                <a href="index_admin.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
            </div>
</body>

</html>