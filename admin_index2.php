<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    include('admin_login_time.php');

    $sql = "SELECT admin_name FROM admin_sign_in WHERE admin_userid = '$admin_userid'";

    $result = $connect->query($sql);
    $admin_name = "";
    $admin_name_error = "";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $admin_name = $row['admin_name'];
        }
    } else {
        $admin_name_error = "No results found for user ID $admin_userId";
    }

    $query = "SELECT vendor_name, vendor_stall_number, MAX(latest_timestamp) as latest_timestamp FROM (
        SELECT vendor_name, vendor_stall_number, vendor_timestamp as latest_timestamp
        FROM vendor_messages
        UNION
        SELECT vendor_name, vendor_stall_number, admin_timestamp as latest_timestamp
        FROM admin_messages
    ) as combined_messages
    GROUP BY vendor_name, vendor_stall_number
    ORDER BY latest_timestamp DESC";

    $result = $connect->query($query);
    if (!$result) {
        die("Error executing the query: " . $connect->error);
    }
?>

    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> Homepage </title>
        <link rel="stylesheet" type="text/css" href="index.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    </head>

    <body>
        <header>
            <img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src">
        </header>
        <div class="main-sidebar">
            <ul class="sidebar-outside">
                <div class="profile-container">
                    <img class="profile-pic-holder" src="assets\images\sign-in\profile-pic.svg">
                    <img class="profile-design" src="assets\images\sign-in\profile-design.png">
                    <p class="vendor-name">Welcome, <?php echo $admin_name; ?>! </p>
                </div>
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
        <h3 class="announcement-text"> Announcement</h3>
        <h3 class="messages-text"> Messages</h3>
        <div class="flex-box">
            <main class="main-container">
                <div class="dashboard-announcement">

                    <?php
                    $sql_sent_announcement = "SELECT DISTINCT announcement_text, announcement_time FROM announcements ORDER BY announcement_id DESC";
                    $result_sent_announcement = $connect->query($sql_sent_announcement);

                    $connect->close();

                    if ($result_sent_announcement->num_rows > 0) {
                        while ($row = $result_sent_announcement->fetch_assoc()) {

                            echo "<div class='announcement-container'>";
                            echo "<p class='announcement-datetime'>Date and Time: " . $row['announcement_time'] . "</p>";
                            echo "<h1 class='title-holder'>Announcement Title Placeholder</h1>";
                            echo "<h1 class='subtitle-holder'>Announcement Subject Placeholder</h1>";
                            echo "<div><p class='announcement'>" . $row['announcement_text'] . "</p></div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No sent announcements found.</p>";
                    }
                    ?>
                </div>
                <div class="sending-message">
                    <form action="admin_send_announcement_1.php" method="post">
                        <label class="title-subject" for="Market Vendors">TITLE:</label><br>
                        <label class="title-subject" for="Market Vendors">SUBJECT:</label>
                        </select><br>
                        <label for="admin_announcement"></label>
                        <textarea class="text-box" name="admin_announcement" id="admin_announcement" cols="30" rows="5" placeholder="Write Something here... " required></textarea><br>
                        <input class="sending-button" type="submit" value="Send">
                    </form>


                </div>

                <div class="dashboard-message">

                </div>





                <!-- <a href=admin_index.php> THIS IS THE BACK BUTTON
                <h1>BACK</h1>
            </a> -->
            </main>
        </div>
        </div>
        <div class="flex-box">
            <div class="dashboard-map"></div>
            <div class="dashboard-payment-notif"></div>
            <div class="dashboard-notif"></div>
        </div>
        <footer></footer>
    </body>

    </html>

<?php
} else {
    header("location:admin_login.php");
}
?>