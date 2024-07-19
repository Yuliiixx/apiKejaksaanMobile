<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Include configuration file
include 'config.php';

// Query to fetch all user data
$sql = "SELECT * FROM tb_users";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    // Fetch all user data
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Close the connection
$conn->close();

// Return the user data in JSON format
header('Content-Type: application/json');
echo json_encode($users);
?>
