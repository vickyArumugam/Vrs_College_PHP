<?php
include 'cors.php';
include 'db_config.php';

// Handle POST request (update footer content)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST['content'];

    $sql = "UPDATE Copy_rights SET content = '$content' WHERE id = 1";
    if ($conn->query($sql) === TRUE) {
        echo "Footer content updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle GET request (fetch footer content)
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $result = $conn->query("SELECT content FROM Copy_rights WHERE id = 1"); // Adjust query as needed

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['copyRight' => $row['content']]);
    } else {
        echo json_encode(['copyRight' => '© 2024 Your Organization. All rights reserved.']); // Default text
    }
    
}
$conn->close();
?>
