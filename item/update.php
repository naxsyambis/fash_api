<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

$item_id = $_POST['item_id'] ?? '';
$size    = $_POST['size'] ?? '';
$color   = $_POST['color'] ?? '';
$price   = $_POST['price'] ?? '';

if ($item_id == '' || $size == '' || $color == '' || $price == '') {
    response(false, "Semua field wajib diisi");
    exit;
}

/* cek item */
$check = $conn->prepare("SELECT item_id FROM item_produk WHERE item_id = ? AND is_active = 1");
$check->bind_param("i", $item_id);
$check->execute();
$check->store_result();

if ($check->num_rows == 0) {
    response(false, "Item produk tidak ditemukan");
    exit;
}

/* update item */
$stmt = $conn->prepare("
    UPDATE item_produk
    SET size = ?, color = ?, price = ?
    WHERE item_id = ?
");
$stmt->bind_param("ssdi", $size, $color, $price, $item_id);

if ($stmt->execute()) {
    response(true, "Item produk berhasil diperbarui");
} else {
    response(false, "Gagal memperbarui item produk");
}

?>