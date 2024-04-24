<?php
// * API FOR INSERTING ROOM
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $department_id = $_POST['departmentId'];
    $building_name = $_POST['buildingName'];
    $room_name = $_POST['roomName'];
    $type = $_POST['type'];

    $query = "INSERT INTO room (department_id, building_name, room_name, room_type) VALUE(?,?,?,?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss",$department_id, $building_name, $room_name,$type);
    $stmt->execute();

    http_response_code(200);
    echo json_encode(array("message"=>"Successfully added the room!"));
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