<?php
// * API FOR FETCHING ALL DEPARTMENT
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';


// Initialize response object
$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $instructor_id = $_GET['instructorId'];
    // SQL query to fetch data
    $sql = "SELECT schedule.*, instructor.first_name, instructor.middle_name, instructor.surname, course.course_code, course.course_name, room.room_name, course_type.course_type, program.program_name, program.abbreviation, school_year_semester.school_year, school_year_semester.term
            FROM schedule
            LEFT JOIN instructor ON schedule.instructor_id = instructor.instructor_id
            LEFT JOIN course ON schedule.course_id = course.course_id
            LEFT JOIN room ON schedule.room_id = room.room_id
            LEFT JOIN course_type ON schedule.course_type_id = course_type.course_type_id
            LEFT JOIN program ON schedule.program_id = program.program_id
            LEFT JOIN school_year_semester ON schedule.school_year_semester_id = school_year_semester.school_year_semester_id
            WHERE school_year_semester.status = 'active' AND schedule.instructor_id = '$instructor_id'";

    // Execute the query
    $result = $conn->query($sql);

    // Check if there are results
    if ($result->num_rows > 0) {
        // Initialize an array to store the rows
        $rows = array();

        // Fetch data from each row
        while ($row = $result->fetch_assoc()) {
            // Add the row to the array
            $rows[] = $row;
        }

        // Set response properties
        $response->success = true;
        $response->data = $rows;
    } else {
        // No results found
        $response->success = false;
        $response->message = "No data found";
    }

    // Convert response object to JSON format
    $json_response = json_encode($response);

    http_response_code(200);
    // Send JSON response
    echo $json_response;
} else {
    // Invalid request method
    $response->success = false;
    $response->message = "Invalid request method";

    // Convert response object to JSON format
    $json_response = json_encode($response);

    http_response_code(405); // Method Not Allowed
    // Send JSON response
    echo $json_response;
}
?>
