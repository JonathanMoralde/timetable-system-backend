<?php
// * API FOR INSERTING Course Type
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $course_type_id = $_POST['courseTypeId'];
    $course_code = $_POST['courseCode'];
    $course_name = $_POST['courseName'];
    $curriculum_id = $_POST['curriculumId'];
    $year_level = $_POST['yearLevel'];
    $semester = $_POST['semester'];
    $course_id = $_POST['courseId'];

    $query = "UPDATE course SET course_type_id = ?, course_code = ?, course_name = ?,curriculum_id = ?,year_level = ?,semester = ?
    WHERE course_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issiiii",$course_type_id, $course_code, $course_name,$curriculum_id,$year_level,$semester,$course_id);
    $stmt->execute();

    http_response_code(200);
    echo json_encode(array("message"=>"Successfully updated the Course!"));
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