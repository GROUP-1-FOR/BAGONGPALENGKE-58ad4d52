<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    if (isset($_POST['send'])) {
        $admin_id = $_SESSION["id"];
        $admin_userid = $_SESSION["userid"];
        $receiver_id = $_POST['receiver'];
        $message_content = $_POST['message'];

        // Insert message into the database
        $sqlInsertMessage = "INSERT INTO messages (sender_id, receiver_id, message_content) VALUES (?, ?, ?)";
        $stmtInsertMessage = $connect->prepare($sqlInsertMessage);
        $stmtInsertMessage->bind_param('iis', $admin_id, $receiver_id, $message_content);
        $stmtInsertMessage->execute();

        header("Location: admin_messages.php");
        exit();
    } else {
        header("Location: admin_messages.php");
        exit();
    }
} else {
    header("Location: admin_login.php");
    exit();
}
?>