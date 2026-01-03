<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

$item_id = $_POST['item_id'] ?? '';
$qty     = $_POST['quantity'] ?? '';
$reason  = $_POST['reason'] ?? 'PURCHASE';
$note    = $_POST['note'] ?? null;

if ($item_id == '' || $qty == '' || $qty <= 0) {
    response(false, "Data stock out tidak valid");
    exit;
}

$conn->begin_transaction();

try {

    /* hitung stock */
    $queryStock = "
    SELECT IFNULL(SUM(
        CASE 
            WHEN movement_type='IN' THEN quantity
            WHEN movement_type='OUT' THEN -quantity
        END
    ),0) AS stock
    FROM stock_movements
    WHERE item_id = ?
    ";

    $stmtStock = $conn->prepare($queryStock);
    $stmtStock->bind_param("i", $item_id);
    $stmtStock->execute();
    $stock = $stmtStock->get_result()->fetch_assoc()['stock'];

    if ($qty > $stock) {
        throw new Exception("Stok tidak cukup");
    }

    /* insert stock OUT */
    $stmt = $conn->prepare("
        INSERT INTO stock_movements
        (item_id, movement_type, quantity, reason, note)
        VALUES (?, 'OUT', ?, ?, ?)
    ");
    $stmt->bind_param("iiss", $item_id, $qty, $reason, $note);
    $stmt->execute();

    $conn->commit();
    response(true, "Stock berhasil dikurangi");

} catch (Exception $e) {
    $conn->rollback();
    response(false, $e->getMessage());
}

?>