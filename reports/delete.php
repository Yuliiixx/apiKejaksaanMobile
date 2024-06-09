<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config.php';

function respond($code, $message) {
    http_response_code($code);
    echo json_encode(array("message" => $message));
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (isset($data->id) && !empty($data->id)) {
    $stmt = $conn->prepare("DELETE FROM tb_reports WHERE id = ?");
    $stmt->bind_param("i", $data->id);

    if ($stmt->execute()) {
        respond(200, "Report was deleted.");
    } else {
        respond(503, "Unable to delete report.");
    }
    $stmt->close();
} else {
    respond(400, "Unable to delete report. Data is incomplete.");
}

$conn->close();
?>
