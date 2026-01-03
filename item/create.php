<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

$product_id = $_POST['product_id'] ?? '';
$size       = $_POST['size'] ?? '';
$color      = $_POST['color'] ?? '';
$price      = $_POST['price'] ?? '';

if ($product_id == '' || $size == '' || $color == '' || $price == '') {
    response(false, "Semua field item produk wajib diisi");
    exit;
}

/* cek produk */
$check = $conn->prepare("SELECT product_id FROM products WHERE product_id = ? AND is_active = 1");
$check->bind_param("i", $product_id);
$check->execute();
$check->store_result();

if ($check->num_rows == 0) {
    response(false, "Produk tidak ditemukan atau tidak aktif");
    exit;
}

/* insert item */
$stmt = $conn->prepare("
    INSERT INTO item_produk (product_id, size, color, price, is_active)
    VALUES (?, ?, ?, ?, 1)
");
$stmt->bind_param("issd", $product_id, $size, $color, $price);

if ($stmt->execute()) {
    response(true, "Item produk berhasil ditambahkan");
} else {
    response(false, "Gagal menambahkan item produk");
}

?>