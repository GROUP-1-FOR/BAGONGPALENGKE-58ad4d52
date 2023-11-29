<?php
// remove_entries.php

require("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming the selected entry IDs are sent as an array
    $selectedIds = $_POST["selectedIds"];

    if (!empty($selectedIds)) {
        // Escape and sanitize the IDs to prevent SQL injection
        $escapedIds = array_map('mysqli_real_escape_string', array($connect), $selectedIds);

        // Create a comma-separated list of escaped IDs
        $idList = implode(",", $escapedIds);

        // Perform the delete operation in the database
        $deleteQuery = "DELETE FROM ven_payments WHERE id IN ($idList)";

        if (mysqli_query($connect, $deleteQuery)) {
            echo "Selected entries removed successfully.";
        } else {
            echo "Error removing entries: " . mysqli_error($connect);
        }
    } else {
        echo "No entries selected for removal.";
    }
} else {
    echo "Invalid request method.";
}
?>