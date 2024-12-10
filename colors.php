<?php
include 'cors.php';
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $color1 = $data['color1'];
    $color2 = $data['color2'];

    $stmt = $conn->prepare("INSERT INTO colors (color1, color2) VALUES (?, ?)");
    $stmt->bind_param("ss", $color1, $color2);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["message" => "Colors saved successfully"]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT color1, color2 FROM colors ORDER BY id DESC LIMIT 1");
    $colors = [];
    while ($row = $result->fetch_assoc()) {
        $colors[] = $row;
    }
    echo json_encode($colors);
}

$conn->close();
?>
