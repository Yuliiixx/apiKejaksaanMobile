<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'config.php';
           
function respond($code, $message) {
    http_response_code($code);
    echo json_encode(array("message" => $message));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required_fields = ['id', 'name', 'email', 'phone', 'ktp_number', 'address', 'password'];
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        respond(400, "Data tidak lengkap. Field yang hilang: " . implode(", ", $missing_fields));
    }

    // Log the received data
    error_log("Received Data: " . json_encode($_POST));

    $id_user = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $ktp_number = $_POST['ktp_number'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "UPDATE tb_users SET 
            name = ?, email = ?, phone = ?, ktp_number = ?, address = ?, password = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Failed to prepare statement: " . $conn->error);
        respond(503, "Gagal menyiapkan statement: " . $conn->error);
    }

    error_log("SQL Query: " . $sql);

    $stmt->bind_param("ssssssi", $name, $email, $phone, $ktp_number, $address, $hashed_password, $id_user);
    if ($stmt->execute()) {
        respond(200, "Data pengguna berhasil diperbarui.");
    } else {
        error_log("Failed to execute statement: " . $stmt->error);
        respond(503, "Gagal memperbarui data pengguna: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    respond(405, "Metode tidak diizinkan.");
}
?>
