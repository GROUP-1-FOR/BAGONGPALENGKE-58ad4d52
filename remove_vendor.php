<?php
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendorId = $_POST['vendorId'];
    $vendorName = $_POST['vendorName'];

    // Perform removal from vendor_sign_in
    $removeSql = "INSERT INTO vendor_archives SELECT * FROM vendor_sign_in WHERE vendor_userid = ?";
    $removeStmt = $connect->prepare($removeSql);
    $removeStmt->bind_param("s", $vendorId);
    $removeResult = $removeStmt->execute();
    $removeStmt->close();

    // If removal is successful, insert into vendor_archives
    if ($removeResult) {
        $archiveSql = "DELETE FROM vendor_sign_in WHERE vendor_userid = ?";
        $archiveStmt = $connect->prepare($archiveSql);
        $archiveStmt->bind_param("s", $vendorId);
        $archiveResult = $archiveStmt->execute();
        $archiveStmt->close();

        if ($archiveResult) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error archiving vendor']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Error removing vendor']);
    }
}

$connect->close();
?>