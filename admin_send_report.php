<?php

require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

$sql_ticket_number = "SELECT MAX(ticket_number) AS max_ticket FROM report_bug";
$result = $connect->query($sql_ticket_number);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentTicketNumber = $row['max_ticket'] + 1;
} else {
    // If no existing ticket, start from 1
    $currentTicketNumber = 1;
}
$formattedTicketNumber = sprintf('%04d', $currentTicketNumber);

$connect->close();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report a Problem</title>
    <script>
        function validateForm() {
            var reportMessage = document.getElementById("admin_report_message").value;
            // Validate Report Message (not empty)
            if (reportMessage.trim() === "") {
                alert("Please enter a Report Message.");
                return false;
            }

            if (reportMessage.length > 255) {
                alert("Report Message should not exceed 255 characters.");
                return false;
            }

            return true; // Form is valid
        }
    </script>
</head>

<body>

    <h1 align="center">Report a Problem </h1>

    <form action="admin_send_report_1.php" method="post" onsubmit="return validateForm()">
        <label for="admin_report_ticket">Ticket No: </label>
        <input type="text" id="admin_report_ticket" name="admin_report_ticket" value="<?php echo $formattedTicketNumber; ?>" required readonly><br />

        <label for="admin_report_message">Report Message</label>
        <textarea name="admin_report_message" id="admin_report_message" cols="30" rows="5" required></textarea><br>

        <input type="submit" value="Submit">
    </form>

    <a href=admin_index.php>
        <h1>BACK</h1>
    </a>
</body>

</html>