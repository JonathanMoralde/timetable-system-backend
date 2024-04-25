<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
// * API FOR SIGN IN

include_once '../../includes/db.php';
include '../../includes/header.php';

header("Content-Type: multipart/form-data");

// Initialize response object
$response = new stdClass();

if($_SERVER["REQUEST_METHOD"] == "POST"){


    // ! USING FORM DATA FORMAT
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']) ? true : false; // Check if "Remember Me" is checked


    $query = "SELECT * FROM users WHERE email = ?";
     $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // check if theres an entry with the same id_num
    if($result->num_rows > 0){
          $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        // Verify password
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['type'] = $row['type'];

                 // Check if "Remember Me" is checked
                if ($remember_me) {
                    // Generate a random token
                    $token = bin2hex(random_bytes(32));
                    // Store the token in a cookie for 7 days
                    setcookie('remember_token', $token, time() + (7 * 24 * 60 * 60), '/');
                    // Store the token and expiration time in the database
                    $expiration = time() + (7 * 24 * 60 * 60); 
                }else {
                // Generate a random token
                $token = bin2hex(random_bytes(32));
                // Store the token in a cookie for 1 day
                setcookie('remember_token', $token, time() + (1 * 24 * 60 * 60), '/');
                // Store the token and expiration time in the database
                $expiration = time() + (1 * 24 * 60 * 60);
                }

                $query = "UPDATE users SET remember_token = ?, remember_token_expiration = ? WHERE email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sis", $token, $expiration, $email);
                $stmt->execute();

                // Check user role and redirect accordingly
                if ($row['type'] === 'admin') {
                    // create an object to be sent in JSON format for frontend
                    $response->message = "Successfully logged in";
                    $response->type = "admin";
                    $response->email = $row['email'];
                    $response->token = $token ?? ''; // Include the token in the response
                    $response->user_id = $_SESSION['user_id'];
                    http_response_code(200);
                    echo json_encode($response);
                    exit();
                }else if($row['type' === 'instructor']){
                    $response->message = "Successfully logged in";
                    $response->type = "instructor";
                    $response->email = $row['email'];
                    $response->token = $token ?? ''; // Include the token in the response
                    http_response_code(200);
                    echo json_encode($response);
                    exit();
                } else { 
                    // create an object to be sent in JSON format for frontend
                    $response->message = "Successfully logged in";
                    $response->type = "student";
                    $response->email = $row['email'];
                    $response->token = $token ?? ''; // Include the token in the response
                    http_response_code(200);
                    echo json_encode($response);
                    exit();
                 }
                 } else {
                // Invalid password
                $response->error = "Invalid password";
                http_response_code(401);
                    echo json_encode($response);
            }
    }else{
        // Invalid id_num
                $response->error = "Email is not registered yet";
                http_response_code(401);
                    echo json_encode($response);
    }
}else{
    http_response_code(501);
    echo json_encode($conn->error);
}


