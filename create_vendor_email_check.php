<?php
require("config.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];


    $email = $_POST['email'];

    // Prepare and execute the query
    $stmt = $connect->prepare("SELECT vendor_email FROM vendor_sign_in WHERE vendor_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the email is already taken
    $emailTaken = $result->num_rows > 0;

    echo json_encode(['emailTaken' => $emailTaken]);

    $stmt->close();


    $connect->close();
}
