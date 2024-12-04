<?php
include 'cors.php';
include 'db_config.php';

// Handle POST request (insert or update data)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $platform = $_POST['platform'];
    $link_url = $_POST['link_url'];

    // Insert or update based on platform
    $sql = "INSERT INTO social_media (platform, link_url) VALUES ('$platform', '$link_url')
            ON DUPLICATE KEY UPDATE link_url = '$link_url'";
    if ($conn->query($sql) === TRUE) {
        echo "Social media link updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle GET request (fetch data)
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "
        SELECT t1.id, t1.platform, t1.link_url
        FROM social_media t1
        INNER JOIN (
            SELECT platform, MAX(id) AS max_id
            FROM social_media
            GROUP BY platform
        ) t2
        ON t1.platform = t2.platform AND t1.id = t2.max_id
    ";
    $result = $conn->query($sql);

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
}

$conn->close();
?>
