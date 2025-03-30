<?php
header('Content-Type: application/json');

// Get the JSON data from the POST request
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Invalid data received."]);
    exit;
}

$table = $data['table'];
$orders = $data['orders'];

// Database connection credentials
$servername = "localhost";
$username = "pinkfloyd";
$password = "";
$dbname = "floyd";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Example: Insert each ordered item into a table called "orders"
// The "orders" table might have columns: id (auto-increment), table_number, item_name, quantity, price, order_date (timestamp)
$stmt = $conn->prepare("INSERT INTO floyd (table, order, quantity) VALUES (?, ?, ?)");
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Prepare failed: " . $conn->error]);
    exit;
}

foreach ($orders as $order) {
    $item_name = $order['name'];
    $quantity = $order['quantity'];
    // Bind parameters (assuming table_number and item_name as strings, quantity and price as integers)
    $stmt->bind_param("ssii", $table, $item_name, $quantity);
    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "error" => "Execute failed: " . $stmt->error]);
        exit;
    }
}

$stmt->close();
$conn->close();

echo json_encode(["success" => true]);
?>
