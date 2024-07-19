<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'config.php';

$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : die();
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : die();

$query = "SELECT * FROM tb_reports WHERE report_type = ? AND user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $report_type, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$reports = array();

while ($row = $result->fetch_assoc()) {
    $report = array(
        "nama" => $row['nama'],
        "nohp" => $row['nohp'],
        "ktp" => $row['ktp'],
        "report" => $row['report'],
        "document" => $row['document'],
        "status" => $row['status'],
        "created_at" => $row['created_at']
    );
    array_push($reports, $report);
}

echo json_encode($reports);

$stmt->close();
$conn->close();
?>
