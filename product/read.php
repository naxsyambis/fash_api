<?php
header("Content-Type: application/json");
require "../config/database.php";

$query = "
    SELECT 
        p.product_id,
        p.product_name,
        p.image_url,
        c.category_name
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    WHERE p.is_active = 1
    ORDER BY p.product_id DESC
";

$result = $conn->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

?>