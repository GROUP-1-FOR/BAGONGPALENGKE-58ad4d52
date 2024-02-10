<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

// Fetch paid records from the database
$query = "SELECT id, vendor_name, balance, payment_date, mop, transaction_id FROM paid_records";
$result = mysqli_query($connect, $query);

if (!$result) {
    die('Error: Unable to fetch paid records from the database');
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paid Records</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="text-style.css">
    <link rel="javascript" type="text/script" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }


        th {
            background-color: #f2f2f2;
        }

        .archive-button {
            display: block;
            margin: 20px auto;
            /* Adjust margin as needed */
        }
    </style>
</head>

<body>
    <header class="header2"></header>
    <?php include 'sidebar.php'; ?>

    <div class="flex-row">
        <h2 class="paid-record" style="color: maroon" ;> PAID RECORDS</h2>
        <div class="paid-records2">

            <div class="flex-box1">
                <div class="main-container">
                    <table>
                        <center>
                            <tr class="table-header">
                                <th class="table-header-left th1">Name</th>
                                <th class="th1">Balance</th>
                                <th class="th1">Payment Date</th> <!-- Add this line for the payment date -->
                                <th class="th1">Mode of Payment</th>
                                <th class="table-header-right th1">Transaction ID</th>
                            </tr>
                        </center>

                        <?php
                        echo "</center>";
                        // Loop through the database results and display them in the table
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<center>";
                            echo "<tr class='table-header1'>";
                            echo "<td class= 'td2'>{$row['vendor_name']}</td>";
                            echo "<td class= 'td1'>{$row['balance']}</td>";
                            echo "<td class= 'td1'>{$row['payment_date']}</td>";
                            echo "<td class= 'td1'>{$row['mop']}</td>";
                            echo "<td class= 'td1'>{$row['transaction_id']}</td>";
                            echo "</tr>";
                        }
                        echo "</center>";
                        ?>


                </div>
                </table>
            </div>

        </div>

    </div>
    <!-- <button class="archive-button" onclick="requestAccess()">Request Access for Archive</button> -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function requestAccess() {
            $.ajax({
                type: "POST",
                url: "request_access.php", // Replace with your server-side script to handle access requests
                data: {
                    requestType: "archive"
                },
                success: function(response) {
                    alert(response); // Display the server's response (if needed)
                },
                error: function() {
                    alert("Error requesting access for archive");
                }
            });
        }

        function archiveAll() {
            // Iterate through each row and trigger the archive function
            $("table tr").each(function() {
                var id = $(this).find("td:first-child").text(); // Assuming the first cell contains the ID
                var name = $(this).find("td:nth-child(2)").text(); // Assuming the second cell contains the name
                archivePayment(id, name);
            });
        }
    </script>


    <footer></footer>
</body>

</html>