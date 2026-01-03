<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

$name     = $_POST['name'] ?? '';
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($name == '' || $email == '' || $password == '') {
    response(false, "Data tidak lengkap");
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare(
    "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
);
$stmt->bind_param("sss", $name, $email, $hash);

if ($stmt->execute()) {
    response(true, "Registrasi berhasil");
} else {
    response(false, "Email sudah terdaftar");
}

?>