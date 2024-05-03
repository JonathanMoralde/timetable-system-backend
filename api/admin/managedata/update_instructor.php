
<?php
// * API FOR SIGN UP

include_once '../../../includes/db.php';
include '../../../includes/header.php';

header("Content-Type: multipart/form-data");


// Initialize response object
$response = new stdClass();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(!isset($_POST['instructorId']) && !isset($_POST['employeeSchoolId']) && !isset($_POST['departmentId']) && !isset($_POST['researchStatus']) && !isset($_POST['academicRank']) && !isset($_POST['employmentStatus']) && !isset($_POST['firstName']) && !isset($_POST['middleName']) && !isset($_POST['surname'])){
        // Handle case where 'cor' key is not set or file upload failed
        $response->error = "Please fill out all the required fields!";
        http_response_code(400);
        echo json_encode($response);
        exit(); // Terminate script execution
    }

    // ! USING FORM DATA FORMAT
    $instructor_id = $_POST['instructorId'];
    $employee_school_id = $_POST['employeeSchoolId'];
    $department_id = $_POST['departmentId'];
    $research_status = $_POST['researchStatus'];
    $academic_rank = $_POST['academicRank'];
    $employment_status = $_POST['employmentStatus'];
    $first_name = $_POST['firstName'];
    $middle_name = $_POST['middleName'];
    $surname = $_POST['surname'];
    // optionals
    $title = isset($_POST['title']) ? $_POST['title'] : null;


    //? Validate email format
        // $valid_email_formats = '/^(.+)@(bicol-u\.edu\.ph)$/';  // Combined regex
        // if(!preg_match($valid_email_formats, $email, $matches)){
        //             $response->error = "Invalid email format";
        //             http_response_code(401);
        //             echo json_encode($response);
        //             exit(); // Terminate script execution
        // }

    //         // Hash the password
    // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // // Check if email already exists
    // $stmt_email_exist = $conn->prepare("SELECT * FROM users WHERE email = ?");
    // $stmt_email_exist->bind_param("s", $email);
    // $stmt_email_exist->execute();
    // $result_email_exist = $stmt_email_exist->get_result();

    // if ($result_email_exist->num_rows > 0) {
    //     $response->error = "Account with the same email already exists.";
    //     http_response_code(401);
    //     echo json_encode($response);
    //     exit(); // Terminate script execution
    // } else {
        // Insert into users table
        // $stmt_user_insert = $conn->prepare("INSERT INTO users (email, password, type) VALUES (?, ?, 'instructor')");
        // $stmt_user_insert->bind_param("ss", $email, $hashedPassword);

        // if ($stmt_user_insert->execute()) {
            // Get the last inserted user ID
            // $last_insert = $stmt_user_insert->insert_id;

            // Construct the admin insert query dynamically
            $admin_sql = "UPDATE instructor SET department_id = ?, school_id = ?, research_status = ?, academic_rank = ?, employment_status = ?, first_name = ?, middle_name = ?, surname = ?";
            $where_sql = " WHERE instructor_id = ?";

            // Parameters array for binding
            $params = array("isssssssi", $department_id, $employee_school_id, $research_status, $academic_rank, $employment_status, $first_name, $middle_name, $surname);

            // Add optional fields to the query if provided
            if ($title !== null) {
                $admin_sql .= ", title = ?";
                $params[0] = "issssssssi";
                $params[] = $title; // Push the title to the parameters array
            }
            $params[] = $instructor_id; // Push the title to the parameters array

            // Complete the query and execute
            $admin_sql .= $where_sql;
            $stmt_admin_insert = $conn->prepare($admin_sql);
            $stmt_admin_insert->bind_param(...$params);
            // Use call_user_func_array to pass parameters array dynamically

            if ($stmt_admin_insert->execute()) {
                $response->message = "Instructor details updated successfully";
                http_response_code(200);
                echo json_encode($response);
                exit(); // Terminate script execution
            } else {
                $response->error = $conn->error;
                http_response_code(401);
                echo json_encode($response);
                exit(); // Terminate script execution
            }
        // } else {
        //     $response->error = $conn->error;
        //     http_response_code(401);
        //     echo json_encode($response);
        //     exit(); // Terminate script execution
        // }
    // }
} else {
    $response->error = "Invalid request method";
    http_response_code(501);
    echo json_encode($response);
    exit(); // Terminate script execution
}