<?php
// * API FOR CHECKING IF USER IS STILL LOGGED IN
// TODO TEST


include_once '../../includes/db.php';
include '../../includes/header.php';
// header("Content-Type: application/json; charset=UTF-8");

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    // Check if the user is logged in
    if (!empty($_SESSION['user_id'])) {
        // User is logged in
        // Return JSON response indicating the user is logged in
        http_response_code(200);
        echo json_encode(array("logged_in" => true, "user_id" => $_SESSION['user_id'], "type" => $_SESSION['type']));
    } else {
        // User is not logged in, check for remember me token
        if (!empty($_COOKIE['remember_token'])) {
            // Look up token in database
            $token = $_COOKIE['remember_token'];
            $query = "SELECT * FROM users WHERE remember_token = ? AND remember_token_expiration > ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $token, time());
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // User is authenticated via remember me token
                // Retrieve user information
                $row = $result->fetch_assoc();
                $_SESSION['user_id'] = $row['user_id'];
                // $_SESSION['id_num'] = $row['id_num'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['type'] = $row['type'];
                http_response_code(200);
                echo json_encode(array("logged_in" => true, "user_id" => $_SESSION['user_id'], "type" => $_SESSION['type']));
            } else {
                // No valid remember me token found or it's expired, user is not logged in
                // Clear the expired remember me token from the cookie
                setcookie('remember_token', '', time() - 3600, '/');
                http_response_code(200); // Unauthorized
                echo json_encode(array("logged_in" => false));
            }
        } else {
            // No remember me token found, user is not logged in
            http_response_code(200); // Unauthorized
            echo json_encode(array("logged_in" => false));
        }
    }
}
?>
