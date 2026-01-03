<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

$category_id = $_POST['category_id'] ?? '';

if ($category_id == '') {
    response(false, "ID kategori wajib dikirim");
    exit;
}

/* cek apakah kategori masih dipakai produk */
$checkProduct = $conn->prepare(
    "SELECT product_id FROM products WHERE category_id = ?"
);
$checkProduct->bind_param("i", $category_id);
$checkProduct->execute();
$checkProduct->store_result();

if ($checkProduct->num_rows > 0) {
    response(false, "Kategori tidak dapat dihapus karena masih digunakan produk");
    exit;
}

/* hapus kategori */
$stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);

if ($stmt->execute()) {
    response(true, "Kategori berhasil dihapus");
} else {
    response(false, "Gagal menghapus kategori");
}

?>