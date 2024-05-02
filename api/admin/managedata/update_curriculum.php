<?php
// * API FOR INSERTING CURRICULUM
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $year_effectivity = $_POST['yearEffectivity'];
    $program_id = $_POST['programId'];
    $curriculum_id = $_POST['curriculumId'];

    $query = "UPDATE curriculum SET year_effectivity = ?, program_id = ? 
                WHERE curriculum_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii",$year_effectivity, $program_id,$curriculum_id);
    $stmt->execute();

    http_response_code(200);
    echo json_encode(array("message"=>"Successfully updated the curriculum!"));
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