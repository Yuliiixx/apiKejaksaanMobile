<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once 'config.php';

$result = $conn->query("SELECT * FROM tb_reports");

if ($result->num_rows > 0) {
    $reports_arr = array();
    $reports_arr["records"] = array();

    while($row = $result->fetch_assoc()) {
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
        array_push($reports_arr["records"], $report_item);
    }

    http_response_code(200);
    echo json_encode($reports_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No reports found."));
}

$conn->close();
?>
