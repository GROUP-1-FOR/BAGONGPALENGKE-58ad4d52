<?php

require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcement</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="javascript" type="text/script" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

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
    <header class="header2"></header>
    <?php include 'sidebar.php'; ?>


    <div class="flex-row">
        <h2 class="announcement-header">ANNOUNCEMENT</h2>
        <div class="dashboard-announcement2">

            <?php
            $currentDate = date('Y-m-d');
            $sql_sent_announcement = "SELECT DISTINCT announcement_id, announcement_title, announcement_subject, announcement_text, announcement_time FROM announcements ORDER BY announcement_id DESC";
            $result_sent_announcement = $connect->query($sql_sent_announcement);

            if ($result_sent_announcement->num_rows > 0) {
                while ($row = $result_sent_announcement->fetch_assoc()) {
            ?>
                    <div class="notification-box">

                        <div class="inside-notification">
                            <p class="announcement-datetime"><?php echo $row['announcement_time']; ?></p>
                            <p class="announcement-title"><?php echo $row['announcement_title']; ?> </p>
                            <p class="announcement-subject"><?php echo $row['announcement_subject']; ?></p>
                            <p class="announcement-content"><?php echo $row['announcement_text']; ?></p>

                            <div class="modify-buttons">
                                <!--     <button class="button-1" type="button" onclick="editAnnouncement(<?php //echo $row['announcement_id']; 
                                                                                                            ?>)">Edit</button> -->
                                <button class="button-2" type="button" onclick="removeAnnouncement(<?php echo $row['announcement_id']; ?>)">Remove</button>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p>No sent announcements found.</p>";
            }

            $connect->close();
            ?>
        </div>


        <div class="dashboard-announcement-box">
            <form class="form-style" action="admin_send_announcement_1.php" method="post" onsubmit="return validateForm()">
                <div class="flexbox-row2">
                    <label class="label-title" for="admin_announcement_title">Title:</label>
                    <input class="title-box" type="text" id="admin_announcement_title" name="admin_announcement_title" required maxlength="50" oninput="updateCounter('admin_announcement_title', 'title_counter', 50)">
                    <!-- <span id="title_counter" class="counter">0/50</span> -->
                    <span id="error_title" class="error"></span>
                </div>

                <div class="flexbox-row2">
                    <label class="label-subject" for="admin_announcement_subject">Subject:</label>
                    <input class="subject-box" type="text" id="admin_announcement_subject" name="admin_announcement_subject" required maxlength="100" oninput="updateCounter('admin_announcement_subject', 'subject_counter', 100)">
                    <!-- <span id="subject_counter" class="counter">0/100</span> --><br>
                </div>

                <div class="flexbox-row2">
                    <label class="label-date" for="admin_announcement_time">Date:</label>
                    <input class="calendar" type="date" id="admin_announcement_time" name="admin_announcement_time" min="<?php echo date('Y-m-d'); ?>" required onkeydown="return false" required>
                    <span id="error_subject" class="error"></span>
                </div>
                <textarea class="admin-announcement" placeholder=" Write Something..." name="admin_announcement" id="admin_announcement" cols="30" rows="5" required maxlength="500" oninput="updateCounter('admin_announcement', 'message_counter', 500)"></textarea>
                <!-- <span id="message_counter" class="counter">0/500</span> -->
                <span id="error_message" class="error"></span><br>



                <span id="error_date" class="error"></span>

                <input class="button-3" type="submit" value="Post">
            </form>

        </div>
    </div>

    <footer></footer>
</body>

</html>