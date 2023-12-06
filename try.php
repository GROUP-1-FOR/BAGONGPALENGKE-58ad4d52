<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Tables</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Add your desired styles here */
        .table {
            width: 50px;
            height: 50px;
            border: 1px solid black;
            margin: 5px;
            display: inline-block;
            cursor: pointer;
        }

        .selected {
            background-color: gray;
        }

        #optionsPopup label {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<!-- Add 74 tables arranged in a 14x14 grid with unique IDs -->
<div id="tableContainer">
    <!-- You can generate these tables dynamically using JavaScript -->
    <!-- For simplicity, I'm manually adding a few tables here -->
    <!-- Use a nested loop to generate a 14x14 grid -->
    <?php
        $tableCounter = 1;
        for ($i = 1; $i <= 5; $i++) { // row
            for ($j = 1; $j <= 15; $j++) {
                $vendorStallNumber = str_pad($tableCounter, 4, '0', STR_PAD_LEFT);
                echo '<div class="table" id="table'.$tableCounter.'" onclick="showOptions(\'table'.$tableCounter.'\')">Stall '.$vendorStallNumber.'</div>';
                $tableCounter++;
            }
            echo '<br>';  // Start a new line after each row
        }
    ?>
</div>

<!-- Popup for options -->
<div id="optionsPopup" style="display: none;">
    <button id="paid" name="status" value="green" onclick="selectOption('green')">Paid</button><br>
    <button id="pending" name="status" value="red" onclick="selectOption('red')">Pending</button><br>
    <button id="unoccupied" name="status" value="blue" onclick="selectOption('blue')">Unoccupied</button><br>
    <button onclick="confirmAndChangeColor()">Confirm</button>
</div>

<script>
    let currentTableId;
    let selectedStatus;

    function showOptions(tableId) {
        // Remove the 'selected' class from all tables
        const tables = document.querySelectorAll('.table');
        tables.forEach(table => table.classList.remove('selected'));

        // Add the 'selected' class to the clicked table
        currentTableId = tableId;
        document.getElementById(currentTableId).classList.add('selected');
        document.getElementById('optionsPopup').style.display = 'block';
    }

    function selectOption(status) {
        selectedStatus = status;
    }

    function confirmAndChangeColor() {
        if (selectedStatus) {
            const confirmation = confirm("Are you sure you want to change the color?");
            if (confirmation) {
                document.getElementById(currentTableId).style.backgroundColor = selectedStatus;
                document.getElementById('optionsPopup').style.display = 'none';
            }
        } else {
            alert("Please select an option.");
        }
    }
</script>
</body>
</html>
