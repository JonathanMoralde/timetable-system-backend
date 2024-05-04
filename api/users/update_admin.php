
<?php
// * API FOR SIGN UP

include_once '../../includes/db.php';
include '../../includes/header.php';

header("Content-Type: multipart/form-data");


// Initialize response object
$response = new stdClass();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if( !isset($_POST['firstName']) && !isset($_POST['middleName']) && !isset($_POST['surname']) ){
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
    // optionals
    $email =  isset($_POST['email']) ? $_POST['email'] : null;
    $department_id = isset($_POST['departmentId']) ? $_POST['departmentId'] : null;
    $position = isset($_POST['position']) ? $_POST['position'] : null;
    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $user_id = $_SESSION['user_id'];


    //? Validate email format
        // $valid_email_formats = '/^(.+)@(bicol-u\.edu\.ph)$/';  // Combined regex
        // if(!preg_match($valid_email_formats, $email, $matches)){
        //             $response->error = "Invalid email format";
        //             http_response_code(401);
        //             echo json_encode($response);
        //             exit(); // Terminate script execution
        // }

            // Hash the password
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
        // Update email only if provided
    if ($email !== null) {
        // Update email in users table
        $stmt_user_update = $conn->prepare("UPDATE users SET email = ? WHERE user_id = ?");
        $stmt_user_update->bind_param("si", $email, $user_ud);

        if (!$stmt_user_update->execute()) {
            $response->error = $conn->error;
            http_response_code(401);
            echo json_encode($response);
            exit(); // Terminate script execution
        }
    }

        // if ($stmt_user_update->execute()) {

            // Construct the admin insert query dynamically
            $admin_sql = "UPDATE admin SET first_name = ?,middle_name = ?, surname = ?";

            // Parameters array for binding
            $params = array("sss", $first_name,$middle_name, $surname);
            
            // Add optional fields to the query if provided
            if ($department_id !== null && $position == null && $title == null) {
                $admin_sql .= ", department_id = ? WHERE user_id = ?";
                array_push($params, $department_id, $user_id);
                $params[0] = "sssii";
            }else if ($department_id == null && $position !== null && $title == null) {
                $admin_sql .= ", position = ? WHERE user_id = ?";
                // $values_sql .= ", ?";
                array_push($params,$position, $user_id);
                $params[0] = "ssssi";
            }else if ($department_id == null && $position == null && $title !== null) {
                $admin_sql .= ", title = ? WHERE user_id = ?";
                // $values_sql .= ", ?";
                array_push($params,$title, $user_id);
                $params[0] = "ssssi";
            }else if ($department_id !== null && $position !== null && $title == null){
                $admin_sql .= ", department_id = ?, position = ? WHERE user_id = ?";
                // $values_sql .= ", ?, ?";
                 array_push($params,$department_id, $position, $user_id);
                $params[0] = "sssisi";
            }else if ($department_id !== null && $position == null && $title !== null){
                $admin_sql .= ", department_id = ?, title = ? WHERE user_id = ?";
                // $values_sql .= ", ?, ?";
                array_push($params,$department_id, $title, $user_id);
                $params[0] = "sssisi";
            }else if ($department_id == null && $position !== null && $title !== null){
                $admin_sql .= ", position = ?, title = ? WHERE user_id = ?";
                // $values_sql .= ", ?, ?";
                array_push($params,$position, $title, $user_id);
                $params[0] = "sssssi";
            }else{
                 $admin_sql .= ", department_id = ?, position = ?, title = ? WHERE user_id = ?";
                // $values_sql .= ", ?, ?, ?";
                array_push($params,$department_id,$position, $title, $user_id);
                $params[0] = "sssissi";
            }
            
            // Complete the query and execute
            $stmt_admin_insert = $conn->prepare($admin_sql);
            $stmt_admin_insert->bind_param(...$params);

            if ($stmt_admin_insert->execute()) {
                $response->message = "Admin details was updated successfully";
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