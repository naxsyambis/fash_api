<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

$name = $_POST['name'] ?? '';

if ($name == '') {
    response(false, "Nama kategori wajib diisi");
    exit;
}

$stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
$stmt->bind_param("s", $name);

if ($stmt->execute()) {
    response(true, "Kategori berhasil ditambahkan");
} else {
    response(false, "Gagal menambah kategori");
}
?>