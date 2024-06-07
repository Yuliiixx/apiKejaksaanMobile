<?php
include 'config.php';

// Ambil data dari $_POST
$id = isset($_POST['id']) ? $_POST['id'] : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$phone = isset($_POST['phone']) ? $_POST['phone'] : null;
$ktp_number = isset($_POST['ktp_number']) ? $_POST['ktp_number'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$address = isset($_POST['address']) ? $_POST['address'] : null;
$role = isset($_POST['role']) ? $_POST['role'] : null;

// Debug: Tulis data yang diterima ke dalam log
file_put_contents('debug.log', "Data: " . print_r(compact('id', 'name', 'email', 'phone', 'ktp_number', 'password', 'address', 'role'), true) . "\n", FILE_APPEND);

// Lakukan validasi data
if (is_null($id) || is_null($name) || is_null($email) || is_null($phone) || is_null($ktp_number) || is_null($address) || is_null($role)) {
    echo json_encode(["message" => "All fields are required"]);
    exit();
}

// Hash password jika disediakan
$hashed_password = null;
if (!is_null($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
}

// Siapkan statement SQL
if (is_null($hashed_password)) {
    $sql = "UPDATE tb_users SET name=?, email=?, phone=?, ktp_number=?, address=?, role=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $name, $email, $phone, $ktp_number, $address, $role, $id);
} else {
    $sql = "UPDATE tb_users SET name=?, email=?, phone=?, ktp_number=?, password=?, address=?, role=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $name, $email, $phone, $ktp_number, $hashed_password, $address, $role, $id);
}

// Eksekusi statement SQL
if ($stmt->execute()) {
    echo json_encode(["message" => "User updated"]);
} else {
    echo json_encode(["message" => "Error: " . $stmt->error]);
}

// Tutup statement dan koneksi
$stmt->close();
$conn->close();
?>
