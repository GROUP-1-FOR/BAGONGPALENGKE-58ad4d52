<?php
require("config.php");


if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
    exit();
}

if (isset($_GET['id'])) {
    $announcementId = $_GET['id'];


    // Retrieve announcement details based on the ID
    $sql = "SELECT * FROM announcements WHERE announcement_id = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("i", $announcementId);
    $stmt->execute();
    $result = $stmt->get_result();
    $announcement = $result->fetch_assoc();

    $stmt->close();
    $connect->close();
} else {
    // If the announcement ID is not set, redirect to the main page
    header("Location: admin_send_announcement.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement</title>
</head>

<body>

    <h1>Edit Announcement</h1>

    <form action="admin_update_edited_announcement.php" method="post">
        <input type="hidden" name="announcement_id" value="<?php echo $announcement['announcement_id']; ?>">

        <label for="announcement_title">Announcement Title</label>
        <input type="text" id="announcement_title" name="announcement_title" value="<?php echo $announcement['announcement_title']; ?>" required><br />

        <label for="announcement_subject">Announcement Subject</label>
        <input type="text" id="announcement_subject" name="announcement_subject" value="<?php echo $announcement['announcement_subject']; ?>" required><br />

        <label for="announcement_text">Announcement Text</label>
        <textarea name="announcement_text" id="announcement_text" cols="30" rows="5" required><?php echo $announcement['announcement_text']; ?></textarea><br>

        <label for="announcement_time">Announcement Date</label>
        <input type="date" id="announcement_time" name="announcement_time" value="<?php echo date('Y-m-d', strtotime($announcement['announcement_time'])); ?>" min="<?php echo date('Y-m-d'); ?>" required onkeydown="return false"><br>

        <input type="submit" value="Update Announcement">
    </form>

</body>

</html>