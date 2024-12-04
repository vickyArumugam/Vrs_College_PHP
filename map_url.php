<?php
include 'cors.php';
include 'db_config.php';

// Handle POST request (insert or update data)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $iframe_url = $_POST['iframe_url'];
    

    $sql = "INSERT INTO map_data (iframe_url) VALUES ('$iframe_url')";
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle GET request (fetch data)
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT * FROM map_data ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode($data);
    } else {
        echo json_encode(["message" => "No data found"]);
    }
}

$conn->close();
?>
