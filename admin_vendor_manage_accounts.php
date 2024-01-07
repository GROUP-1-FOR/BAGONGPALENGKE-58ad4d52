<?php
require_once "config.php";

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

// Fetch data from the database
$sql = "SELECT vendor_userid, vendor_name FROM vendor_sign_in";
$result = $connect->query($sql);

// Function to handle the search
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    // Use a prepared statement to prevent SQL injection
    $sql = "SELECT vendor_userid, vendor_name FROM vendor_sign_in WHERE vendor_name LIKE ? OR vendor_userid LIKE ?";
    $stmt = $connect->prepare($sql);
    // Bind parameters
    $searchParam = "%" . $searchTerm . "%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}

// Function to fetch search suggestions
function getSearchSuggestions($searchTerm)
{
    global $connect;
    $sql = "SELECT vendor_userid, vendor_name FROM vendor_sign_in WHERE vendor_name LIKE ? OR vendor_userid LIKE ?";
    $stmt = $connect->prepare($sql);
    // Bind parameters
    $searchParam = "%" . $searchTerm . "%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    $suggestions = array();
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }
    return $suggestions;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vendor Accounts</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px;
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

        button {
            padding: 5px 10px;
        }

        #addButton {
            display: block;
            margin: 20px auto;
        }

        #searchForm {
            float: right;
            margin-right: 20px;
        }

        #autocomplete {
            position: absolute;
            border: 1px solid #ddd;
            max-height: 150px;
            overflow-y: auto;
            z-index: 1;
            background-color: #fff;
        }

        #autocomplete div {
            padding: 10px;
            cursor: pointer;
        }

        #autocomplete div:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>

    <h2>Manage Vendor Accounts</h2>

    <!-- Search form -->
    <form id="searchForm" method="get">
        <label for="search">Search:</label>
        <input type="text" id="search" name="search" placeholder="Enter vendor name" oninput="showSuggestions()" maxlength="15">
        <input type="submit" value="Search">
        <div id="autocomplete"></div>
    </form>

    <table>
        <tr>
            <th>Vendor Information</th>
            <th>Action</th>
            <th>Remove</th> <!-- New column for checkboxes -->
        </tr>

        <?php
        // Function to check if vendor is editable
        function isVendorEditable($vendorId)
        {
            global $connect;

            // Check if vendor_userid exists in vendor_edit_profile table and vendor_edit column is equal to 0
            $sql = "SELECT vendor_userid FROM vendor_edit_profile WHERE vendor_userid = ? AND vendor_edit = 0";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("s", $vendorId);
            $stmt->execute();
            $stmt->store_result();
            $rowCount = $stmt->num_rows;
            $stmt->close();

            return $rowCount > 0;
        }

        // Display data in a table
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr data-vendorid='" . $row["vendor_userid"] . "'>";
                echo "<td>" . $row["vendor_name"] . "<br>Vendor ID: " . $row["vendor_userid"] . "</td>";
                echo "<td>";

                // Check if vendor_userid exists in vendor_edit_profile table and vendor_edit column is equal to 0
                $editButtonVisible = isVendorEditable($row["vendor_userid"]);

                if ($editButtonVisible) {
                    echo "<button onclick='editVendor(\"" . $row["vendor_userid"] . "\")'>Edit</button>";
                }

                echo "</td>";
                echo "<td><input type='checkbox' class='vendorCheckbox' name='selectedVendors[]' value='" . $row["vendor_userid"] . "' onchange='handleCheckboxSelection()'></td>"; // Add onchange attribute here
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No vendors found</td></tr>";
        }
        ?>
    </table>

    <!-- Add Vendor button -->
    <center>
        <div id="addButton">
            <button onclick="redirectToAddVendors()">Add Vendor (+)</button>
            <!-- Remove Vendor button -->
            <button id="removeButton" onclick="removeSelectedVendors()" disabled>Remove Vendor</button><br>
        </div>

        <a href=admin_index.php><button id="Button">Back</button></a>
    </center>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        // Function to handle the checkbox selection
        function handleCheckboxSelection() {
            const checkboxes = document.querySelectorAll('.vendorCheckbox');
            const removeButton = document.getElementById('removeButton');

            let anyCheckboxSelected = false;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    anyCheckboxSelected = true;
                }
            });

            removeButton.disabled = !anyCheckboxSelected;
        }

        // Function to handle the Remove Vendor button click
        function removeSelectedVendors() {
            const checkboxes = document.querySelectorAll('.vendorCheckbox:checked');
            const selectedVendorIds = [];

            checkboxes.forEach(checkbox => {
                selectedVendorIds.push(checkbox.value);
            });

            // Show a confirmation dialog
            const confirmRemove = confirm('Are you sure you want to remove the selected vendors?');

            if (confirmRemove) {
                // Perform the AJAX request or form submission to remove selected vendors
                // Replace 'remove_vendor.php' with the actual PHP file to handle vendor removal
                $.ajax({
                    url: 'remove_vendor.php',
                    type: 'POST', // Use POST to submit the selected vendor_userids
                    data: {
                        vendorIds: selectedVendorIds
                    }, // Pass selected vendor IDs to the server
                    dataType: 'json', // Expect JSON response
                    success: function(response) {
                        // Handle the response as needed
                        if (response.success) {
                            console.log('Vendors removed successfully');
                            // Update the table without refreshing the page
                            updateTable(selectedVendorIds);
                        } else {
                            console.error('Error removing vendors:', response.error);
                        }
                    },
                    error: function(error) {
                        console.error('AJAX error:', error);
                    }
                });
            }
        }

        // Function to dynamically update the table after removing vendors
        function updateTable(selectedVendorIds) {
            // Remove the corresponding table rows
            selectedVendorIds.forEach(vendorId => {
                const rowToRemove = document.querySelector(`[data-vendorid="${vendorId}"]`);
                if (rowToRemove) {
                    rowToRemove.remove();
                }
            });

            // Clear the checkbox selection and disable the Remove button
            const checkboxes = document.querySelectorAll('.vendorCheckbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            const removeButton = document.getElementById('removeButton');
            removeButton.disabled = true;
        }

        function editVendor(vendorId) {
            window.location.href = 'admin_edit_vendor.php?vendor_userid=' + encodeURIComponent(vendorId);
        }

        function redirectToAddVendors() {
            window.location.href = 'interactive_map.php';
        }

        function showSuggestions() {
            const searchInput = document.getElementById('search');
            const autocompleteContainer = document.getElementById('autocomplete');

            if (searchInput.value.length === 0) {
                autocompleteContainer.innerHTML = '';
                return;
            }

            // Simulate an AJAX request to get search suggestions
            const searchTerm = searchInput.value;
            $.ajax({
                url: 'search_get_suggestions.php', // Replace with the actual PHP file to handle suggestions
                type: 'GET',
                data: {
                    search: searchTerm
                },
                success: function(data) {
                    autocompleteContainer.innerHTML = '';
                    data.forEach(suggestion => {
                        const suggestionDiv = document.createElement('div');
                        suggestionDiv.innerHTML = suggestion.vendor_name + ' (ID: ' + suggestion.vendor_userid + ')';
                        suggestionDiv.onclick = function() {
                            searchInput.value = suggestion.vendor_name;
                            autocompleteContainer.innerHTML = '';
                        };
                        autocompleteContainer.appendChild(suggestionDiv);
                    });
                },
                error: function(error) {
                    console.error('Error fetching suggestions:', error);
                }
            });
        }
    </script>
</body>

</html>