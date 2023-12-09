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
        <!-- Add your styles or include a separate CSS file -->
    </head>

    <body>

        <h1>RECEIPT</h1>

        <div>
            <p>Date: <?php echo $details['payment_date']; ?></p>
            <p>Transaction ID: <?php echo $details['transaction_id']; ?></p>
            <p>Vendor User ID: <?php echo $details['vendor_userid']; ?></p>
            <p>Vendor Name: <?php echo $details['vendor_name']; ?></p>
            <p>Receipt No: <?php echo $details['receipt_number']; ?></p>
            <p>Received By: <?php echo $details['admin_userid']; ?></p>
            <p>Balance: <?php echo $details['balance']; ?></p>
            <p>Mode of Payment: <?php echo $details['mop']; ?></p>
            <!-- Add more details as needed -->
        </div>


        <button onclick="window.print()">Print Receipt</button>


        <a href=vendor_transaction_history.php>
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