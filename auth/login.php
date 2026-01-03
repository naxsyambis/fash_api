<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";
require "../utils/jwt.php";

$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email == '' || $password == '') {
    response(false, "Email & password wajib diisi");
    exit;
}

$stmt = $conn->prepare(
    "SELECT user_id, name, password, role FROM users WHERE email = ?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    response(false, "Login gagal");
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    response(false, "Login gagal");
    exit;
}

/* generate JWT */
$token = generateJWT([
    "user_id" => $user['user_id'],
    "name"    => $user['name'],
    "role"    => $user['role']
]);

response(true, "Login berhasil", [
    "token" => $token,
    "user"  => [
        "name" => $user['name'],
        "role" => $user['role']
    ]
]);

?>