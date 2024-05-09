<?php
// * API FOR INSERTING Course Type
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $courses = json_decode($_POST['courses']);
    $instructor_id = $_POST['instructorId'];

    foreach ($courses as $course_id) {
        
        $query = "INSERT INTO courses_assigned (course_id, instructor_id) VALUE(?,?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii",$course_id, $instructor_id);
        $stmt->execute();
    
    }
    
    http_response_code(200);
    echo json_encode(array("message"=>"Successfully assigned the courses!"));
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