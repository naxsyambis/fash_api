<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

$product_id   = $_POST['product_id'] ?? '';
$product_name = $_POST['product_name'] ?? '';
$category_id  = $_POST['category_id'] ?? '';
$image_url    = $_POST['image_url'] ?? null;

if ($product_id == '' || $product_name == '' || $category_id == '') {
    response(false, "Semua field wajib diisi");
    exit;
}

/* cek produk */
$check = $conn->prepare("SELECT product_id FROM products WHERE product_id = ?");
$check->bind_param("i", $product_id);
$check->execute();
$check->store_result();

if ($check->num_rows == 0) {
    response(false, "Produk tidak ditemukan");
    exit;
}

/* update */
$stmt = $conn->prepare("
    UPDATE products 
    SET product_name = ?, category_id = ?, image_url = ?
    WHERE product_id = ?
");
$stmt->bind_param("sisi", $product_name, $category_id, $image_url, $product_id);

if ($stmt->execute()) {
    response(true, "Produk berhasil diperbarui");
} else {
    response(false, "Gagal memperbarui produk");
}

?>