<?php
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Map</title>
    <!-- Include your CSS file here -->
    <link rel="stylesheet" type="text/css" href="interactive_map.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .map-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .table {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            text-align: center;
            cursor: pointer; /* Add this line to make the tables clickable */
            width: 50px; /* Adjust the width as needed */
            height: 50px; /* Adjust the height as needed */
        }

        .add-button {
            display: none;
        }
    </style>
</head>
<body>

<div class="map-container">
    <?php
    // Loop through 75 tables
    for ($i = 1; $i <= 75; $i++) {
        echo '<div class="table" id="table' . $i . '" onclick="navigateToVendorAccount(' . $i . ')">';
        echo '<p>Stall ' . str_pad($i, 4, '0', STR_PAD_LEFT) . '</p>';
        echo '<a class="add-button" href="admin_create_vendor_account.php?stall_number=' . $i . '">Add</a>';
        echo '</div>';
    }
    ?>
</div>

<!-- Include your JavaScript file here -->
<script src="interactive_map.js"></script>
<script>
    function navigateToVendorAccount(tableNumber) {
        var url = 'admin_create_vendor_account.php?stall_number=' + tableNumber;
        window.location.href = url;
    }
</script>
<center>
    <button><a href="admin_index.php">Back</a></button>
</center>
</body>
</html>
