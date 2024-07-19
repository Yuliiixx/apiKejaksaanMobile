<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'config.php';

function respond($code, $message, $success = false) {
    http_response_code($code);
    echo json_encode(array("message" => $message, "success" => $success));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required_fields = ['id', 'user_id', 'report_type', 'nama', 'nohp', 'report', 'status'];
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        respond(400, "Data tidak lengkap. Field yang hilang: " . implode(", ", $missing_fields), false);
    }

    // Tambahkan log untuk data yang diterima
    error_log("Data yang diterima: " . json_encode($_POST));

    $id = $_POST['id'];
    $ktpPath = null;
    $documentPath = null;

    if (isset($_FILES['ktp'])) {
        $ktpPath = '../uploads/' . 'ktp_' . $_POST['nama'] . '.pdf';
        if (!move_uploaded_file($_FILES['ktp']['tmp_name'], $ktpPath)) {
            respond(503, "Tidak dapat mengunggah file KTP.", false);
        }
    }

    if (isset($_FILES['document'])) {
        $documentPath = '../uploads/' . 'dokument_' . $_POST['nama'] . '.pdf';
        if (!move_uploaded_file($_FILES['document']['tmp_name'], $documentPath)) {
            respond(503, "Tidak dapat mengunggah file dokumen.", false);
        }
    }

    $sql = "UPDATE tb_reports SET 
            user_id = ?, report_type = ?, nama = ?, nohp = ?, report = ?, status = ?, updated_at = NOW()";

    if ($ktpPath) {
        $sql .= ", ktp = ?";
    }

    if ($documentPath) {
        $sql .= ", document = ?";
    }

    $sql .= " WHERE id = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        respond(503, "Gagal menyiapkan statement: " . $conn->error, false);
    }

    // Tambahkan log untuk query SQL yang akan dieksekusi
    error_log("Query SQL: " . $sql);

    if ($ktpPath && $documentPath) {
        $stmt->bind_param("isssssssi", $_POST['user_id'], $_POST['report_type'], $_POST['nama'], $_POST['nohp'], $_POST['report'], $_POST['status'], $ktpPath, $documentPath, $id);
    } elseif ($ktpPath) {
        $stmt->bind_param("issssssi", $_POST['user_id'], $_POST['report_type'], $_POST['nama'], $_POST['nohp'], $_POST['report'], $_POST['status'], $ktpPath, $id);
    } elseif ($documentPath) {
        $stmt->bind_param("issssssi", $_POST['user_id'], $_POST['report_type'], $_POST['nama'], $_POST['nohp'], $_POST['report'], $_POST['status'], $documentPath, $id);
    } else {
        $stmt->bind_param("isssssi", $_POST['user_id'], $_POST['report_type'], $_POST['nama'], $_POST['nohp'], $_POST['report'], $_POST['status'], $id);
    }

    if ($stmt->execute()) {
        respond(200, "Laporan berhasil diperbarui.", true);
    } else {
        respond(503, "Tidak dapat memperbarui laporan: " . $stmt->error, false);
    }

    $stmt->close();
} else {
    respond(405, "Metode permintaan tidak diizinkan.", false);
}

$conn->close();
?>
