<?php
// * API FOR INSERTING DEPARTMENT
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $sy_sem_id = $_POST['sySemId'];

    $reset_query = "UPDATE school_year_semester SET status = 'inactive'";
    if($conn->query($reset_query) === TRUE){

        $query = "UPDATE school_year_semester SET status = 'active'
                  WHERE school_year_semester_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i",$sy_sem_id);
        $stmt->execute();
    
        http_response_code(200);
        echo json_encode(array("message"=>"Successfully changed the active School Year & Semester!"));
        $stmt->close();
    }


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