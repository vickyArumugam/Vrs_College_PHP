<?php
include 'cors.php';
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? '';

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['description'])) {
        $description = $conn->real_escape_string($data['description']);
        $sql = "INSERT INTO about_scope_conference ( description) VALUES ( '$description')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Data submitted successfully!"]);
        } else {
            echo json_encode(["error" => "Error: " . $conn->error]);
        }
    } else {
        echo json_encode(["error" => "All fields are required."]);
    }
} else {
    $sql = "SELECT * FROM about_scope_conference ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    $conferences = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $conferences[] = $row;
        }
    }
    echo json_encode($conferences);
}

$conn->close();
