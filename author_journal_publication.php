<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set CORS headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Database credentials
$host = 'localhost';
$db_name = 'paper';
$username = 'root';
$password = '';

// Establish database connection
$conn = new mysqli($host, $username, $password, $db_name);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Handle OPTIONS request for CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Determine the request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Handle POST request
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['title']) && is_array($data['descriptions']) && count($data['descriptions']) > 0) {
        $title = $conn->real_escape_string($data['title']);
        $descriptions = array_map([$conn, 'real_escape_string'], $data['descriptions']);

        $errors = [];
        foreach ($descriptions as $description) {
            // Use prepared statement for secure query
            $stmt = $conn->prepare("INSERT INTO author_journals_publication (title, publication_description) VALUES (?, ?)");
            $stmt->bind_param("ss", $title, $description);

            if (!$stmt->execute()) {
                $errors[] = $stmt->error;
            }
            $stmt->close();
        }

        if (empty($errors)) {
            echo json_encode(['message' => 'Data submitted successfully!']);
        } else {
            echo json_encode(['error' => 'Database error: ' . implode(', ', $errors)]);
        }
    } else {
        echo json_encode(['error' => 'Both title and at least one description are required.']);
    }
} elseif ($method === 'GET') {
    // Handle GET request
    $sql = "SELECT title, publication_description FROM author_journals_publication";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $journals = [];
        while ($row = $result->fetch_assoc()) {
            $journals[] = [
                'title' => $row['title'],
                'description' => $row['publication_description']
            ];
        }
        echo json_encode($journals);
    } else {
        echo json_encode(['message' => 'No journals found.']);
    }
} else {
    // Handle unsupported methods
    echo json_encode(['error' => 'Unsupported request method.']);
}

$conn->close();
?>
