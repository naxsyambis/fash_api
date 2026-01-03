<?php
header("Content-Type: application/json");
require "../config/database.php";

$item_id = $_GET['item_id'] ?? '';

$query = "
SELECT
    IFNULL(SUM(
        CASE 
            WHEN movement_type = 'IN' THEN quantity
            WHEN movement_type = 'OUT' THEN -quantity
        END
    ),0) AS stock
FROM stock_movements
WHERE item_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_assoc());

?>