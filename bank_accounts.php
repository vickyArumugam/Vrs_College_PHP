<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost'; // Your database host
$dbname = 'paper'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Prepare SQL query to fetch bank accounts
        $stmt = $pdo->prepare("SELECT * FROM bank_accounts ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($accounts); // Return all accounts as JSON
    } catch (PDOException $e) {
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

        $stmt = $pdo->prepare("INSERT INTO bank_accounts (accountName, accountNumber, branch, ifscCode, micr) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['accountName'],
            $data['accountNumber'],
            $data['branch'],
            $data['ifscCode'],
            $data['micr']
        ]);

        echo json_encode(['message' => 'Account added successfully']);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to add account: ' . $e->getMessage()]);
    }
}
?>
