<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];


    //to know last log in time of admin
    include('admin_login_time.php');

?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Main Page</title>
    </head>

    <body>
        <h1>Welcome, <?php echo $admin_userid ?>! </h1>


        <a href=admin_confirmpay.php>
            <h1>Confirm Payment</h1>
        </a>

        <a href=''>
            <h1>View Market Rental Stalls</h1>
        </a>

        <a href="admin_send_announcement.php">
            <h1>Send Announcements</h1>
        </a>


        <a href=admin_vendor_manage_accounts.php>
            <h1>Manage Vendor Accounts</h1>
        </a>

        <a href=admin_messages_preview.php>
            <h1>Messages</h1>
        </a>

        <a href=admin_feature_6.php>
            <h1>Feature 6</h1>
        </a>

        <a href=admin_logout.php>
            <h1>LOGOUT</h1>
        </a>
    </body>


    </html>
<?php } else {
    header("location:admin_login.php");
}
