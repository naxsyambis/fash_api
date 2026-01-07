<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "fash_inventory"; // 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}
?>
