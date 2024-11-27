<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$db_name = 'paper'; 
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    echo json_encode(['message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? '';

$data = json_decode(file_get_contents("php://input"), true);

// Handle POST method to insert data
if ($method === "POST") {
    $conferenceName = $_POST['conferenceName'];
    $conferenceDate = $_POST['conferenceDate'];
    $collegeName = $_POST['collegeName'];
    $address = $_POST['address'];
    $image = null;

    // Handle file upload if exists
    if (!empty($_FILES['image']['name'])) {
        $image = "" . basename($_FILES["image"]["name"]);
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image)) {
            echo json_encode(["message" => "Failed to upload image"]);
            exit;
        }
    }

    // Insert data into the database
    $sql = "INSERT INTO earlier_conferences (conference_name, conference_date, college_name, address, image) 
            VALUES ('$conferenceName', '$conferenceDate', '$collegeName', '$address', '$image')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Conference details saved successfully!"]);
    } else {
        echo json_encode(["message" => "Error: " . $conn->error]);
    }
}

// Handle GET method to retrieve data
if ($method  === "GET") {
    $sql = "SELECT * FROM earlier_conferences ORDER BY id DESC LIMIT 2";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $conferences = [];
        while ($row = $result->fetch_assoc()) {
            $conferences[] = $row;
        }
        echo json_encode($conferences);
    } else {
        echo json_encode([]);
    }
}

// Close connection
$conn->close();
?>
