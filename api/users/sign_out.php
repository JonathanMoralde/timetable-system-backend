<?php
// * API FOR SIGN OUT

include_once '../../includes/db.php';
include '../../includes/header.php';
header("Content-Type: application/json; charset=UTF-8");

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("error" => "Method Not Allowed"));
    exit;
}else{

    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
         // Clear the remember me token cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }

         // Clear remember_token and remember_token_expiration in the database
        $user_id = $_SESSION['user_id'];
        $query = "UPDATE users SET remember_token = NULL, remember_token_expiration = NULL WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Unset all of the session variables
        $_SESSION = array();
    
        // Destroy the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    
    
        // Destroy the session
        session_destroy();
    
        http_response_code(200);
        echo json_encode(array("message" => "Signed out successfully!"));
    } else {
        // If the user is not logged in, redirect them to the login page or any other desired page
        http_response_code(401);
        echo json_encode(array("error" => "Already signed out!"));
    }
}