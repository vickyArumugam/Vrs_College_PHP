<?php
include 'cors.php';
include 'db_config.php';


$method = $_SERVER['REQUEST_METHOD'];

// Handle GET request
if ($method === 'GET') {
   // Check if the query parameters `date` and `created_at` are provided
   $date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : null;
   $created_at = isset($_GET['created_at']) ? $conn->real_escape_string($_GET['created_at']) : null;

   if ($date && $created_at) {
       // Query to fetch data where `date` and `created_at` match
       $sql = "SELECT * FROM key_dates WHERE date = '$date' AND created_at = '$created_at'";
   } else {
       // If no filter provided, return all data
       $sql ="SELECT * FROM key_dates ORDER BY created_at DESC LIMIT 4";
   }

   $result = $conn->query($sql);

   if ($result->num_rows > 0) {
       $rows = [];
       while ($row = $result->fetch_assoc()) {
           $rows[] = $row;
       }
       echo json_encode($rows);
   } else {
       echo json_encode(["message" => "No data found."]);
   }
   $conn->close();
   exit;
}

// Handle POST request
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data)) {
        echo json_encode(["error" => "No data received."]);
        http_response_code(400);
        exit;
    }

    foreach ($data as $card) {
        $description = $conn->real_escape_string($card['description']);
        $date = $conn->real_escape_string($card['date']);

        $sql = "INSERT INTO key_dates (description, date) VALUES ('$description', '$date')";

        if (!$conn->query($sql)) {
            echo json_encode(["error" => "Failed to insert data: " . $conn->error]);
            http_response_code(500);
            exit();
        }
    }

    echo json_encode(["success" => "Data inserted successfully!"]);
    $conn->close();
    http_response_code(200);
    exit;
}

// Handle invalid methods
http_response_code(405);
echo json_encode(["error" => "Method not allowed."]);
?>
