<?php
// * API FOR FETCHING ALL DEPARTMENT
// TODO TEST

include_once '../../includes/db.php';
include '../../includes/header.php';


// Initialize response object
$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $user_id = $_SESSION['user_id'];
    // SQL query to fetch data
    $sql = "SELECT admin.*, users.email
            FROM admin
            LEFT JOIN users ON admin.user_id = users.user_id
            WHERE admin.user_id = '$user_id'";
    // Execute the query
    $result = $conn->query($sql);

    // Check if there are results
    if ($result->num_rows > 0) {
        // Initialize an array to store the rows
        $rows = array();

        // Fetch data from each row
        while ($row = $result->fetch_assoc()) {
            // Add the row to the array
            $rows[] = $row;
        }

        // Set response properties
        $response->success = true;
        $response->data = $rows;
    } else {
        // No results found
        $response->success = false;
        $response->message = "No data found";
    }

    // Convert response object to JSON format
    $json_response = json_encode($response);

    http_response_code(200);
    // Send JSON response
    echo $json_response;
} else {
    // Invalid request method
    $response->success = false;
    $response->message = "Invalid request method";

    // Convert response object to JSON format
    $json_response = json_encode($response);

    http_response_code(405); // Method Not Allowed
    // Send JSON response
    echo $json_response;
}
?>
