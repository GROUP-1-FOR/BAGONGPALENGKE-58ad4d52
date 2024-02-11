    <?php
    // gcash_pay.php

    // Include the configuration file
    require("config.php");

    if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
        $userid = $_SESSION["userid"];

        // Retrieve data from vendor_balance table
        $sqlGetBalance = "SELECT vendor_name, balance, transaction_id FROM vendor_balance WHERE vendor_userid = ?";
        $stmtGetBalance = $connect->prepare($sqlGetBalance);
        $stmtGetBalance->bind_param('s', $userid);
        $stmtGetBalance->execute();
        $stmtGetBalance->bind_result($vendorName, $balance, $transactionId);
        $stmtGetBalance->fetch();
        $stmtGetBalance->close();

        if (isset($_POST['pay_button'])) {
            // Process payment
            $paymentDate = date('Y-m-d H:i:s');
            $paymentMethod = "GCASH";

            // Insert payment details into ven_payments table
            $sqlInsertPayment = "INSERT INTO ven_payments (vendor_userid, vendor_name, balance, archived, confirmed, payment_date, mop, transaction_id) VALUES (?, ?, ?, 0, 0, ?, ?, ?)";
            $stmtInsertPayment = $connect->prepare($sqlInsertPayment);
            $stmtInsertPayment->bind_param('ssdsss', $userid, $vendorName, $balance, $paymentDate, $paymentMethod, $transactionId);
            $stmtInsertPayment->execute();
            $stmtInsertPayment->close();

            // Insert notification into admin_notification table
            $notifTitle = "Payment Confirmation Request";
            $confirmValue = 1;

            $sqlInsertNotification = "INSERT INTO admin_notification (vendor_userid, vendor_name, transaction_id, title, confirm, mop, notif_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtInsertNotification = $connect->prepare($sqlInsertNotification);
            $stmtInsertNotification->bind_param('ssssiss', $userid, $vendorName, $transactionId, $notifTitle, $confirmValue, $paymentMethod, $paymentDate);
            $stmtInsertNotification->execute();
            $stmtInsertNotification->close();

            // Redirect or perform additional actions after successful payment
            header("Location: vendor_index.php");
            exit();
        }
    ?>

        <!DOCTYPE html>
        <html>

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" type="text/css" href="index.css">
            <link rel="stylesheet" type="text/css" href="overlay.css">
            <link rel="stylesheet" type="text/css" href="text-style.css">
            <link rel="stylesheet" type="text/css" href="box-style.css">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
            <style>
                body {
                    text-align: center;
                    margin: 50px;
                    background-color: #f2f2f2;
                }

                #invoice-summary {
                    width: 50%;
                    margin: auto;
                    border: 3px solid #ccc;
                    padding: 20px;
                    background-color: #fff;
                }

                #invoice-summary h2 {
                    color: #850F16;
                }

                #invoice-summary p {
                    font-size: 1.2em;
                    margin: 10px 0;
                }

                #payment-buttons {
                    margin-top: 20px;
                }

                #payment-buttons button {
                    margin: 10px;
                    padding: 10px;
                    background-color: #850F16;
                    color: #fff;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                }
            </style>
        </head>

        <body>
            <header class="header2"></header>
            <?php include 'sidebar.php'; ?>




            <div id="invoice-summary">


                <div id="payment-confirmation">
                    <h2>YOU ARE ABOUT TO PAY</h2>
                    <p>Amount</p>
                    <p>PHP <?php echo number_format($balance, 2); ?></p>
                    <p>Total</p>
                    <form id="payment-buttons" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return confirmPayment()">
                        <button class="gcash-button gcb1" type="submit" name="pay_button">PAY</button>
                        <input type="hidden" name="payment_confirmed" value="1">
                        <button class="gcash-button gcb1" type="button" onclick="window.location.href='vendor_invoice_summary.php'">Cancel</button>
                    </form>
                </div>


            </div>

            <footer></footer>
            <script>
                function confirmPayment() {
                    // Display a confirmation dialog
                    var confirmation = confirm("Are you sure you want to proceed with the payment?");

                    // If user clicks OK, set the hidden input value to 1
                    if (confirmation) {
                        document.getElementsByName("payment_confirmed")[0].value = "1";
                    }

                    // If user clicks OK, the form will be submitted; otherwise, it will be canceled
                    return confirmation;
                }
            </script>
        </body>

        </html>
    <?php
    } else {
        header("location: vendor_logout.php");
    }
    ?>