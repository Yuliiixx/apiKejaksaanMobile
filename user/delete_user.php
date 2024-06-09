<?php
include 'config.php';

// Ambil ID dari $_POST
$id = isset($_POST['id']) ? $_POST['id'] : null;

// Debug: Tulis data yang diterima ke dalam log
file_put_contents('debug.log', "ID: " . $id . "\n", FILE_APPEND);

// Lakukan validasi data
if (is_null($id)) {
    echo json_encode(["message" => "ID is required"]);
    exit();
}

// Siapkan statement SQL
$sql = "DELETE FROM tb_users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

// Eksekusi statement SQL
if ($stmt->execute()) {
    echo json_encode(["message" => "User deleted"]);
} else {
    echo json_encode(["message" => "Error: " . $stmt->error]);
}

// Tutup statement dan koneksi
$stmt->close();
$conn->close();
?>
