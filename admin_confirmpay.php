<?php
require("config.php");
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
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .paid-mark {
            color: green;
            /* Customize the color of the "Paid" mark */
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>CONFIRM PAYMENT, <?php echo $admin_userid  ?>! </h1>

    <table>
        <tr>
            <th>Name</th>
            <th>Balance</th>
            <th>Status</th>
            <th>Payment Date</th>
            <th>Mode of Payment</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['vendor_name']}</td>";
            echo "<td>{$row['balance']}</td>";

            // Check if the payment is confirmed and archived
            if ($row['confirmed'] == 1 && $row['archived'] == 1) {
                echo "<td class='paid-mark'>Paid</td>";
            } else {
                echo "<td class='action-cell'>
                        <button onclick=\"confirmAndArchive('{$row['vendor_userid']}', '{$row['vendor_name']}', '{$row['payment_date']}', '{$row['mop']}', '{$row['transaction_id']}', this)\" data-vendor-id='{$row['vendor_userid']}'>Confirm</button>
                    </td>";
            }

            // Display the current date, Mode of Payment, and Transaction ID
            echo "<td>{$row['payment_date']}</td>";
            echo "<td>{$row['mop']}</td>";
            //echo "<td>{$row['transaction_id']}</td>";

            echo "</tr>";
        }
        ?>
    </table>

    <?php
    // Check if there is at least one row with confirmed and archived both equal to 1
    $validationQuery = "SELECT COUNT(*) AS count FROM ven_payments WHERE confirmed = 1 AND archived = 1";
    $validationResult = mysqli_query($connect, $validationQuery);

    if ($validationResult) {
        $rowCount = mysqli_fetch_assoc($validationResult)['count'];

        // Display the button only if there is at least one row
        if ($rowCount > 0) {
            echo '<button onclick="confirmRemoveAll()">Remove All Confirmed Payments</button>';
        } else {
            echo '<p>No confirmed payments available for removal.</p>';
        }
    } else {
        echo '<p>Error checking for confirmed payments: ' . mysqli_error($connect) . '</p>';
    }
    ?>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function confirmAndArchive(vendorUserId, vendorName, paymentDate, modeOfPayment, transactionId, row) {
            // Display a confirmation dialog with the vendor's name
            var isConfirmed = confirm("Are you sure you want to confirm and archive for vendor: " + vendorName + "?");

            // Check the user's response
            if (isConfirmed) {
                // User confirmed, proceed with the AJAX call
                $.ajax({
                    type: "POST",
                    url: "confirm_and_archive_db.php",
                    data: {
                        vendorUserId: vendorUserId,
                        vendorName: vendorName,
                        paymentDate: paymentDate,
                        modeOfPayment: modeOfPayment,
                        transactionId: transactionId,
                        balance: $(row).closest('tr').find('td:eq(1)').text(),
                        adminName: "<?php echo $admin_name; ?>" // Pass admin_name from PHP to JavaScript
                    },
                    success: function(response) {
                        alert(response);
                        $(row).closest('tr').find('.action-cell').html('Paid');
                    },
                    error: function() {
                        alert("Error confirming payment and archiving");
                    }
                });
            } else {
                // User canceled, you can handle this as needed
                console.log("Action canceled by the user");
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


    <a href=admin_index.php>
        <h1>BACK</h1>
    </a>




    <a href=admin_payment_records.php>
        <h1>VIEW VENDOR PAYMENT HISTORY</h1>
    </a>


</body>


</html>