<?php
// * API FOR INSERTING Course Type
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $course_type = $_POST['courseType'];
    $duration = $_POST['duration'];
    $lec_unit = $_POST['lecUnit'];
    $lab_unit = $_POST['labUnit'];
    $load_unit = $_POST['loadUnit'];
    $course_type_id = $_POST['courseTypeId'];
    
    $query = "UPDATE course_type SET course_type = ?, duration = ?, lec_unit = ?,lab_unit = ?,load_unit = ?
    WHERE course_type_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siiiii",$course_type, $duration, $lec_unit,$lab_unit,$load_unit,$course_type_id);
    $stmt->execute();

    http_response_code(200);
    echo json_encode(array("message"=>"Successfully updated the Course Type!"));
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