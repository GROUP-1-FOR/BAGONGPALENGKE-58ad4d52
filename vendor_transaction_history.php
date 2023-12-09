<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];

    $queryTransaction = "SELECT transaction_id, vendor_userid, vendor_name, balance, payment_date, mop 
    FROM paid_records 
    WHERE vendor_userid = '$userid' 
    ORDER BY payment_date DESC";;

    $result = mysqli_query($connect, $queryTransaction);

    if (!$result) {
        die('Error: Unable to fetch data from the database');
    }




?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Vendor Transaction History</title>
        <style>
            .transaction-record {
                display: flex;
                align-items: center;
                justify-content: space-between;
                border: 1px solid #dddddd;
                padding: 8px;
                margin-bottom: 10px;
            }

            .paid-message {
                font-weight: bold;
                /* Make the message bold or customize styling as needed */
            }

            .transaction-details {
                margin-right: 10px;
            }

            .action-button {
                padding: 5px;
            }
        </style>
    </head>

    <body>

        <h1>Vendor Transaction Details</h1>

        <?php
        // Loop through the result set and display each record
        while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <div class='transaction-record'>
                <div class='transaction-details'>
                    <p style="color:green ;">Paid a Rent</p>
                    <p>Transaction ID: <?php echo $row['transaction_id']; ?></p>
                </div>
                <button class='action-button' onclick="viewDetails('<?php echo $row['transaction_id']; ?>')">View Details</button>
            </div>
        <?php
        }
        ?>

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

        <a href=vendor_index.php>
            <h1>Back</h1>
        </a>


    </body>


    </html>
<?php
} else {
    header("location:vendor_logout.php");
}
?>