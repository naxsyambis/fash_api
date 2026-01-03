<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

$category_id   = $_POST['category_id'] ?? '';
$category_name = $_POST['category_name'] ?? '';

if ($category_id == '' || $category_name == '') {
    response(false, "ID kategori dan nama kategori wajib diisi");
    exit;
}

/* cek apakah kategori ada */
$check = $conn->prepare("SELECT category_id FROM categories WHERE category_id = ?");
$check->bind_param("i", $category_id);
$check->execute();
$check->store_result();

if ($check->num_rows == 0) {
    response(false, "Kategori tidak ditemukan");
    exit;
}

/* update kategori */
$stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
$stmt->bind_param("si", $category_name, $category_id);

if ($stmt->execute()) {
    response(true, "Kategori berhasil diperbarui");
} else {
    response(false, "Gagal memperbarui kategori");
}

?>