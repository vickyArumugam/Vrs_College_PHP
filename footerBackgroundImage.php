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

        // Validate file size (optional)
        if ($file['size'] > 5 * 1024 * 1024) { // 5 MB max size
            echo json_encode(['message' => 'File size exceeds the limit of 5 MB.']);
            exit;
        }

        // Get the binary content of the uploaded file
        $backgroundImage = file_get_contents($file['tmp_name']);

        // Check if backgroundImage contains data
        if (empty($backgroundImage)) {
            echo json_encode(['message' => 'Image data is empty.']);
            exit;
        }

        // Debug: Check the size of the image data
        error_log('Image data size: ' . strlen($backgroundImage));

        // Prepare the SQL query
        $stmt = $conn->prepare("INSERT INTO about_footer (background_image) VALUES (?)");

        // Check for SQL errors
        if (!$stmt) {
            echo json_encode(['message' => 'Error preparing the SQL statement: ' . $conn->error]);
            exit;
        }

        // Bind the parameters (binary data)
        $stmt->bind_param("s", $backgroundImage);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Image uploaded successfully']);
        } else {
            echo json_encode(['message' => 'Error uploading image', 'error' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['message' => 'No file uploaded']);
    }
}
// GET method: Retrieve the latest background image
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT background_image FROM about_footer ORDER BY id DESC LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $backgroundImage = base64_encode($row['background_image']);
        echo json_encode(['backgroundImage' => $backgroundImage]);
    } else {
        echo json_encode(['message' => 'No image found']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}

$conn->close();
