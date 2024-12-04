<?php
include 'cors.php';
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required fields are set
    if (isset($_POST['conferenceName'], $_POST['conferenceDate'], $_POST['collegeName'], $_POST['address'], $_FILES['image'])) {
        $conferenceName = $_POST['conferenceName'];
        $conferenceDate = $_POST['conferenceDate'];
        $collegeName = $_POST['collegeName'];
        $address = $_POST['address'];
        
        // Handle the image file upload
        $image = $_FILES['image'];
        $imageTmpName = $image['tmp_name'];
        $imageName = $image['name'];
        $imageType = $image['type'];
        $imageSize = $image['size'];

        // Check if the image is valid
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($imageType, $allowedTypes)) {
            $imageData = file_get_contents($imageTmpName);  // Read the image data

            // Insert data into the database
            $stmt = $conn->prepare("INSERT INTO earlier_conferences (conference_name, conference_date, college_name, address, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $conferenceName, $conferenceDate, $collegeName, $address, $imageData);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Data saved successfully!"]);
            } else {
                echo json_encode(["success" => false, "message" => "Error saving data: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Invalid image type. Only JPG, PNG, and GIF are allowed."]);
        }
    } else {
        // Handle missing fields or image file
        echo json_encode(["success" => false, "message" => "Missing required fields or image"]);
    }

    $conn->close();
    exit;
}

if ($method === "GET") {
    $sql = "SELECT * FROM earlier_conferences ORDER BY id DESC LIMIT 2 ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $conferences = [];
        while ($row = $result->fetch_assoc()) {
            $row['image'] = base64_encode($row['image']); // Convert binary image data to base64 for display
            $conferences[] = $row;
        }
        echo json_encode($conferences);
    } else {
        echo json_encode([]);
    }
    exit;
}

// Close connection
$conn->close();
?>
