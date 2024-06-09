<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'config.php';

// Ambil data dari $_POST
$name = isset($_POST['name']) ? $_POST['name'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$phone = isset($_POST['phone']) ? $_POST['phone'] : null;
$ktp_number = isset($_POST['ktp_number']) ? $_POST['ktp_number'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$address = isset($_POST['address']) ? $_POST['address'] : null;
$role = isset($_POST['role']) ? $_POST['role'] : null;

// Debug: Tulis input ke dalam log
file_put_contents('debug.log', "Name: " . $name . "\nEmail: " . $email . "\nPhone: " . $phone . "\nKTP: " . $ktp_number . "\nPassword: " . $password . "\nAddress: " . $address . "\nRole: " . $role . "\n", FILE_APPEND);

// Lakukan validasi data
if (is_null($name) || is_null($email) || is_null($phone) || is_null($ktp_number) || is_null($password) || is_null($address) || is_null($role)) {
    echo json_encode(["message" => "All fields are required"]);
    exit();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Siapkan statement SQL
$sql = "INSERT INTO tb_users (name, email, phone, ktp_number, password, address, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    file_put_contents('debug.log', "Prepare failed: " . $conn->error . "\n", FILE_APPEND);
    echo json_encode(["message" => "Prepare failed: " . $conn->error]);
    exit();
}
$stmt->bind_param("sssssss", $name, $email, $phone, $ktp_number, $hashed_password, $address, $role);

// Eksekusi statement SQL
if ($stmt->execute()) {
    echo json_encode(["message" => "User registered"]);
} else {
    file_put_contents('debug.log', "Execute failed: " . $stmt->error . "\n", FILE_APPEND);
    echo json_encode(["message" => "Error: " . $stmt->error]);
}

// Tutup statement dan koneksi
$stmt->close();
$conn->close();
?>
