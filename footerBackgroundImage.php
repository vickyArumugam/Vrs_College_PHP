<?php
include 'cors.php';
include 'db_config.php';


// Handle OPTIONS request for CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// POST method: Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['backgroundImage'])) {
        $file = $_FILES['backgroundImage'];

        // Validate file type (JPEG, PNG, and GIF are allowed)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['message' => 'Invalid file type. Only JPEG, PNG, and GIF are allowed.']);
            exit;
        }

        // Validate file size (20 MB max size)
        if ($file['size'] > 15 * 1024 * 1024) { // 20 MB max size
            echo json_encode(['message' => 'File size exceeds the limit of 15 MB.']);
            exit;
        }

        // Get the binary content of the uploaded file
        $backgroundImage = file_get_contents($file['tmp_name']);

        if (!$backgroundImage) {
            echo json_encode(['message' => 'Image data is empty.']);
            exit;
        }

        // Prepare the SQL query
        $stmt = $conn->prepare("INSERT INTO about_footer (background_image) VALUES (?)");
        if (!$stmt) {
            echo json_encode(['message' => 'Error preparing the SQL statement: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param("s", $backgroundImage);

        if ($stmt->execute()) {
            echo json_encode(['message' => 'Image uploaded successfully']);
            exit;
        } else {
            echo json_encode(['message' => 'Error uploading image', 'error' => $stmt->error]);
            exit;
        }

        $stmt->close();
    } else {
        echo json_encode(['message' => 'No file uploaded']);
        exit;
    }
}

// GET method: Retrieve the latest background image
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT background_image FROM about_footer ORDER BY id DESC LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $backgroundImage = base64_encode($row['background_image']);
        echo json_encode(['backgroundImage' => $backgroundImage]);
        exit;
    } else {
        echo json_encode(['message' => 'No image found']);
        exit;
    }
}

// If no valid method matches
http_response_code(405);
echo json_encode(['message' => 'Method not allowed']);
exit;

$conn->close();
