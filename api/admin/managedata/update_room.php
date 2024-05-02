<?php
// * API FOR INSERTING DEPARTMENT
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $room_id = $_POST['roomId'];
    $department_id = $_POST['departmentId'];
    $building_name = $_POST['buildingName'];
    $room_name = $_POST['roomName'];
    $room_type = $_POST['roomType'];

    $query = "UPDATE room SET department_id = ?, building_name = ?, room_name = ?, room_type = ?
              WHERE room_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssi",$department_id, $building_name, $room_name, $room_type, $room_id);
    $stmt->execute();

    http_response_code(200);
    echo json_encode(array("message"=>"Successfully updated the Room!"));
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