<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "paper";

$conn = new mysqli($host, $user, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Handle GET request to fetch all members
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM about_editorial_board";
    $result = $conn->query($query);

    if (!$result) {
        echo json_encode(["success" => false, "message" => "Error fetching members."]);
        exit;
    }

    $members = [];
    while ($row = $result->fetch_assoc()) {
        $members[] = $row;
    }

    echo json_encode(["success" => true, "data" => $members]);
    exit;
}

// Handle POST request to add a new member
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(["success" => false, "message" => "Invalid JSON."]);
        exit;
    }

    $name = $data['name'];
    $position = $data['position'];
    $institution = $data['institution'];
    $location = $data['location'];

    if (!$name || !$position || !$institution || !$location) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    $query = $conn->prepare("INSERT INTO about_editorial_board (name, position, institution, location) VALUES (?, ?, ?, ?)");
    $query->bind_param("ssss", $name, $position, $institution, $location);

    if ($query->execute()) {
        echo json_encode(["success" => true, "message" => "Member added successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error adding member."]);
    }
    exit;
}

// Handle PUT request to update an existing member
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(["success" => false, "message" => "Invalid JSON."]);
        exit;
    }

    $id = $data['id'];
    $name = $data['name'];
    $position = $data['position'];
    $institution = $data['institution'];
    $location = $data['location'];

    if (!$id || !$name || !$position || !$institution || !$location) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    $query = $conn->prepare("UPDATE about_editorial_board SET name=?, position=?, institution=?, location=? WHERE id=?");
    $query->bind_param("ssssi", $name, $position, $institution, $location, $id);

    if ($query->execute()) {
        echo json_encode(["success" => true, "message" => "Member updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error updating member."]);
    }
    exit;
}

// Handle DELETE request to delete a member
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_GET['id'])) {
        echo json_encode(["success" => false, "message" => "ID is required."]);
        exit;
    }

    $id = intval($_GET['id']); // Sanitize the ID

    $query = $conn->prepare("DELETE FROM about_editorial_board WHERE id=?");
    $query->bind_param("i", $id);

    if ($query->execute()) {
        echo json_encode(["success" => true, "message" => "Member deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error deleting member."]);
    }
    exit;
}


$conn->close();
