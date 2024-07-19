<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents('php://input'), true);

error_log('Received data: ' . print_r($data, true)); // Log data yang diterima

if (!isset($data['user_id']) || !isset($data['rating']) || !isset($data['feedback'])) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit();
}

$user_id = $data['user_id'];
$rating = $data['rating'];
$feedback = $data['feedback'];
$created_at = date('Y-m-d H:i:s');

// Include config.php to use database connection
include 'config.php';

$sql = "INSERT INTO tb_ratings (user_id, rating, feedback, created_at) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("idss", $user_id, $rating, $feedback, $created_at);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    error_log('MySQL error: ' . $stmt->error); // Log error dari MySQL
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
