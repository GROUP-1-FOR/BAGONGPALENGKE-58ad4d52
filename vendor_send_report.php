<?php

require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $vendor_id = $_SESSION["id"];
    $vendor_userid = $_SESSION["userid"];
} else {
    header("location:vendor_logout.php");
}


$sql_ticket_number = "SELECT MAX(ticket_number) AS max_ticket FROM report_bug";
$result = $connect->query($sql_ticket_number);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentTicketNumber = intval($row['max_ticket']) + 1;
} else {
    // If no existing ticket, start from 1
    $currentTicketNumber = 1;
}
$formattedTicketNumber = sprintf('%04d', $currentTicketNumber);

$connect->close();


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGN IN</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="text-style.css">
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
        function validateForm() {
            var message = document.getElementById("vendor_report_message").value;

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
    <header class="header2"></header>
    <?php include 'sidebar2.php'; ?>

    <div class="flex-row">
        <h2 class="manage-account-header">REPORTS</h2>
        <div class="report-manage">
            <br>
            <h1 class="admin-name-report"><?php echo $vendor_userid  ?>! </h1>
            <br>


            <form action="vendor_send_report_1.php" method="post" onsubmit="return validateForm()">
                <div class="flex-box-row">

                    <label class="text-design1" for="admin_report_ticket"></label>
                    <p class="text-design" type="text" id="vendor_report_ticket" name="vendor_report_ticket" required readonly></p>

                </div>

                <div class="flex-box-row">
                    <!-- <input type="text" id="admin_report_ticket" name="admin_report_ticket" value="<//?php echo $formattedTicketNumber; ?>" required readonly> -->
                    <label class="text-design1" for="vendor_report_ticket">Ticket No: <?php echo $formattedTicketNumber; ?></label>
                    <input class="text-design" type="hidden" id="vendor_report_ticket" name="vendor_report_ticket" value="<?php echo $formattedTicketNumber; ?>">
                    <br />
                </div>

                <div>
                    <textarea class=" text-box-area" placeholder="Report message..." name="vendor_report_message" id="vendor_report_message" cols="30" rows="5" required maxlength="500" oninput="updateCounter('vendor_report_message', 'message_counter', 500)"></textarea>
                    <span class="text-counter1" id="message_counter" class="counter">0/500</span>
                    <span id="error_message" class="error"></span><br>
                    <center><input class="submit-button1" type="submit" value="Send"></center>
                </div>


            </form>
        </div>
        <div class="flex-box1">
        </div>

        <div class="main-container">
        </div>


    </div>


    <footer></footer>
</body>

</html>