<?php
header("Content-Type: application/json");
require "../config/database.php";

$data = [];
$result = $conn->query("SELECT * FROM categories");

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>