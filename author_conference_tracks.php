<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection details
$host = 'localhost';
$db_name = 'paper'; 
$username = 'root';
$password = '';

// Create a connection to the database
$conn = new mysqli($host, $username, $password, $db_name);

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode(['message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Handle OPTIONS request (pre-flight for CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Handle GET request (to fetch the fields)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM conference_tracks_fields ORDER BY created_at DESC LIMIT 4";
    $result = $conn->query($sql);

    $fields = [];

    if ($result->num_rows > 0) {
        // Fetch all the fields and add them to the $fields array
        while ($row = $result->fetch_assoc()) {
            $fields[] = $row;
        }
        echo json_encode($fields);
    } else {
        echo json_encode(["message" => "No fields found."]);
    }
}

// Handle POST request (to add new fields)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Ensure the 'fields' array is present
    if (isset($data['fields']) && is_array($data['fields'])) {
        $fields = $data['fields'];

        // Loop through the fields and insert each into the database
        foreach ($fields as $field_name) {
            // Escape special characters to prevent SQL injection
            $field_name = $conn->real_escape_string($field_name);

            // Insert field into the database
            $sql = "INSERT INTO conference_tracks_fields(field_name) VALUES ('$field_name')";
            if (!$conn->query($sql)) {
                echo json_encode(["error" => "Error inserting data: " . $conn->error]);
                exit;
            }
        }

        echo json_encode(["message" => "Fields added successfully!"]);
    } else {
        echo json_encode(["error" => "No fields provided."]);
    }
}

// Close the database connection
$conn->close();
?>
