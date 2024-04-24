<?php
// * API FOR INSERTING CURRICULUM
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $year_effectivity = $_POST['yearEffectivity'];
    $program_id = $_POST['programId'];

    $query = "INSERT INTO curriculum (year_effectivity, program_id) VALUE(?,?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii",$year_effectivity, $program_id);
    $stmt->execute();

    http_response_code(200);
    echo json_encode(array("message"=>"Successfully added the curriculum!"));
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