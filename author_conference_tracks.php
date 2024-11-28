<?php
include 'cors.php';
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM conference_tracks_fields ORDER BY created_at DESC LIMIT 4";
    $result = $conn->query($sql);

    $fields = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $fields[] = $row;
        }
        echo json_encode($fields);
    } else {
        echo json_encode(["message" => "No fields found."]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['fields']) && is_array($data['fields'])) {
        $fields = $data['fields'];
        foreach ($fields as $field_name) {

            $field_name = $conn->real_escape_string($field_name);

            $sql = "INSERT INTO conference_tracks_fields(field_name) VALUES ('$field_name')";
            if (!$conn->query($sql)) {
                echo json_encode(["error" => "Error inserting data: " . $conn->error]);
                exit;
            }
        }

        echo json_encode(["message" => "Fields added successfully!"]);
    } else {
        echo json_encode(["error" => "No fields provided."]);
    }
}

// Close the database connection
$conn->close();
?>
