<?php
include 'config.php';

// Query untuk mendapatkan semua data dari tb_users
$sql = "SELECT * FROM tb_users";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    // Menyimpan setiap baris hasil query ke dalam array
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Menutup koneksi
$conn->close();

// Mengembalikan data dalam format JSON
header('Content-Type: application/json');
echo json_encode($users);
?>
