<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization');

// Database connection
$conn = new mysqli('localhost', 'root', '', 'paper');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $conn->query("SELECT id, section, name, position FROM organizing_committee");
    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[$row['section']][] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'position' => $row['position']
        ];
    }
    $output = [];
    foreach ($sections as $section => $members) {
        $output[] = ['title' => $section, 'members' => $members];
    }
    echo json_encode($output);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['section'], $data['name'], $data['position'])) {
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO organizing_committee (section, name, position) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $data['section'], $data['name'], $data['position']);
    $stmt->execute();
    echo json_encode(['message' => 'Member added successfully!']);
    exit;
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['id'], $data['name'], $data['position'])) {
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }
    $stmt = $conn->prepare("UPDATE organizing_committee SET name = ?, position = ? WHERE id = ?");
    $stmt->bind_param("ssi", $data['name'], $data['position'], $data['id']);
    $stmt->execute();
    echo json_encode(['message' => 'Member updated successfully!']);
    exit;
}

if ($method === 'DELETE') {
    if (!isset($_GET['id'])) {
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM organizing_committee WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo json_encode(['message' => 'Member deleted successfully!']);
    exit;
}

echo json_encode(['error' => 'Invalid request method']);
?>
