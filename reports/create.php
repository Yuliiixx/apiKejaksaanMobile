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
    $required_fields = ['ktp', 'document', 'user_id', 'report_type', 'nama', 'nohp', 'report', 'status'];
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if ($field === 'ktp' || $field === 'document') {
            if (!isset($_FILES[$field])) {
                $missing_fields[] = $field;
            }
        } else {
            if (!isset($_POST[$field])) {
                $missing_fields[] = $field;
            }
        }
    }

    if (!empty($missing_fields)) {
        respond(400, "Data tidak lengkap. Field yang hilang: " . implode(", ", $missing_fields));
    }

    // Validasi nilai report_type
    $valid_report_types = ['Pegawai', 'Korupsi', 'Hukum', 'Aliran', 'Pilkada'];
    if (!in_array($_POST['report_type'], $valid_report_types)) {
        respond(400, "Nilai report_type tidak valid.");
    }

    // Jalur unggahan file
    $ktpPath = '../uploads/' . 'ktp_' . $_POST['nama'] . '.pdf';
    $documentPath = '../uploads/' . 'dokument_' . $_POST['nama'] . '.pdf';

    // Pindahkan file yang diunggah
    if (!move_uploaded_file($_FILES['ktp']['tmp_name'], $ktpPath) || !move_uploaded_file($_FILES['document']['tmp_name'], $documentPath)) {
        respond(503, "Tidak dapat mengunggah file.");
    }

    // Siapkan dan ikat
    $stmt = $conn->prepare("INSERT INTO tb_reports (user_id, report_type, nama, nohp, ktp, report, document, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    if ($stmt === false) {
        respond(503, "Gagal menyiapkan statement: " . $conn->error);
    }

    $stmt->bind_param("isssssss", $_POST['user_id'], $_POST['report_type'], $_POST['nama'], $_POST['nohp'], $ktpPath, $_POST['report'], $documentPath, $_POST['status']);

    // Eksekusi statement
    if ($stmt->execute()) {
        respond(201, "Laporan berhasil dibuat.");
    } else {
        respond(503, "Tidak dapat membuat laporan: " . $stmt->error);
    }

    // Tutup statement
    $stmt->close();
} else {
    respond(405, "Metode permintaan tidak diizinkan.");
}

$conn->close();
?>
