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
            position: relative;
            /* Add this line to make the positioning of the button relative to the table */
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            text-align: center;
            cursor: pointer;
            width: 50px;
            height: 80px;
        }

        .add-button {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="map-container">
        <?php
        // Loop through 75 tables
        for ($i = 1; $i <= 75; $i++) {
            echo '<div class="table" id="table' . $i . '" onclick="toggleAddButton(' . $i . ')">';
            echo '<p>Stall ' . $i . '</p>'; // Display stall number without leading zeros
            echo '<a class="add-button" href="javascript:void(0);" onclick="confirmAdd(' . $i . ')">Add</a>';
            echo '</div>';
        }
        ?>
    </div>

    <!-- Include your JavaScript file here -->
    <script src="interactive_map.js"></script>
    <script>
        function toggleAddButton(tableNumber) {
            var addButton = document.getElementById('table' + tableNumber).getElementsByClassName('add-button')[0];
            addButton.style.display = (addButton.style.display === 'none' || addButton.style.display === '') ? 'inline-block' : 'none';
        }

        function confirmAdd(tableNumber) {
            var confirmAddition = confirm('Are you sure you want to add a vendor?');
            if (confirmAddition) {
                var url = 'admin_create_vendor_account.php?stall_number=' + tableNumber;
                window.location.href = url;
            }
        }
    </script>
    <center>
        <button><a href="admin_index.php">Home</a></button>
    </center>
</body>

</html>