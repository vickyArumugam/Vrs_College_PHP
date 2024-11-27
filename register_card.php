<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$host = 'localhost';
$db_name = 'paper'; 
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    echo json_encode(['message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Determine request method
$method = $_SERVER['REQUEST_METHOD'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request: Add a new card
    $input = json_decode(file_get_contents("php://input"), true);

    $category = $conn->real_escape_string($input['category']);
    $currency = $conn->real_escape_string($input['currency']);
    $value = $conn->real_escape_string($input['value']);

    $sql = "INSERT INTO register_cards (category, currency, value) VALUES ('$category', '$currency', '$value')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Card added successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Failed to add card"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request: Fetch all cards
    $sql = "SELECT id, category, currency, value FROM register_cards ORDER BY id DESC LIMIT 4";
    $result = $conn->query($sql);

    $cards = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cards[] = $row;
        }
    }

    echo json_encode($cards);
} else {
    // Invalid request method
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
}

$conn->close();
