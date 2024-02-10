<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];
} else {
    header("location:vendor_logout.php");
}


$queryTransaction = "SELECT transaction_id, vendor_userid, vendor_name, balance, payment_date, mop 
    FROM paid_records 
    WHERE vendor_userid = '$userid' 
    ORDER BY payment_date DESC";

$result = mysqli_query($connect, $queryTransaction);

if (!$result) {
    die('Error: Unable to fetch data from the database');
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Transaction History</title>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="text-style.css">
    <link rel="javascript" type="text/script" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">



    <style>
    </style>
</head>

<body>
    <header class="header2"></header>
    <?php include 'sidebar2.php'; ?>
    <div class="flex-row-body">
        <h2 class="payment-header">TRANSACTION HISTORY</h2>

        <div class="hr"></div>

        <div class="confirm-payment-container">
            <table>
                <div class="flex-box1">

                    <div class="main-container-message">

                        <?php
                        while ($row = mysqli_fetch_assoc($result)) {


                            echo '<div class="flex-box-column2">';
                            echo "<h2 class='price'> Paid a Rent </h2>";


                            echo "<div class='flex-box-row1'>";
                            echo "<p class='subtitle-text'>Transaction ID: </p>"; //TEXT
                            echo "<p class='sub-text' >{$row['transaction_id']}</h3>";
                            echo "</div>";

                            // Check if the payment is confirmed and archived

                            echo "<div class='button-container3'>";
                            echo "<button class='confirm-button' onclick=\"viewDetails('{$row['transaction_id']}')\">View Details</button>";
                            echo "</div>";


                            // echo "<p class='subtitle-text2'>Payment Date: </p>"; //TEXT
                            // echo "<div class='date-container'>";
                            // echo "<p class='sub-text2' >{$row['payment_date']}</p>";;
                            // echo "</div>";

                            // echo "<div class='flex-box-row'>";
                            // echo "<p class='subtitle-text'>Mode of Payment:</p>";
                            // echo "<p class='sub-text'>{$row['mop']}</p>";
                            // echo "</div>";
                            echo "<br>";
                            echo "</div>";
                            echo "<div class='hr'></div>";
                        }
                        ?>
            </table>

            <script>
                function viewDetails(transactionId) {
                    // Redirect to the view_details.php page with the transaction ID
                    window.location.href = 'vendor_transaction_history_1.php?transaction_id=' + transactionId;
                }
            </script>
            <?php
            // Close the database connection
            mysqli_close($connect);
            ?>
        </div>
    </div>


    <footer></footer>

    </div>
</body>


</html>