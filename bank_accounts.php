<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization');

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = 'localhost';
$db_name = 'paper'; 
$username = 'root';
$password = '';

// Create connection using MySQLi
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Prepare SQL query to fetch bank accounts
        $query = "SELECT * FROM bank_accounts ORDER BY id DESC LIMIT 1";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $accounts = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($accounts); // Return all accounts as JSON
        } else {
            echo json_encode(['message' => 'No accounts found']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to fetch accounts: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if all required fields are present
    if (empty($data['accountName']) || empty($data['accountNumber']) || empty($data['branch']) || empty($data['ifscCode']) || empty($data['micr'])) {
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }

    try {
        // Prepare SQL query to insert data into bank_accounts
        $query = "INSERT INTO bank_accounts (accountName, accountNumber, branch, ifscCode, micr) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $data['accountName'], $data['accountNumber'], $data['branch'], $data['ifscCode'], $data['micr']);
        $stmt->execute();

        echo json_encode(['message' => 'Account added successfully']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to add account: ' . $e->getMessage()]);
    }
}
?>
