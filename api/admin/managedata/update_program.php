<?php
// * API FOR INSERTING PROGRAM
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $program_name = $_POST['programName'];
    $abbreviation = $_POST['abbreviation'];
    $department_id = $_POST['departmentId'];
    $program_id = $_POST['programId'];

    $query = "UPDATE program SET program_name = ?, abbreviation = ?, department_id = ?
    WHERE program_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $program_name, $abbreviation,$department_id, $program_id);
    $stmt->execute();

    http_response_code(200);
    echo json_encode(array("message"=>"Successfully updated the program!"));
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