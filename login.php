<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

$validEmail = 'admin';
$validPassword = 'admin';

if ($email === $validEmail && $password === $validPassword) {
    echo json_encode(['message' => 'Login successful', 'user' => $email]);
    http_response_code(200);
} else {
    echo json_encode(['message' => 'Invalid email or password.']);
    http_response_code(401);
}
?>
