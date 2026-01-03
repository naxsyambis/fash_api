<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

$item_id = $_POST['item_id'] ?? '';

if ($item_id == '') {
    response(false, "ID item produk wajib dikirim");
    exit;
}

/* cek transaksi stock */
$checkStock = $conn->prepare(
    "SELECT stock_id FROM stock_movements WHERE item_id = ?"
);
$checkStock->bind_param("i", $item_id);
$checkStock->execute();
$checkStock->store_result();

if ($checkStock->num_rows > 0) {
    response(false, "Item tidak dapat dihapus karena memiliki riwayat stok");
    exit;
}

/* soft delete */
$stmt = $conn->prepare("
    UPDATE item_produk SET is_active = 0 WHERE item_id = ?
");
$stmt->bind_param("i", $item_id);

if ($stmt->execute()) {
    response(true, "Item produk berhasil dihapus");
} else {
    response(false, "Gagal menghapus item produk");
}

?>