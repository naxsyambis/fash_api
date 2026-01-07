<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

// Ambil JSON dari Android
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if ($username == '' || $password == '') {
    response(false, "Data tidak lengkap");
    exit;
}

// Hash password
$hash = password_hash($password, PASSWORD_BCRYPT);

// Insert user
$stmt = $conn->prepare(
    "INSERT INTO users (username, password_hash) VALUES (?, ?)"
);
$stmt->bind_param("ss", $username, $hash);

if ($stmt->execute()) {
    response(true, "Registrasi berhasil");
} else {
    response(false, "Username sudah terdaftar");
}

?>