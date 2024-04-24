<?php
// * API FOR INSERTING DEPARTMENT
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $department_name = $_POST['departmentName'];
    $dean = $_POST['dean'];

    $query = "INSERT INTO department (department_name, dean) VALUE(?,?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss",$department_name, $dean);
    $stmt->execute();

    http_response_code(200);
    echo json_encode(array("message"=>"Successfully added the department!"));
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