<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'config.php';

$sql = "SELECT * FROM tb_jms";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. " - School Name: " . $row["school_name"]. " - Applicant Name: " . $row["applicant_name"]. " - User ID: " . $row["user_id"]. "<br>";
    }
} else {
    echo "0 results";
}
$conn->close();
?>
