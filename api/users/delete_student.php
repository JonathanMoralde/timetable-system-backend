<?php
// * API FOR DELETING COURSE

include_once '../../includes/db.php';
include '../../includes/header.php';

// Set response content type
header("Content-Type: multipart/form-data");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Validate and sanitize input
    $student_id = $_POST['studentId'];
    $user_id = $_POST['userId'];

    if ($student_id) {
        // Prepare and execute SQL delete query
        $query = "DELETE FROM student WHERE student_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $student_id);

        if ($stmt->execute()) {

            $user_query = "DELETE FROM users WHERE user_id = ?";
            $user_stmt = $conn->prepare($user_query);
            $user_stmt->bind_param("i", $user_id);
            if($user_stmt->execute()){
                // Send success response
                http_response_code(200);
                echo json_encode(array("message" => "Successfully deleted the student"));

            }else{

                // Send error response
                http_response_code(500); // Internal Server Error
                echo json_encode(array("message" => "Failed to delete the student"));
            }
        } else {
            // Send error response
            http_response_code(500); // Internal Server Error
            echo json_encode(array("message" => "Failed to delete the student"));
        }

        // Close statement
        $stmt->close();
    } else {
        // Send invalid input response
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "Invalid input data"));
    }
} else {
    // Send method not allowed response
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "Invalid request method"));
}
?>
