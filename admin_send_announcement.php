<?php

require("config.php");
require("admin_check_login.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Announcement</title>

    <style>
        .error {
            color: red;
        }

        .counter {
            font-size: 12px;
            color: gray;
        }
    </style>

    <script>
        function editAnnouncement(announcementId) {
            // Redirect to the edit page with the announcementId
            window.location.href = "admin_edit_announcement.php?id=" + announcementId;
        }

        function removeAnnouncement(announcementId) {
            // Redirect to the remove page with the announcementId
            var confirmed = confirm("Are you sure you want to remove this announcement?");
            if (confirmed) {
                window.location.href = "admin_remove_announcement.php?id=" + announcementId;
            }
        }

        function validateForm() {
            var title = document.getElementById("admin_announcement_title").value;
            var subject = document.getElementById("admin_announcement_subject").value;
            var message = document.getElementById("admin_announcement").value;
            var date = document.getElementById("admin_announcement_time").value;

            document.getElementById("error_title").innerHTML = "";
            document.getElementById("error_subject").innerHTML = "";
            document.getElementById("error_message").innerHTML = "";
            document.getElementById("error_date").innerHTML = "";

            var isValid = true;

            if (title.trim() === "") {
                document.getElementById("error_title").innerHTML = "Title is required";
                isValid = false;
            } else if (title.length > 50) {
                document.getElementById("error_title").innerHTML = "Title cannot exceed 50 characters";
                isValid = false;
            }

            if (subject.trim() === "") {
                document.getElementById("error_subject").innerHTML = "Subject is required";
                isValid = false;
            } else if (subject.length > 100) {
                document.getElementById("error_subject").innerHTML = "Subject cannot exceed 100 characters";
                isValid = false;
            }

            if (message.trim() === "") {
                document.getElementById("error_message").innerHTML = "Message is required";
                isValid = false;
            } else if (message.length > 500) {
                document.getElementById("error_message").innerHTML = "Message cannot exceed 500 characters";
                isValid = false;
            }

            if (date.trim() === "") {
                document.getElementById("error_date").innerHTML = "Date is required";
                isValid = false;
            }

            return isValid;
        }

        function updateCounter(inputId, counterId, maxLength) {
            var input = document.getElementById(inputId);
            var counter = document.getElementById(counterId);
            var currentChars = input.value.length;
            var remainingChars = maxLength - currentChars;

            counter.innerHTML = currentChars + '/' + maxLength;
        }
    </script>

</head>

<body>

    <h1>Welcome, <?php echo $admin_userid ?>! </h1>
    <?php
    $currentDate = date('Y-m-d');
    $sql_sent_announcement = "SELECT DISTINCT announcement_id, announcement_title, announcement_subject, announcement_text, announcement_time FROM announcements ORDER BY announcement_time DESC";
    $result_sent_announcement = $connect->query($sql_sent_announcement);
    if ($result_sent_announcement->num_rows > 0) {
        while ($row = $result_sent_announcement->fetch_assoc()) {
    ?>
            <div>
                <hr />
                <h1 style="color: green;"><?php echo $row['announcement_title']; ?> </h1>
                <h2 style="color: gray;"><?php echo $row['announcement_subject']; ?></h2>
                <p><?php echo $row['announcement_text']; ?></p>
                <p><?php echo $row['announcement_time']; ?></p>
                <button type="button" onclick="editAnnouncement(<?php echo $row['announcement_id']; ?>)">Edit</button>
                <button type="button" onclick="removeAnnouncement(<?php echo $row['announcement_id']; ?>)">Remove</button>
            </div>
    <?php
        }
    } else {
        echo "<p>No sent announcements found.</p>";
    }

    $connect->close();
    ?>

    <h1>Send an Announcement:</h1>

    <form action="admin_send_announcement_1.php" method="post" onsubmit="return validateForm()">
        <h2>Market Vendors</h2>

        <label for="admin_announcement_title">Announcement Title</label>
        <input type="text" id="admin_announcement_title" name="admin_announcement_title" required maxlength="50" oninput="updateCounter('admin_announcement_title', 'title_counter', 50)">
        <span id="title_counter" class="counter">0/50</span>
        <span id="error_title" class="error"></span><br />

        <label for="admin_announcement_subject">Announcement Subject</label>
        <input type="text" id="admin_announcement_subject" name="admin_announcement_subject" required maxlength="100" oninput="updateCounter('admin_announcement_subject', 'subject_counter', 100)">
        <span id="subject_counter" class="counter">0/100</span>
        <span id="error_subject" class="error"></span><br />

        <label for="admin_announcement">Announcement Message</label>
        <textarea name="admin_announcement" id="admin_announcement" cols="30" rows="5" required maxlength="500" oninput="updateCounter('admin_announcement', 'message_counter', 500)"></textarea>
        <span id="message_counter" class="counter">0/500</span>
        <span id="error_message" class="error"></span><br>

        <label for="admin_announcement_time">Announcement Date</label>
        <input type="date" id="admin_announcement_time" name="admin_announcement_time" min="<?php echo date('Y-m-d'); ?>" required onkeydown="return false">
        <span id="error_date" class="error"></span><br>

        <input type="submit" value="Send announcement">
    </form>

    <a href=admin_index.php>
        <h1>BACK</h1>
    </a>
</body>

</html>