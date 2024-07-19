<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database configuration
$host = 'localhost'; // sesuaikan dengan host database Anda
$dbname = 'db_kejaksaan'; // nama database
$username = 'root'; // username database
$password = ''; // password database

// Koneksi ke database menggunakan PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query untuk mengambil data rating beserta informasi user
    $query = "SELECT r.*, u.name as user_name
              FROM tb_ratings r
              INNER JOIN tb_users u ON r.user_id = u.id";

    // Eksekusi query
    $stmt = $pdo->query($query);
    $ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Encode ke JSON dan kirimkan sebagai response
    echo json_encode($ratings);
} catch (PDOException $e) {
    // Tangani error koneksi atau query
    http_response_code(500); // Internal Server Error
    echo json_encode(array("message" => "Error saat mengambil data: " . $e->getMessage()));
}
