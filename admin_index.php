<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // To know the last log in time of admin
    // include('admin_login_time.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body class="solid-body">
    <header>
        <img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src">
    </header>

    <div class="sidebar">
        <ul class="sidebar-outside">
            <!-- <img class="profile-design" src="assets\images\sign-in\profile-design.png"> -->
            <img class="profile-pic-holder" src="assets\images\sign-in\profile-pic.svg">
        </ul>
        <div class="sidebar-inside">
            <ul class="dashboard-sidebar">
                <li><a class="home-index" href=admin_index.php> Home </a></li>
                <li><a class="manage-vendor" href=admin_vendor_manage_accounts.php> Manage Vendor Accounts </a></li>
                <li><a class="report-management" href="#"> Report Management </a></li>
                <li><a class="help-button" href="#"> Help </a></li>
            </ul>
        </div>
        <div>
            <a href=admin_logout.php>
                <h1 class="logout-button">LOGOUT</h1>
            </a>
        </div>
    </div>

    <div class="content">
        <h2 class="title-placement">Announcement</h2>

        <div class="super-container">
            <div class="button-container">
                <div class="announcement-container">

                    <?php
                    $sql_sent_announcement = "SELECT DISTINCT announcement_text, announcement_time FROM announcements ORDER BY announcement_id DESC";
                    $result_sent_announcement = $connect->query($sql_sent_announcement);

                    if ($result_sent_announcement->num_rows > 0) {
                        while ($row = $result_sent_announcement->fetch_assoc()) {
                            // Display sent announcements
                            echo "<div class='announcement-item'>";
                            echo "<p class='announcement-datetime'>" . $row['announcement_time'] . "</p>";
                            echo "Vendor Name Here";
                            echo "<p class='announcement-text'>Announcement: " . $row['announcement_text'] . "</p>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No sent announcements found.</p>";
                    }

                    $connect->close(); // Close the connection after fetching data
                    ?>
                </div>
                <form action="admin_send_announcement_1.php" method="post">
                    <label for="Market Vendors">To all Market Vendors:</label>
                    <select>
                        <!-- Add your options here -->
                    </select>
                    <label for="admin_announcement">Announcement Message</label>
                    <div class="submit-container">
                        <textarea class="text-box" name="admin_announcement" id="admin_announcement" cols="30" rows="5" required></textarea><br>
                    </div>
                    <input class="submit-button" type="submit" value="Send announcement">
                </form>
            </div>
            <div class="buttons">
                <div class="box"></div>
                <div class="box"></div>
                <div class="box"></div>
            </div>
        </div>
    </div>

    </body>
    <footer></footer>

</html>
<?php
} else {
    header("location:admin_login.php");
}
?>
