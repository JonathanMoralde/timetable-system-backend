<?php
// * API FOR INSERTING Schedule
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $instructor_id = $_POST['instructorId'];
    $course_id = $_POST['courseId'];
    $room_id = $_POST['roomId'];
    $course_type_id = $_POST['courseTypeId'];
    $program_id = $_POST['programId'];
    $year_level = $_POST['yearLevel'];
    $block = $_POST['block'];
    $day = $_POST['day'];
    $start_time = $_POST['startTime'];
    $end_time = $_POST['endTime'];

    // Fetch the school_year_semester_id first
    $fetch_sysem_sql = "SELECT school_year_semester_id FROM school_year_semester WHERE status = 'active'";
    $result = $conn->query($fetch_sysem_sql);

    if($result && $result->num_rows > 0) {
         $row = $result->fetch_assoc();
        $school_year_semester_id = $row['school_year_semester_id'];

        $query = "INSERT INTO schedule (instructor_id, course_id, room_id,course_type_id,program_id,year_level,block,day, start_time, end_time, school_year_semester_id) VALUE(?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiiiiissssi",$instructor_id, $course_id, $room_id,$course_type_id,$program_id,$year_level,$block, $day, $start_time, $end_time, $school_year_semester_id);
        $stmt->execute();
    
        http_response_code(200);
        echo json_encode(array("message"=>"Successfully added the Schedule!"));
        $stmt->close();

    }else {
        http_response_code(404);
        echo json_encode(array("error" => "No active semester found"));
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