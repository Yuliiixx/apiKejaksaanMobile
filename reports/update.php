<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config.php';

function respond($code, $message) {
    http_response_code($code);
    echo json_encode(array("message" => $message));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_method']) && $_POST['_method'] == 'PUT') {
    // Check if required fields are set
    if (!isset($_POST['id']) || !isset($_POST['user_id']) || !isset($_POST['report_type']) || !isset($_POST['nama']) || !isset($_POST['nohp']) || !isset($_POST['report']) || !isset($_POST['status'])) {
        respond(400, "Data is incomplete.");
    }

    // Validate report_type value
    $valid_report_types = ['Pegawai', 'Korupsi', 'Hukum', 'Aliran', 'Pilkada'];
    if (!in_array($_POST['report_type'], $valid_report_types)) {
        respond(400, "Invalid report_type value.");
    }

    // File upload paths
    $ktpPath = null;
    $documentPath = null;

    if (isset($_FILES['ktp']) && is_uploaded_file($_FILES['ktp']['tmp_name'])) {
        $ktpPath = '../uploads/' . 'ktp_' . $_POST['nama'] . '.pdf';
        if (!move_uploaded_file($_FILES['ktp']['tmp_name'], $ktpPath)) {
            respond(503, "Unable to upload KTP file.");
        }
    }

    if (isset($_FILES['document']) && is_uploaded_file($_FILES['document']['tmp_name'])) {
        $documentPath = '../uploads/' . 'document_' . $_POST['nama'] . '.pdf';
        if (!move_uploaded_file($_FILES['document']['tmp_name'], $documentPath)) {
            respond(503, "Unable to upload Document file.");
        }
    }

    // Prepare SQL query with conditional file update
    $query = "UPDATE tb_reports SET user_id = ?, report_type = ?, nama = ?, nohp = ?, report = ?, status = ?, updated_at = NOW()";
    if ($ktpPath) {
        $query .= ", ktp = '$ktpPath'";
    }
    if ($documentPath) {
        $query .= ", document = '$documentPath'";
    }
    $query .= " WHERE id = ?";

    // Prepare and bind
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssssi", $_POST['user_id'], $_POST['report_type'], $_POST['nama'], $_POST['nohp'], $_POST['report'], $_POST['status'], $_POST['id']);

    // Execute the statement
    if ($stmt->execute()) {
        respond(200, "Report was updated.");
    } else {
        respond(503, "Unable to update report.");
    }

    // Close the statement
    $stmt->close();
} else {
    respond(405, "Request method not allowed.");
}

$conn->close();
?>
