<?php
require_once "config.php";

if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];

    // Fetch suggestions from the database
    $sql = "SELECT vendor_userid, vendor_name FROM vendor_sign_in WHERE vendor_name LIKE '%$searchTerm%'";
    $result = $connect->query($sql);

    $suggestions = array();
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }

    // Return the suggestions as JSON
    header('Content-Type: application/json');
    echo json_encode($suggestions);
} else {
    // Invalid request
    http_response_code(400);
    echo "Invalid request";
}

// Close the database connection
$connect->close();
?>