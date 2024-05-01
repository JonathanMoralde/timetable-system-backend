<?php
// * API FOR INSERTING DEPARTMENT
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $department_id = $_POST['departmentId'];
    $department_name = $_POST['departmentName'];
    $dean = $_POST['deanName'];

    $query = "UPDATE department SET department_name = ?, dean = ?
              WHERE department_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi",$department_name, $dean, $department_id);
    $stmt->execute();

    http_response_code(200);
    echo json_encode(array("message"=>"Successfully updated the department!"));
    $stmt->close();

}else {
    // Invalid request method
    $response->success = false;
    $response->message = "Invalid request method";

    // Convert response object to JSON format
    $json_response = json_encode($response);

    http_response_code(405); // Method Not Allowed
    // Send JSON response
    echo $json_response;
}