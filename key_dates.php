<?php
include 'cors.php';
include 'db_config.php';

// Handle GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM author_key_dates";
    $result = $conn->query($sql);
    $dates = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dates[] = $row;
        }
    }
    echo json_encode($dates);
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $description = $data['description'];
    $date = $data['date'];

    $sql = "INSERT INTO author_key_dates (description, date) VALUES ('$description', '$date')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "New key date added successfully"]);
    } else {
        echo json_encode(["message" => "Error: " . $sql . "<br>" . $conn->error]);
    }
}

$conn->close();
?>
