<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];


    $query = "SELECT vendor_userid, vendor_name, balance, confirmed, archived, payment_date, mop, transaction_id FROM ven_payments";
    // Only select necessary columns
    $result = mysqli_query($connect, $query);

    if (!$result) {
        die('Error: Unable to fetch data from the database');
    }
    if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
        $admin_id = $_SESSION["id"];
        $admin_userid = $_SESSION["userid"];
    } else {
        header("location:admin_logout.php");
    }

    // Query to get admin_name based on admin_userid
    $adminNameQuery = "SELECT admin_name FROM admin_sign_in WHERE admin_userid = '$admin_userid'";
    $adminNameResult = mysqli_query($connect, $adminNameQuery);

    if (!$adminNameResult) {
        die('Error: Unable to fetch admin_name from the database');
    }

    $adminRow = mysqli_fetch_assoc($adminNameResult);
    $admin_name = $adminRow['admin_name'];


    $query = "SELECT vendor_userid, vendor_name, balance, confirmed, archived, payment_date, mop, transaction_id FROM ven_payments";
    // Only select necessary columns
    $result = mysqli_query($connect, $query);

    if (!$result) {
        die('Error: Unable to fetch data from the database');
    }
?>


    <!DOCTYPE html>
    <html>

    <head>
        <title>Admin Confirm Payments</title>


        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirm Payment</title>
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
        <?php include 'sidebar.php'; ?>
        <div class="flex-row-body">
            <h2 class="payment-header">CONFIRM PAYMENT</h2>

            <div class="hr"></div>

            <div class="payment-container">
                <table>
                    <div class="flex-box1">

                        <div class="main-container-message">

                            <?php
                            while ($row = mysqli_fetch_assoc($result)) {


                                echo '<div class="flex-box-column2">';
                                echo "<h2 class='price'  data-balance='{$row['balance']}'>₱{$row['balance']}</h2>";


                                echo "<div class='flex-box-row'>";
                                echo "<p class='subtitle-text'>Vendor Name:</p>"; //TEXT
                                echo "<p class='sub-text' >{$row['vendor_name']}</h3>";
                                echo "</div>";

                                // Check if the payment is confirmed and archived
                                if ($row['confirmed'] == 1 && $row['archived'] == 1) {
                                    echo "<div class='button-container2'>";
                                    echo "<h2 class='paid-mark'>Paid</h2>";
                                    echo "</div>";
                                } else {
                                    echo "<div class='button-container2'>";
                                    echo "<button class='confirm-button' onclick=\"confirmAndArchive('{$row['vendor_userid']}', '{$row['vendor_name']}', '{$row['payment_date']}', '{$row['mop']}', '{$row['transaction_id']}', this)\" data-vendor-id='{$row['vendor_userid']}' data-balance='{$row['balance']}'></h4>Confirm</h4></button>";
                                    echo "</div>";
                                }

                                // echo "<p class='subtitle-text2'>Payment Date: </p>"; //TEXT
                                echo "<div class='date-container'>";
                                echo "<p class='sub-text2' >{$row['payment_date']}</p>";;
                                echo "</div>";

                                echo "<div class='flex-box-row'>";
                                echo "<p class='subtitle-text'>Mode of Payment:</p>";
                                echo "<p class='sub-text'>{$row['mop']}</p>";
                                echo "</div>";

                                echo "</div>";
                                echo "<div class='hr'></div>";
                            }
                            ?>
                </table>


                <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                <script>
                    function confirmAndArchive(vendorUserId, vendorName, paymentDate, modeOfPayment, transactionId, row) {
                    // Prompt the user for the balance
                    var userEnteredBalance = prompt("Please enter the paid balance for vendor " + vendorName + ":", $(row).data('balance'));

                    // Check if the user entered a valid balance
                    if (userEnteredBalance !== null && !isNaN(userEnteredBalance)) {
                        // Display a confirmation dialog with the vendor's name
                        var isConfirmed = confirm("Are you sure you want to confirm and archive for vendor: " + vendorName + "?");

                        // Check the user's response
                        if (isConfirmed) {
                            $.ajax({
                                type: "POST",
                                url: "confirm_and_archive_db.php",
                                data: {
                                    vendorUserId: vendorUserId,
                                    vendorName: vendorName,
                                    paymentDate: paymentDate,
                                    modeOfPayment: modeOfPayment,
                                    transactionId: transactionId,
                                    balance: userEnteredBalance,
                                    adminName: "<?php echo $admin_name; ?>"
                                },
                                success: function(response) {
                                    alert(response);
                                    // Reload the page after successful confirmation
                                    window.location.href = window.location.href;
                                },
                                error: function() {
                                    alert("Error confirming payment and archiving");
                                }
                            });
                        } else {
                            console.log("Action canceled by the user");
                        }
                    } else {
                        alert("Invalid balance entered. Please enter a valid number.");
                    }
                }


                    function confirmRemoveAll() {
                        var confirmDelete = confirm("Are you sure you want to remove all confirmed payments?");
                        if (confirmDelete) {
                            removeAllConfirmedPayments();
                        }
                    }

                    function removeAllConfirmedPayments() {
                        $.ajax({
                            type: "POST",
                            url: "remove_all_confirmed_payments.php", // Create this file to handle the removal
                            success: function(response) {
                                alert(response); // Display the server's response (if needed)
                                // Reload the page or update the table if needed
                                location.reload();
                            },
                            error: function() {
                                alert("Error removing confirmed payments");
                            }
                        });
                    }
                </script>

            </div>

            <div class="button-placement">
                <?php
                // Check if there is at least one row with confirmed and archived both equal to 1
                $validationQuery = "SELECT COUNT(*) AS count FROM ven_payments WHERE confirmed = 1 AND archived = 1";
                $validationResult = mysqli_query($connect, $validationQuery);

                if ($validationResult) {
                    $rowCount = mysqli_fetch_assoc($validationResult)['count'];

                    // Display the button only if there is at least one row
                    if ($rowCount > 0) {
                        echo '<button  class="remove-confirmed-payment" onclick="confirmRemoveAll()">Remove All Confirmed Payments</button>';
                    } else {
                        echo '<p>No confirmed payments available for removal.</p>';
                    }
                } else {
                    echo '<p>Error checking for confirmed payments: ' . mysqli_error($connect) . '</p>';
                }
                ?>

                <center><a href=admin_payment_records.php><button class="payment-history">VIEW VENDOR PAYMENT HISTORY</button></a></center>
                <center><a href='admin_index.php'><button class="back-button6">
                            < Back</button></a></center>
            </div>
        </div>


        <footer></footer>

        </div>
    </body>


    </html>
<?php } else {
    header("location:admin_logout.php");
}
