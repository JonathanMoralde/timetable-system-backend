<?php
// * API FOR FETCHING ALL DEPARTMENT
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';

// Initialize response object
$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $program_id = isset($_GET['programId']) ? $_GET['programId'] : null;
    // SQL query to fetch data
    $sql = "SELECT 
                instructor.instructor_id,
                instructor.first_name,
                instructor.middle_name,
                instructor.surname,
                instructor.academic_rank,
                instructor.employment_status,
                department.department_name,
                course.course_id,
                course.course_name,
                course.course_code,
                room.room_id,
                room.room_name,
                course_type.course_type_id,
                course_type.course_type,
                course_type.lec_unit,
                course_type.lab_unit,
                course_type.load_unit,
                program.program_id,
                program.abbreviation,
                school_year_semester.school_year_semester_id,
                schedule.schedule_id,
                schedule.day,
                schedule.start_time,
                schedule.end_time,
                schedule.year_level,
                schedule.block
            FROM schedule
            LEFT JOIN instructor ON schedule.instructor_id = instructor.instructor_id
            LEFT JOIN department ON instructor.department_id = department.department_id
            LEFT JOIN course ON schedule.course_id = course.course_id
            LEFT JOIN room ON schedule.room_id = room.room_id
            LEFT JOIN course_type ON schedule.course_type_id = course_type.course_type_id
            LEFT JOIN program ON schedule.program_id = program.program_id
            LEFT JOIN school_year_semester ON schedule.school_year_semester_id = school_year_semester.school_year_semester_id
            WHERE school_year_semester.status = 'active'";

            if(!empty($program_id) && isset($_GET['programId'])){
                $sql .= " AND schedule.program_id = '$program_id'";
            }

    // Execute the query
    $result = $conn->query($sql);

    // Check if there are results
    if ($result->num_rows > 0) {
        // Initialize an array to store the rows
        $response_data = array();

        // Fetch data from each row
        while ($row = $result->fetch_assoc()) {
            // Create the schedules array
            $schedule_data = array(
                "schedule_id" => $row['schedule_id'],
                "course_id" => $row['course_id'],
                "course_name" => $row['course_name'],
                "course_code" => $row['course_code'],
                "day" => $row['day'],
                "start_time" => $row['start_time'],
                "end_time" => $row['end_time'],
                "room_id" => $row['room_id'],
                "room_name" => $row['room_name'],
                "course_type_id" => $row['course_type_id'],
                "lec_unit" => $row['lec_unit'],
                "lab_unit" => $row['lab_unit'],
                "load_unit" => $row['load_unit'],
                "program_id" => $row['program_id'],
                "abbreviation" => $row['abbreviation'],
                "year_level" => $row['year_level'],
                "block" => $row['block'],
                "school_year_semester_id" => $row['school_year_semester_id']
            );

            // Check if instructor already exists in response data
            $instructor_exists = false;
            foreach ($response_data as &$instructor) {
                if ($instructor['instructor_id'] == $row['instructor_id']) {
                    // Add schedule to existing instructor
                    $instructor['schedules'][] = $schedule_data;
                    $instructor_exists = true;
                    break;
                }
            }

            // If instructor does not exist, add instructor and schedule
            if (!$instructor_exists) {
                $instructor_data = array(
                    "instructor_id" => $row['instructor_id'],
                    "surname" => $row['surname'],
                    "middle_name" => $row['middle_name'],
                    "first_name" => $row['first_name'],
                    "academic_rank" => $row['academic_rank'],
                    "employment_status" => $row['employment_status'],
                    "department_name" => $row['department_name'],
                    "schedules" => array($schedule_data)
                );
                $response_data[] = $instructor_data;
            }
        }

        // Set response properties
        $response->success = true;
        $response->data = $response_data;
    } else {
        // No results found
        $response->success = false;
        $response->data = [];
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
