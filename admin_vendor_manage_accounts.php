<?php
require_once "config.php";

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
        <input type="text" id="search" name="search" placeholder="Enter vendor name" oninput="showSuggestions()">
        <input type="submit" value="Search">
        <div id="autocomplete"></div>
    </form>

    <table>
        <tr>
            <th>Vendor Information</th>
            <th>Action</th>
        </tr>

        <?php
        // Display data in a table
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["vendor_name"] . "<br>Vendor ID: " . $row["vendor_userid"] . "</td>";
                echo "<td>";
                echo "<button onclick='editVendor(\"" . $row["vendor_userid"] . "\")'>Edit</button>";
                echo "<button onclick='removeVendor(\"" . $row["vendor_userid"] . "\")'>Remove</button>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No vendors found</td></tr>";
        }
        ?>
    </table>

    <!-- Button to add vendors -->
    <button id="addButton" onclick="redirectToAddVendors()">Add Vendor (+)</button>

    <a href=admin_index.php><button id="Button">Back</button></a>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function editVendor(vendorId) {
            console.log("Edit vendor with ID: " + vendorId);
        }

        function removeVendor(vendorId) {
            // Ask for user confirmation
            var confirmation = confirm("Are you sure you want to remove this vendor?");
            if (confirmation) {
                // User confirmed, make AJAX request to remove vendor
                $.ajax({
                    url: 'remove_vendor.php', // Replace with the actual PHP file to handle removal
                    type: 'POST',
                    data: {
                        vendorId: vendorId
                    },
                    success: function(data) {
                        // If removal from the database is successful, remove the corresponding row from the table
                        if (data.success) {
                            var rowToRemove = $("button[data-vendor-id='" + vendorId + "']").closest('tr');
                            rowToRemove.remove();
                        } else {
                            console.error('Error removing vendor:', data.error);
                        }
                    },
                    error: function(error) {
                        console.error('Error removing vendor:', error);
                    }
                });
            }
        }

        function redirectToAddVendors() {
            window.location.href = 'admin_create_vendor_account.php';
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

<?php
// Close the database connection
$connect->close();
?>