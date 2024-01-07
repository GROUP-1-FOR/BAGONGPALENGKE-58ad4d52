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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paid Records</title>
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

        .archive-button {
            display: block;
            margin: 20px auto;
            /* Adjust margin as needed */
        }
    </style>
</head>

<body>

    <h2>Paid Records</h2>

    <table>
        <tr>
            <th>Name</th>
            <th>Balance</th>
            <th>Payment Date</th> <!-- Add this line for the payment date -->
            <th>Mode of Payment</th>
            <th>Transaction ID</th>
        </tr>

        <?php
        // Loop through the database results and display them in the table
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['vendor_name']}</td>";
            echo "<td>{$row['balance']}</td>";
            echo "<td>{$row['payment_date']}</td>";
            echo "<td>{$row['mop']}</td>";
            echo "<td>{$row['transaction_id']}</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <button class="archive-button" onclick="requestAccess()">Request Access for Archive</button>

    <a href=admin_confirmpay.php>
        <h1>BACK</h1>
    </a>

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

</body>

</html>