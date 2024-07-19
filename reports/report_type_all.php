<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'config.php';

// Mendapatkan tipe laporan dari query parameter
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : die();

// Menyiapkan query untuk mendapatkan laporan berdasarkan tipe laporan
$query = "SELECT * FROM tb_reports WHERE report_type = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $report_type);
$stmt->execute();
$result = $stmt->get_result();

$reports = array();

// Mengambil hasil query dan memasukkannya ke dalam array
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

// Mengembalikan hasil dalam format JSON
echo json_encode($reports);

$stmt->close();
$conn->close();
?>
