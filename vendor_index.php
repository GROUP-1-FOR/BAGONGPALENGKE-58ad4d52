<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];


    //to know last log in time of vendor
    include('vendor_login_time.php');
    // Fetch user data using prepared statement
$sql = "SELECT * FROM vendor_sign_in WHERE vendor_userid = ?";
$stmt = $connect->prepare($sql);
$stmt->bind_param('s', $userid); // Use 's' for VARCHAR
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['vendor_name'];
    $balance = $row['balance'];
} else {
    // Handle the case where the user ID is not found or there's an issue with the database query
    die("User not found or database query issue.");
}

// Check the payment status
$paymentStatus = "To be paid";

// Check if the payment has been sent but not confirmed
$sqlCheckPayment = "SELECT * FROM ven_payments WHERE id = ? AND name = ? AND confirmed = 0 AND archived = 0";
$stmtCheckPayment = $connect->prepare($sqlCheckPayment);
$stmtCheckPayment->bind_param('is', $userid, $name);
$stmtCheckPayment->execute();
$resultCheckPayment = $stmtCheckPayment->get_result();

if ($resultCheckPayment->num_rows > 0) {
    $paymentStatus = "Payment has already been sent";
}

// Check if the payment is confirmed
$sqlCheckPaymentConfirmation = "SELECT * FROM ven_payments WHERE id = ? AND name = ? AND confirmed = 1 AND archived = 1";
$stmtCheckPaymentConfirmation = $connect->prepare($sqlCheckPaymentConfirmation);
$stmtCheckPaymentConfirmation->bind_param('is', $userid, $name);
$stmtCheckPaymentConfirmation->execute();
$resultCheckPaymentConfirmation = $stmtCheckPaymentConfirmation->get_result();

if ($resultCheckPaymentConfirmation->num_rows > 0) {
    $paymentStatus = "The payment is confirmed";
    // Set balance to 0 as payment is confirmed
    $balance = $row['balance'];
}

// Process payment if the "Pay" button is clicked
if (isset($_POST['pay']) && $paymentStatus === "To be paid" && $balance > 0) {
    // Insert payment data into ven_payments table
    $paymentDate = date('Y-m-d H:i:s');
    $sqlInsertPayment = "INSERT INTO ven_payments (id, name, balance, archived, confirmed, payment_date) VALUES (?, ?, ?, 0, 0, ?)";
    $stmtInsertPayment = $connect->prepare($sqlInsertPayment);
    $stmtInsertPayment->bind_param('ssds', $userid, $name, $balance, $paymentDate); // Use 's' for VARCHAR and 'd' for DOUBLE
    $stmtInsertPayment->execute();

    // Display a message or perform additional actions if needed
    echo "Payment request sent. Please wait for confirmation.";
} elseif ($balance <= 0) {
    // Display a message if the balance is not sufficient
    echo "Your balance is not sufficient to make a payment.";
}

?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Main Page</title>
        <style>
        body {
            text-align: center;
            margin: 50px;
            background-color: #f2f2f2;
        }

        #money-table {
            width: 70%;
            margin: auto;
            border-collapse: collapse;
            cursor: pointer;
        }

        #money-cell {
            border: 3px solid #ccc;
            padding: 50px;
            background-color: #850F16;
            color: white;
            font-size: 2em; /* Adjust the font size as needed */
        }

        #money-cell button {
            margin-top: 10px;
            padding: 40;
            background-color: gray;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }
    </style>
    </head>

    <body>
    <h1>Welcome, <?php echo $userid ?>! </h1>
    <table id="money-table">
        <tr>
            <td id="money-cell">
                <center>
                    <?php if ($balance > 0): ?>
                        $<?php echo number_format($balance, 2); ?>
                        <form method="post">
                            <button type="submit" name="pay" onclick="return confirm('Are you sure you want to make the payment?')">Pay</button>
                        </form>
                    <?php else: ?>
                        $<?php echo number_format($balance, 2); ?>
                    <?php endif; ?>
                    <?php if ($paymentStatus === "Payment has already been sent"): ?>
                        <p>Payment has already been sent. Wait for confirmation.</p>
                    <?php elseif ($paymentStatus === "The payment is confirmed"): ?>
                        <p>The payment is confirmed. You have no current balance.</p>
                    <?php endif; ?>
                </center>
            </td>
        </tr>
    </table>
    <br>

    <a href=vendor_view_announcement.php>
        <h1>SEE ANNOUNCEMENTS</h1>
    </a>
    <a href="vendor_messages_preview.php">
        <h1>MESSAGES</h1>
    </a>

    <a href=vendor_logout.php>
        <h1>LOGOUT</h1>
    </a>
</body>


    </html>
<?php } else {
    header("location:vendor_login.php");
}
