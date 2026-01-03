<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

$product_id = $_POST['product_id'] ?? '';

if ($product_id == '') {
    response(false, "ID produk wajib dikirim");
    exit;
}

/* cek item produk */
$checkItem = $conn->prepare(
    "SELECT item_id FROM item_produk WHERE product_id = ?"
);
$checkItem->bind_param("i", $product_id);
$checkItem->execute();
$checkItem->store_result();

if ($checkItem->num_rows > 0) {
    response(false, "Produk tidak dapat dihapus karena memiliki item");
    exit;
}

/* soft delete */
$stmt = $conn->prepare("
    UPDATE products SET is_active = 0 WHERE product_id = ?
");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    response(true, "Produk berhasil dihapus");
} else {
    response(false, "Gagal menghapus produk");
}

?>