
<?php
// * API FOR SIGN UP

include_once '../../includes/db.php';
include '../../includes/header.php';

header("Content-Type: multipart/form-data");


// Initialize response object
$response = new stdClass();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(!isset($_POST['firstName']) && !isset($_POST['middleName']) && !isset($_POST['surname']) && !isset($_POST['email']) &&  !isset($_POST['password'])){
        // Handle case where 'cor' key is not set or file upload failed
        $response->error = "Please fill out all the required fields!";
        http_response_code(400);
        echo json_encode($response);
        exit(); // Terminate script execution
    }

    // ! USING FORM DATA FORMAT
    $first_name = $_POST['firstName'];
    $middle_name = $_POST['middleName'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $default_profpic = $_SERVER['DOCUMENT_ROOT'].'/timetable-system-backend/asset/user.png';
    // optionals
    $department_id = isset($_POST['departmentId']) ? $_POST['departmentId'] : null;
    $position = isset($_POST['position']) ? $_POST['position'] : null;
    $title = isset($_POST['title']) ? $_POST['title'] : null;


    //? Validate email format
        // $valid_email_formats = '/^(.+)@(bicol-u\.edu\.ph)$/';  // Combined regex
        // if(!preg_match($valid_email_formats, $email, $matches)){
        //             $response->error = "Invalid email format";
        //             http_response_code(401);
        //             echo json_encode($response);
        //             exit(); // Terminate script execution
        // }

            // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt_email_exist = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt_email_exist->bind_param("s", $email);
    $stmt_email_exist->execute();
    $result_email_exist = $stmt_email_exist->get_result();

    if ($result_email_exist->num_rows > 0) {
        $response->error = "Account with the same email already exists.";
        http_response_code(401);
        echo json_encode($response);
        exit(); // Terminate script execution
    } else {
        // Insert into users table
        $stmt_user_insert = $conn->prepare("INSERT INTO users (email, password, type) VALUES (?, ?, 'admin')");
        $stmt_user_insert->bind_param("ss", $email, $hashedPassword);

        if ($stmt_user_insert->execute()) {
            // Get the last inserted user ID
            $last_insert = $stmt_user_insert->insert_id;

            // Construct the admin insert query dynamically
            $admin_sql = "INSERT INTO admin (user_id, first_name,middle_name, surname, profile_pic";
            $values_sql = "VALUES (?, ?,?, ?, ?";

            // Parameters array for binding
            $params = array("issss", $last_insert, $first_name,$middle_name, $surname, $default_profpic);

            // Add optional fields to the query if provided
            if ($department_id !== null && $position == null && $title == null) {
                $admin_sql .= ", department_id";
                $values_sql .= ", ?";
                array_push($params, $department_id);
                $params[0] = "issssi";
            }else if ($department_id == null && $position !== null && $title == null) {
                $admin_sql .= ", position";
                $values_sql .= ", ?";
                array_push($params,$position);
                $params[0] = "isssss";
            }else if ($department_id == null && $position == null && $title !== null) {
                $admin_sql .= ", title";
                $values_sql .= ", ?";
                array_push($params,$title);
                $params[0] = "isssss";
            }else if ($department_id !== null && $position !== null && $title == null){
                $admin_sql .= ", department_id, position";
                $values_sql .= ", ?, ?";
                 array_push($params,$department_id, $position);
                $params[0] = "issssis";
            }else if ($department_id !== null && $position == null && $title !== null){
                $admin_sql .= ", department_id, title";
                $values_sql .= ", ?, ?";
                array_push($params,$department_id, $title);
                $params[0] = "issssis";
            }else if ($department_id == null && $position !== null && $title !== null){
                $admin_sql .= ", position, title";
                $values_sql .= ", ?, ?";
                array_push($params,$position, $title);
                $params[0] = "issssss";
            }else{
                 $admin_sql .= ", department_id, position, title";
                $values_sql .= ", ?, ?, ?";
                array_push($params,$department_id,$position, $title);
                $params[0] = "issssiss";
            }

            // Complete the query and execute
            $admin_sql .= ") " . $values_sql . ")";
            $stmt_admin_insert = $conn->prepare($admin_sql);
            $stmt_admin_insert->bind_param(...$params);

            if ($stmt_admin_insert->execute()) {
                $response->message = "Account registered successfully";
                http_response_code(200);
                echo json_encode($response);
                exit(); // Terminate script execution
            } else {
                $response->error = $conn->error;
                http_response_code(401);
                echo json_encode($response);
                exit(); // Terminate script execution
            }
        } else {
            $response->error = $conn->error;
            http_response_code(401);
            echo json_encode($response);
            exit(); // Terminate script execution
        }
    }
} else {
    $response->error = "Invalid request method";
    http_response_code(501);
    echo json_encode($response);
    exit(); // Terminate script execution
}