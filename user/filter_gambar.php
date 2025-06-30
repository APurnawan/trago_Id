<?php
session_start();
include '../includes/koneksi.php';

$filtered_image_url = '';
$original_image_url = '';
$error_message = '';

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Field Staff', 'Office Staff'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Fungsi untuk menerapkan filter gambar
function apply_filter($image, $filter)
{
    if (!$image)
        return null;

    switch ($filter) {
        case 'grayscale':
            imagefilter($image, IMG_FILTER_GRAYSCALE);
            break;
        case 'sepia':
            imagefilter($image, IMG_FILTER_GRAYSCALE);
            imagefilter($image, IMG_FILTER_BRIGHTNESS, 10);
            imagefilter($image, IMG_FILTER_CONTRAST, -15);
            imagefilter($image, IMG_FILTER_COLORIZE, 90, 60, 40, 0);
            break;
        case 'negative':
            imagefilter($image, IMG_FILTER_NEGATE);
            break;
        case 'brightness':
            imagefilter($image, IMG_FILTER_BRIGHTNESS, 40);
            break;
        case 'contrast':
            imagefilter($image, IMG_FILTER_CONTRAST, -30);
            break;
    }
    return $image;
}

// ✅ Proses filter jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'apply_filter') {
    if (isset($_FILES['photo1']) && $_FILES['photo1']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['photo1']['tmp_name'];
        $file_type = mime_content_type($file_tmp);
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

        if (!in_array($file_type, $allowed_types)) {
            $error_message = "File harus berupa JPG, PNG, atau GIF.";
        } elseif (!extension_loaded('gd')) {
            $error_message = "Ekstensi GD tidak tersedia. Aktifkan GD di server.";
        } else {
            // Simpan gambar asli
            $original_name = 'original_' . uniqid() . '.png';
            $original_path = 'assets/uploads/' . $original_name;
            $full_original_path = '../' . $original_path;

            switch ($file_type) {
                case 'image/jpeg':
                    $src_image = imagecreatefromjpeg($file_tmp);
                    break;
                case 'image/png':
                    $src_image = imagecreatefrompng($file_tmp);
                    break;
                case 'image/gif':
                    $src_image = imagecreatefromgif($file_tmp);
                    break;
                default:
                    $src_image = null;
            }

            if ($src_image) {
                imagepng($src_image, $full_original_path);
                $original_image_url = $original_path;

                $filter = $_POST['filter'] ?? '';
                $dst_image = apply_filter($src_image, $filter);

                if ($dst_image) {
                    $filtered_name = 'filtered_' . uniqid() . '.png';
                    $filtered_path = 'assets/uploads/' . $filtered_name;
                    $full_filtered_path = '../' . $filtered_path;

                    if (imagepng($dst_image, $full_filtered_path)) {
                        $filtered_image_url = $filtered_path;
                    } else {
                        $error_message = "Gagal menyimpan gambar hasil filter.";
                    }

                    imagedestroy($dst_image);
                } else {
                    $error_message = "Filter gagal diterapkan.";
                }

                imagedestroy($src_image);
            } else {
                $error_message = "Gagal membaca gambar sumber.";
            }
        }
    } else {
        $error_message = "Silakan unggah gambar terlebih dahulu.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Filter Gambar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/user.css"> <!-- gunakan CSS terpisah -->
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <?php include '../includes/sidebar_field.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <h3 class="mb-4">Filter Gambar</h3>

            <form method="POST" enctype="multipart/form-data" class="mb-4">
                <input type="hidden" name="action" value="apply_filter">

                <div class="card p-4">
                    <h5 class="mb-4">Pilih Gambar dan Terapkan Filter</h5>
                    <div class="row">
                        <!-- Kiri -->
                        <div class="col-md-6">
                            <div class="border p-3 bg-white text-center" style="min-height: 300px;">
                                <label class="font-weight-bold d-block">Uploaded Image</label>
                                <img id="preview1"
                                    src="<?= $original_image_url ? "/employee_monitoring/$original_image_url" : 'https://via.placeholder.com/300' ?>"
                                    class="img-fluid border" style="max-height: 220px; object-fit: contain;">
                            </div>

                            <div class="form-group mt-3">
                                <label for="photo1">Pilih Gambar</label>
                                <input type="file" name="photo1" id="photo1" class="form-control-file" required>
                            </div>

                            <div class="form-group">
                                <label for="filter">Pilih Filter</label>
                                <select name="filter" class="form-control" id="filter">
                                    <option value="">-- None --</option>
                                    <option value="grayscale">Grayscale</option>
                                    <option value="sepia">Sepia</option>
                                    <option value="negative">Negative</option>
                                    <option value="brightness">Brightness</option>
                                    <option value="contrast">Contrast</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-info btn-block">Apply Filter</button>
                        </div>

                        <!-- Kanan -->
                        <div class="col-md-6">
                            <div class="border p-3 bg-white text-center" style="min-height: 300px;">
                                <label class="font-weight-bold d-block">Filtered Result</label>
                                <?php if (!empty($filtered_image_url)): ?>
                                    <img src="/employee_monitoring/<?= $filtered_image_url ?>" class="img-fluid border"
                                        style="max-height: 220px; object-fit: contain;">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/300" class="img-fluid border"
                                        style="max-height: 220px; object-fit: contain;">
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($error_message)): ?>
                                <div class="alert alert-danger mt-3"><?= $error_message ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Gambar -->
    <script>
        document.getElementById("photo1").addEventListener("change", function () {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("preview1").src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        });
    </script>
</body>


</html>