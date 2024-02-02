<?php

require("config.php");
require("admin_check_login.php");

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
        function validateForm() {
            var message = document.getElementById("admin_report_message").value;

            document.getElementById("error_message").innerHTML = "";

            if (message.trim() === "") {
                document.getElementById("error_message").innerHTML = "Message is required";
                return false;
            } else if (message.length > 500) {
                document.getElementById("error_message").innerHTML = "Message cannot exceed 500 characters";
                return false;
            }

            return true;
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

    <h1 align="center">Report a Problem </h1>

    <form action="admin_send_report_1.php" method="post" onsubmit="return validateForm()">
        <label for="admin_report_ticket">Ticket No: </label>
        <input type="text" id="admin_report_ticket" name="admin_report_ticket" value="<?php echo $formattedTicketNumber; ?>" required readonly><br />

        <label for="admin_report_message">Report Message</label>
        <textarea name="admin_report_message" id="admin_report_message" cols="30" rows="5" required maxlength="500" oninput="updateCounter('admin_report_message', 'message_counter', 500)"></textarea>
        <span id="message_counter" class="counter">0/500</span>
        <span id="error_message" class="error"></span><br>

        <input type="submit" value="Submit">
    </form>

    <a href=admin_index.php>
        <h1>BACK</h1>
    </a>
</body>

</html>