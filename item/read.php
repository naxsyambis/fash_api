<?php
header("Content-Type: application/json");
require "../config/database.php";

$product_id = $_GET['product_id'] ?? '';

$query = "
    SELECT 
        i.item_id,
        i.size,
        i.color,
        i.price,
        p.product_name
    FROM item_produk i
    JOIN products p ON i.product_id = p.product_id
    WHERE i.product_id = ?
        AND i.is_active = 1
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

?>