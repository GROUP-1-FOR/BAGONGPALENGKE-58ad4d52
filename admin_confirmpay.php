<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];


    $query = "SELECT id, name, balance, confirmed, archived, payment_date FROM ven_payments"; // Only select necessary columns
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
            </tr>

            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['balance']}</td>";

                // Check if the payment is confirmed and archived
                if ($row['confirmed'] == 1 && $row['archived'] == 1) {
                    echo "<td class='paid-mark'>Paid</td>";
                } else {
                    echo "<td class='action-cell'>
            <button onclick=\"confirmAndArchive('{$row['id']}', '{$row['name']}', '{$row['payment_date']}', this)\" data-vendor-id='{$row['id']}'>Confirm</button>
          </td>";
                }

                // Display the current date
                echo "<td>{$row['payment_date']}</td>";

                echo "</tr>";
            }
            ?>
        </table>

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script>
            function confirmAndArchive(id, name, payment_date, row) {
                $.ajax({
                    type: "POST",
                    url: "confirm_and_archive_db.php",
                    data: {
                        vendorId: id,
                        vendorName: name,
                        paymentDate: payment_date
                    },
                    success: function(response) {
                        alert(response); // Display the server's response (if needed)

                        // Update the content of the 'Action' cell to 'Paid'
                        $(row).closest('tr').find('.action-cell').html('Paid');
                    },
                    error: function() {
                        alert("Error confirming payment and archiving");
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
<?php } else {
    header("location:admin_login.php");
}
