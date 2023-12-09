<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $admin_report_ticket = htmlspecialchars($_POST['admin_report_ticket']);
        $admin_report_message = trim(htmlspecialchars($_POST['admin_report_message']));

        //server-side validation
        if (empty($admin_report_message)) {
            echo '<script>';
            echo 'alert("Empty Message!");';
            echo 'window.location.href = "admin_send_report.php";';
            echo '</script>';
        } elseif (strlen($admin_report_message) > 255) {
            echo '<script>';
            echo 'alert("Report Message Too Long!");';
            echo 'window.location.href = "admin_send_report.php";';
            echo '</script>';
        }

        $sql_fetch_user_details = "SELECT admin_name FROM admin_sign_in";
        $result_fetch_user_details = $connect->query($sql_fetch_user_details);

        // Check if the query was successful
        if ($result_fetch_user_details) {
            // Fetch data row by row
            while ($row = $result_fetch_user_details->fetch_assoc()) {
                // Access individual columns using $row['column_name']
                $admin_name = $row['admin_name'];
            }

            // Free result set
            $result_fetch_user_details->free_result();
        } else {
            echo "Error: " . $sql_fetch_user_details . "<br>" . $connect->error;
        }


        // Insert the report details
        $sql = "INSERT INTO report_bug (admin_userid,admin_name, ticket_number, report_message) VALUES ('$admin_userid', '$admin_name', '$admin_report_ticket','$admin_report_message')";
        if ($connect->query($sql) !== TRUE) {
            echo '<script>';
            echo 'alert("Error sending report to developers!");';
            echo 'window.location.href = "admin_index.php";';
            echo '</script>';
        }
        echo '<script>';
        echo 'alert("Report Sent to Developers!");';
        echo 'window.location.href = "admin_index.php";';
        echo '</script>';
    }
    $connect->close();
} else {
    header("location:admin_logout.php");
    exit();
}
