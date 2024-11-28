<?php
include 'cors.php';
include 'db_config.php';

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Determine request method
$method = $_SERVER['REQUEST_METHOD'] ?? '';

if ($method === 'POST') {
    // Add a new Chief Patron
    $data = json_decode(file_get_contents("php://input"), true);

    $name = $data['name'] ?? null;
    $role = $data['role'] ?? null;
    $imageUrl = $data['imageUrl'] ?? null;

    if ($name && $role && $imageUrl) {
        $query = "INSERT INTO chief_patrons (name, role, image_url) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $name, $role, $imageUrl);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Chief Patron added successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to add Chief Patron"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Invalid input"]);
    }
} elseif ($method === 'GET') {
    // Fetch all Chief Patrons
    $query = "SELECT id, name, role, image_url FROM chief_patrons ORDER BY id DESC LIMIT 4";
    $result = $conn->query($query);

    $chiefPatrons = [];
    while ($row = $result->fetch_assoc()) {
        $chiefPatrons[] = $row;
    }

    echo json_encode($chiefPatrons);
} elseif ($method === 'DELETE') {
    // Delete a Chief Patron
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;

    if ($id) {
        $query = "DELETE FROM chief_patrons WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Chief Patron deleted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete Chief Patron"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Invalid ID"]);
    }
} else {
    echo json_encode(["error" => "Unsupported request method"]);
}

$conn->close();
