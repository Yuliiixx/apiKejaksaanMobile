<?php
include 'config.php';

// Periksa apakah data dikirim sebagai form-data atau JSON mentah
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
} else {
    $data = $_POST;
}

// Debug: Tulis input ke dalam log
file_put_contents('debug.log', "Raw Input: " . print_r($data, true) . "\n", FILE_APPEND);

// Ambil data dari array
$email = isset($data['email']) ? $data['email'] : null;
$password = isset($data['password']) ? $data['password'] : null;

// Debug: Tulis data yang diterima ke dalam log
file_put_contents('debug.log', "Email: " . $email . "\nPassword: " . $password . "\n", FILE_APPEND);

// Lakukan validasi data
if (is_null($email) || is_null($password)) {
    echo json_encode(["message" => "Email and password are required"]);
    exit();
}

// Siapkan statement SQL
$sql = "SELECT * FROM tb_users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);

// Eksekusi statement SQL
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah pengguna ditemukan
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Verifikasi password
    if (password_verify($password, $user['password'])) {
        // Jika password benar, kirim data pengguna
        echo json_encode([
            "message" => "Login successful",
            "user" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "email" => $user['email'],
                "phone" => $user['phone'],
                "ktp_number" => $user['ktp_number'],
                "address" => $user['address'],
                "role" => $user['role']
            ]
        ]);
    } else {
        echo json_encode(["message" => "Invalid password"]);
    }
} else {
    echo json_encode(["message" => "User not found"]);
}

// Tutup statement dan koneksi
$stmt->close();
$conn->close();
?>
