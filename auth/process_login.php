<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../includes/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        // Set session
        $_SESSION["user"] = [
            "id" => $user["id"],
            "email" => $user["email"],
            "role" => $user["role"],
            "employee_id" => $user["employee_id"]
        ];

        // Redirect berdasarkan role
        if ($user["role"] == "Manager") {
            header("Location: ../admin/index_admin.php");
        } elseif ($user["role"] == "Office Staff") {
            header("Location:../user/index_field.php");
        } elseif ($user["role"] == "Field Staff") {
            header("Location:../user/index_field.php");
        } else {
            header("Location:login.php?error=Role tidak dikenal.");
        }
        exit;
    } else {
        // Jika login gagal
        header("Location:login.php?error=Email atau password salah.");
        exit;
    }
}
