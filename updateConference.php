<?php
include 'cors.php';
include 'db_config.php';

// Handle OPTIONS request for CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Now handle GET and POST requests
$method = $_SERVER['REQUEST_METHOD'] ?? ''; // Safely check if 'REQUEST_METHOD' is set

if ($method === 'GET') {
    $query = "SELECT * FROM conferences ORDER BY id DESC LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(['message' => 'No data found.']);
    }
} elseif ($method === 'POST') {
    // Get the posted JSON data
    $input = json_decode(file_get_contents("php://input"), true);

    // Validate the received data
    if ($input && isset($input['conferenceTitle'], $input['conferenceSubtitle'], $input['conferenceDate'], $input['conferenceType'])) {
        $conferenceTitle = $conn->real_escape_string($input['conferenceTitle']);
        $conferenceSubtitle = $conn->real_escape_string($input['conferenceSubtitle']);
        $conferenceDate = $conn->real_escape_string($input['conferenceDate']);
        $conferenceType = $conn->real_escape_string($input['conferenceType']);

        $query = "INSERT INTO conferences (conferenceTitle, conferenceSubtitle, conferenceDate, conferenceType) 
                  VALUES ('$conferenceTitle', '$conferenceSubtitle', '$conferenceDate', '$conferenceType')";

        // Execute the query
        if ($conn->query($query) === TRUE) {
            echo json_encode(['message' => 'Data inserted successfully.']);
        } else {
            echo json_encode(['message' => 'Failed to insert data: ' . $conn->error]);
        }
    } else {
        echo json_encode(['message' => 'Invalid data received.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed.']);
}

$conn->close();
?>
