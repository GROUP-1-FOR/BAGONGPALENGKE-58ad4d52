<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    if (isset($_POST['send'])) {
        $vendor_id = $_SESSION["id"];
        $vendor_userid = $_SESSION["userid"];
        $receiver_id = $_POST['receiver'];
        $message_content = $_POST['message'];

        // Insert message into the database
        $sqlInsertMessage = "INSERT INTO messages (sender_id, receiver_id, message_content) VALUES (?, ?, ?)";
        $stmtInsertMessage = $connect->prepare($sqlInsertMessage);
        $stmtInsertMessage->bind_param('iis', $vendor_id, $receiver_id, $message_content);
        $stmtInsertMessage->execute();

        header("Location: vendor_messages.php");
        exit();
    } else {
        header("Location: vendor_messages.php");
        exit();
    }
} else {
    header("Location: vendor_login.php");
    exit();
}
?>