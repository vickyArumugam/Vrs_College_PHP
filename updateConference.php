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
            if ($row['backgroundImage']) {
                $row['backgroundImage'] = base64_encode($row['backgroundImage']); // Encode binary data
            }
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
    if (isset($_FILES['backgroundImage']) && $_FILES['backgroundImage']['error'] === UPLOAD_ERR_OK) {
        // Read the file content as binary
        $backgroundImage = file_get_contents($_FILES['backgroundImage']['tmp_name']);
    } else {
        $backgroundImage = null; // No image provided
    }

    $conferenceTitle = $conn->real_escape_string($_POST['conferenceTitle']);
    $conferenceSubtitle = $conn->real_escape_string($_POST['conferenceSubtitle']);
    $conferenceDate = $conn->real_escape_string($_POST['conferenceDate']);
    $conferenceType = $conn->real_escape_string($_POST['conferenceType']);

    // Use a prepared statement to securely insert binary data
    $stmt = $conn->prepare("INSERT INTO conferences (conferenceTitle, conferenceSubtitle, conferenceDate, conferenceType, backgroundImage) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $conferenceTitle, $conferenceSubtitle, $conferenceDate, $conferenceType, $backgroundImage);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Data inserted successfully.']);
    } else {
        echo json_encode(['message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['message' => 'Invalid request.']);
}


$conn->close();
?>
