<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'config.php';

$id = isset($_GET['id']) ? $_GET['id'] : die();

$stmt = $conn->prepare("SELECT * FROM tb_reports WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $report_item = array(
        "id" => $row["id"],
        "user_id" => $row["user_id"],
        "report_type" => $row["report_type"],
        "nama" => $row["nama"],
        "nohp" => $row["nohp"],
        "ktp" => $row["ktp"],
        "report" => $row["report"],
        "document" => $row["document"],
        "status" => $row["status"],
        "created_at" => $row["created_at"],
        "updated_at" => $row["updated_at"]
    );

    http_response_code(200);
    echo json_encode($report_item);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Report not found."));
}

$stmt->close();
$conn->close();
?>
