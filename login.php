<?php
include 'cors.php';


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
