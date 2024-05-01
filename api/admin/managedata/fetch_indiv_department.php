<?php
// * API FOR FETCHING INDIVIDUAL DEPARTMENT
// TODO TEST

include_once '../../../includes/db.php';
include '../../../includes/header.php';


// Initialize response object
$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $department_id = $_GET['departmentId'];

    // SQL query to fetch data
    $sql = "SELECT * 
            FROM department
            WHERE department_id = '$department_id'";

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
