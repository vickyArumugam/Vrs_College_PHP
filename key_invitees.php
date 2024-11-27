<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database connection
$host = 'localhost';
$db_name = 'paper'; // Replace with your database name
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Handle POST request to store data
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data)) {
        echo json_encode(['error' => 'No data provided']);
        http_response_code(400);
        exit;
    }

    foreach ($data as $card) {
        $name = $conn->real_escape_string($card['name']);
        $imageUrl = $conn->real_escape_string($card['imageUrl']);
        $title = $conn->real_escape_string($card['title']);

        $sql = "INSERT INTO key_invitees (name, image_url, title) VALUES ('$name', '$imageUrl', '$title')";

        if (!$conn->query($sql)) {
            echo json_encode(['error' => 'Failed to insert data: ' . $conn->error]);
            http_response_code(500);
            exit;
        }
    }

    echo json_encode(['success' => 'Data inserted successfully']);
    http_response_code(200);

} elseif ($method === 'GET') {
    // Handle GET request to retrieve data
    $result = $conn->query("SELECT * FROM key_invitees ORDER BY id DESC LIMIT 1");

    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
        http_response_code(200);
    } else {
        echo json_encode(['message' => 'No data found']);
        http_response_code(404);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
    http_response_code(405);
}

$conn->close();
