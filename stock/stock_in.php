<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

$item_id = $_POST['item_id'] ?? '';
$qty     = $_POST['quantity'] ?? '';
$reason  = $_POST['reason'] ?? 'PURCHASE';
$note    = $_POST['note'] ?? null;

if ($item_id == '' || $qty == '' || $qty <= 0) {
    response(false, "Data stock in tidak valid");
    exit;
}

$conn->begin_transaction();

try {

    /* cek item */
    $check = $conn->prepare(
        "SELECT item_id FROM item_produk WHERE item_id = ? AND is_active = 1"
    );
    $check->bind_param("i", $item_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        throw new Exception("Item tidak ditemukan");
    }

    /* insert stock IN */
    $stmt = $conn->prepare("
        INSERT INTO stock_movements
        (item_id, movement_type, quantity, reason, note)
        VALUES (?, 'IN', ?, ?, ?)
    ");
    $stmt->bind_param("iiss", $item_id, $qty, $reason, $note);
    $stmt->execute();

    $conn->commit();
    response(true, "Stock berhasil ditambahkan");

} catch (Exception $e) {
    $conn->rollback();
    response(false, $e->getMessage());
}

?>