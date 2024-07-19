<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require '../config.php';

$request_method = $_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'GET':
        // Retrieve records
        if(!empty($_GET["id"])) {
            $id = intval($_GET["id"]);
            get_jms($id);
        } else {
            get_jms();
        }
        break;
    case 'POST':
        // Insert record
        insert_jms();
        break;
    case 'PUT':
        // Update record
        if(!empty($_GET["id"])) {
            $id = intval($_GET["id"]);
            update_jms($id);
        } else {
            echo json_encode(array("message" => "ID is required for update"));
        }
        break;
    case 'DELETE':
        // Delete record
        if(!empty($_GET["id"])) {
            $id = intval($_GET["id"]);
            delete_jms($id);
        } else {
            echo json_encode(array("message" => "ID is required for delete"));
        }
        break;
    default:
        // Invalid Request Method
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

function get_jms($id = 0) {
    global $conn;
    $query = "SELECT * FROM tb_jms";
    if($id != 0) {
        $query .= " WHERE id=$id LIMIT 1";
    }
    $response = array();
    $result = $conn->query($query);
    while($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
    echo json_encode($response);
}

function insert_jms() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data["school_name"]) || !isset($data["applicant_name"]) || !isset($data["user_id"]) || !isset($data["status"])) {
        echo json_encode(array("message" => "Invalid input"));
        return;
    }

    $school_name = $data["school_name"];
    $applicant_name = $data["applicant_name"];
    $user_id = $data["user_id"];
    $status = $data["status"];

    $query = "INSERT INTO tb_jms (school_name, applicant_name, user_id, status) VALUES ('$school_name', '$applicant_name', '$user_id', '$status')";

    try {
        if($conn->query($query) === TRUE) {
            $response = array("message" => "Record added successfully");
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        $response = array("message" => "Error: " . $e->getMessage());
    }
    echo json_encode($response);
}

function update_jms($id) {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data["school_name"]) || !isset($data["applicant_name"]) || !isset($data["user_id"]) || !isset($data["status"])) {
        echo json_encode(array("message" => "Invalid input"));
        return;
    }

    $school_name = $data["school_name"];
    $applicant_name = $data["applicant_name"];
    $user_id = $data["user_id"];
    $status = $data["status"];

    $query = "UPDATE tb_jms SET school_name='$school_name', applicant_name='$applicant_name', user_id='$user_id', status='$status' WHERE id=$id";

    try {
        if($conn->query($query) === TRUE) {
            $response = array("message" => "Record updated successfully");
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        $response = array("message" => "Error: " . $e->getMessage());
    }
    echo json_encode($response);
}

function delete_jms($id) {
    global $conn;
    $query = "DELETE FROM tb_jms WHERE id=$id";
    try {
        if($conn->query($query) === TRUE) {
            $response = array("message" => "Record deleted successfully");
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        $response = array("message" => "Error: " . $e->getMessage());
    }
    echo json_encode($response);
}

$conn->close();
?>
