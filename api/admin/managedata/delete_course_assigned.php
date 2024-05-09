<?php
// * API FOR DELETING COURSE

include_once '../../../includes/db.php';
include '../../../includes/header.php';

// Set response content type
header("Content-Type: multipart/form-data");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Validate and sanitize input
    $courses_assigned_id = $_POST['courseAssignedId'];

    if ($courses_assigned_id) {
        // Prepare and execute SQL delete query
        $query = "DELETE FROM courses_assigned WHERE courses_assigned_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $courses_assigned_id);

        if ($stmt->execute()) {
            // Send success response
            http_response_code(200);
            echo json_encode(array("message" => "Successfully unassigned the course"));
        } else {
            // Send error response
            http_response_code(500); // Internal Server Error
            echo json_encode(array("message" => "Failed to unassign the course"));
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
