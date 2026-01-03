
<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

$product_name = $_POST['product_name'] ?? '';
$category_id  = $_POST['category_id'] ?? '';
$image_url    = $_POST['image_url'] ?? null;

if ($product_name == '' || $category_id == '') {
    response(false, "Nama produk dan kategori wajib diisi");
    exit;
}

/* cek kategori */
$check = $conn->prepare("SELECT category_id FROM categories WHERE category_id = ?");
$check->bind_param("i", $category_id);
$check->execute();
$check->store_result();

if ($check->num_rows == 0) {
    response(false, "Kategori tidak ditemukan");
    exit;
}

/* insert produk */
$stmt = $conn->prepare("
    INSERT INTO products (product_name, category_id, image_url, is_active)
    VALUES (?, ?, ?, 1)
");
$stmt->bind_param("sis", $product_name, $category_id, $image_url);

if ($stmt->execute()) {
    response(true, "Produk berhasil ditambahkan");
} else {
    response(false, "Gagal menambahkan produk");
}

?>