<!-- view_details.php -->
<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];

    if (isset($_GET['transaction_id'])) {
        $transactionId = $_GET['transaction_id'];

        // Query to get details based on the transaction ID
        $queryDetails = "SELECT * FROM paid_records WHERE transaction_id = '$transactionId'";
        $resultDetails = mysqli_query($connect, $queryDetails);

        if (!$resultDetails) {
            die('Error: Unable to fetch details from the database');
        }

        $details = mysqli_fetch_assoc($resultDetails);
    } else {
        // Redirect to an error page or handle the absence of a transaction ID
        header("Location: error.php");
        exit();
    }
?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>View Receipt</title>
        <style>
            body {
                text-align: center;
                margin: 50px;
                background-color: #f2f2f2;
            }

            #receipt-details {
                width: 50%;
                margin: auto;
                border: 3px solid #ccc;
                padding: 20px;
                background-color: #fff;
            }

            #receipt-details h1 {
                color: #850F16;
            }

            #receipt-details p {
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

            /* Media query to hide the buttons when printing */
            @media print {

                #payment-buttons,
                #back-link {
                    display: none;
                }
            }
        </style>
    </head>

    <body>

        <div id="receipt-details">
            <h1>RECEIPT</h1>
            <div>
                <p>Receipt No: <?php echo $details['receipt_number']; ?></p>
                <p>Transaction ID: <?php echo $details['transaction_id']; ?></p>
                <p>Date: <?php echo $details['payment_date']; ?></p>
                <p>Vendor Name: <?php echo $details['vendor_name']; ?></p>
                <p>Vendor User ID: <?php echo $details['vendor_userid']; ?></p>
                <p>Balance: <?php echo $details['balance']; ?></p>
                <p>Mode of Payment: <?php echo $details['mop']; ?></p>
                <p>Received By: <?php echo $details['admin_userid']; ?></p>
                <!-- Add more details as needed -->
            </div>

            <div id="payment-buttons">
                <button onclick="window.print()">Print Receipt</button>
            </div>
        </div>

        <a href=vendor_transaction_history.php id="back-link">
            <h1>Back</h1>
        </a>
    </body>

    </html>

<?php
    // Close the database connection
    mysqli_close($connect);
} else {
    header("location:vendor_logout.php");
}
?>