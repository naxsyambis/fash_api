<?php
header("Content-Type: application/json");
require "../config/database.php";
require "../utils/response.php";

/*
PARAM OPTIONAL:
- date_from (YYYY-MM-DD)
- date_to   (YYYY-MM-DD)
- item_id
- movement_type (IN / OUT)
*/

$date_from     = $_GET['date_from'] ?? null;
$date_to       = $_GET['date_to'] ?? null;
$item_id       = $_GET['item_id'] ?? null;
$movement_type = $_GET['movement_type'] ?? null;

$query = "
SELECT
    sm.stock_id,
    p.product_name,
    ip.item_id,
    ip.size,
    ip.color,
    sm.movement_type,
    sm.quantity,
    sm.reason,
    sm.note,
    sm.created_at
FROM stock_movements sm
JOIN item_produk ip ON sm.item_id = ip.item_id
JOIN products p ON ip.product_id = p.product_id
WHERE 1=1
";

$params = [];
$types  = "";

/* FILTER ITEM */
if ($item_id) {
    $query .= " AND sm.item_id = ?";
    $types .= "i";
    $params[] = $item_id;
}

/* FILTER TYPE */
if ($movement_type) {
    $query .= " AND sm.movement_type = ?";
    $types .= "s";
    $params[] = $movement_type;
}

/* FILTER DATE */
if ($date_from && $date_to) {
    $query .= " AND DATE(sm.created_at) BETWEEN ? AND ?";
    $types .= "ss";
    $params[] = $date_from;
    $params[] = $date_to;
}

$query .= " ORDER BY sm.created_at DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

response(true, "Data stock movement", $data);

?>