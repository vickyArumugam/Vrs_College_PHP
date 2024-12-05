<?php
include 'cors.php';
include 'db_config.php';

$method = $_SERVER['REQUEST_METHOD'] ?? '';
$data = json_decode(file_get_contents("php://input"), true);

if ($method === 'POST') {
    // Check if file and fields are set
    if (empty($_FILES['image']['tmp_name']) || empty($_POST['name']) || empty($_POST['role'])) {
        echo json_encode(['error' => 'Missing required fields.']);
        http_response_code(400);
        exit;
    }

    $name = $conn->real_escape_string($_POST['name']);
    $role = $conn->real_escape_string($_POST['role']);

    // Validate and handle image upload
    if (is_uploaded_file($_FILES['image']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        $imageDataEscaped = $conn->real_escape_string($imageData);
    } else {
        echo json_encode(['error' => 'Invalid image upload.']);
        http_response_code(400);
        exit;
    }

    // Insert data into the database
    $sql = "INSERT INTO chief_patrons (name, role, image_url) VALUES ('$name', '$role', '$imageDataEscaped')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => 'Data inserted successfully.']);
        http_response_code(200);
    } else {
        echo json_encode(['error' => 'Database error: ' . $conn->error]);
        http_response_code(500);
    }
} elseif ($method === 'GET') {
    // Fetch data from the database
    $result = $conn->query("SELECT id, name, role, TO_BASE64(image_url) as image_url FROM chief_patrons ORDER BY id DESC LIMIT 4");

    if ($result && $result->num_rows > 0) {
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
