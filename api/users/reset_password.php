<?php
// * API FOR CHANGING PASSWORD FROM FORGOT PASSWORD

include_once '../../includes/db.php';
include '../../includes/header.php';
header("Content-Type: multipart/form-data");

// Initialize response object
$response = new stdClass();

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ! USING FORM DATA FORMAT
    $email = mysqli_escape_string($conn, $_POST['email']);
    $new_password = mysqli_escape_string($conn, $_POST['newPassword']);
    $token = mysqli_escape_string($conn, $_POST['passwordToken']);

    if(empty($token)){
        http_response_code(500); 
    $response->error = "No token available";
    echo json_encode($response);
}

if(empty($email) && $empty($new_password)){
    http_response_code(500); 
    $response->error = "Please fill up all the required fields";
    echo json_encode($response);
    }

    

                $check_token = "SELECT verify_token FROM users WHERE verify_token='$token' LIMIT 1";
                $check_token_run = mysqli_query($conn,$check_token);

                if(mysqli_num_rows($check_token_run) > 0){
                              
                                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                                     $update_password_query = "UPDATE users SET password='$hashed_password' WHERE verify_token='$token' LIMIT 1";
                                        $update_password_run = mysqli_query($conn, $update_password_query);

                                        if($update_password_run){
                                            $new_token = md5(rand());
                                            $update_token = "UPDATE users SET verify_token='$new_token' WHERE verify_token='$token' LIMIT 1";
                                            $update_token_run = mysqli_query($conn, $update_token);

                                            if($update_token_run){
                                               $response->message = "Password updated successfully";
                                                http_response_code(200); 
                                                echo json_encode($response);
                                            } else {
                                                http_response_code(500); 
                                                $response->error = "Failed to update Password";
                                                echo json_encode($response);
                                            }
                                        } else {
                                            http_response_code(500); 
                                                $response->error = "Error updating password";
                                                echo json_encode($response);
                                        }

                               
                }else {
                    http_response_code(500); 
                                                $response->error = "Invalid token";
                                                echo json_encode($response);
                }




    
} else {
    http_response_code(405); // Method Not Allowed
    $response->error = "Method Not Allowed";
    echo json_encode($response);
}



// Function to verify user password
function verify_password($user_id, $password) {
    global $conn;

    // Prepare the query
    $query = "SELECT password FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "i", $user_id);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Bind result variables
    mysqli_stmt_bind_result($stmt, $hashed_password);

    // Fetch the result
    mysqli_stmt_fetch($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);

    // Verify the provided password against the hashed password
    if ($hashed_password && password_verify($password, $hashed_password)) {
        // Password is correct
        return true;
    } else {
        // Password is incorrect
        return false;
    }
}