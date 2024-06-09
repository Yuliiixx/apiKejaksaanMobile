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
    // Check if files and form data are set
    if (!isset($_FILES['ktp']) || !isset($_FILES['document']) || !isset($_POST['user_id']) || !isset($_POST['report_type']) || !isset($_POST['nama']) || !isset($_POST['nohp']) || !isset($_POST['report']) || !isset($_POST['status'])) {
        respond(400, "Data is incomplete.");
    }

    // Validate report_type value
    $valid_report_types = ['Pegawai', 'Korupsi', 'Hukum', 'Aliran', 'Pilkada'];
    if (!in_array($_POST['report_type'], $valid_report_types)) {
        respond(400, "Invalid report_type value.");
    }

    // File upload paths
    $ktpPath = '../uploads/' . 'ktp_' . $_POST['nama'] . '.pdf';
    $documentPath = '../uploads/' . 'dokument_' . $_POST['nama'] . '.pdf';

    // Move uploaded files
    if (!move_uploaded_file($_FILES['ktp']['tmp_name'], $ktpPath) || !move_uploaded_file($_FILES['document']['tmp_name'], $documentPath)) {
        respond(503, "Unable to upload files.");
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO tb_reports (user_id, report_type, nama, nohp, ktp, report, document, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isssssss", $_POST['user_id'], $_POST['report_type'], $_POST['nama'], $_POST['nohp'], $ktpPath, $_POST['report'], $documentPath, $_POST['status']);

    // Execute the statement
    if ($stmt->execute()) {
        respond(201, "Report was created.");
    } else {
        respond(503, "Unable to create report.");
    }

    // Close the statement
    $stmt->close();
} else {
    respond(405, "Request method not allowed.");
}

$conn->close();
?>
