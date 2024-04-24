<?php
// * API FOR CHANGING PASSWORD IN SETTINGS

include_once '../../includes/db.php';
include '../../includes/header.php';
header("Content-Type: multipart/form-data");

// Initialize response object
$response = new stdClass();

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ! USING FORM DATA FORMAT
    $user_id = $_SESSION['user_id'];
    $old_password = $_POST['oldPassword'];
    $new_password = $_POST['newPassword'];


     // Verify the old password before updating
    if (verify_password($user_id, $old_password)) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Prepare the update statement
        $update_query = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $update_query);

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            $response->message = "Password updated successfully";
            http_response_code(200); 
            echo json_encode($response);
        } else {
            http_response_code(500); // Internal Server Error
            $response->error = "Error updating password: " . mysqli_error($conn);
            echo json_encode($response);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        http_response_code(401); // Unauthorized
        $response->error = "Incorrect old password";
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