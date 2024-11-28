<?php
include 'cors.php';
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request: Insert data
    $data = json_decode(file_get_contents("php://input"), true);

    $collegeName = $data['collegeName'];
    $isoNumber = $data['isoNumber'];
    $village = $data['village'];
    $district = $data['district'];
    $state = $data['state'];     // New field
    $country = $data['country']; // New field
    $mobile = $data['mobile'];
    $email = $data['email'];
  
    $sql = "INSERT INTO contact_info (college_name, iso_number, village, district, state, country, mobile, email) 
            VALUES ('$collegeName', '$isoNumber', '$village', '$district', '$state', '$country', '$mobile', '$email')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Form submitted successfully!"]);
    } else {
        echo json_encode(["message" => "Error: " . $sql . "<br>" . $conn->error]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request: Retrieve data
    $sql = "SELECT * FROM contact_info ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(["message" => "No records found"]);
    }
} else {
    echo json_encode(["message" => "Invalid Request Method"]);
}

$conn->close();
?>
