<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

// Ambil JSON dari Android
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if ($username == '' || $password == '') {
    response(false, "Username dan password wajib diisi");
    exit;
}

// Ambil user berdasarkan username
$stmt = $conn->prepare(
    "SELECT user_id, username, password_hash FROM users WHERE username = ?"
);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    response(false, "Login gagal");
    exit;
}

$user = $result->fetch_assoc();

// Verifikasi password
if (!password_verify($password, $user['password_hash'])) {
    response(false, "Login gagal");
    exit;
}

// Login sukses
response(true, "Login berhasil", [
    "user_id" => $user['user_id'],
    "username" => $user['username']
]);

?>