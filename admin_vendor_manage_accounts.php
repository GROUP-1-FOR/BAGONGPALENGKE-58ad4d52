<?php
require_once "config.php";

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    // Fetch data from the database
    $sql = "SELECT vendor_userid, vendor_name FROM vendor_sign_in";
    $result = $connect->query($sql);

    
        
    $sql = "SELECT admin_name FROM admin_sign_in WHERE admin_userid = '$admin_userid'";

    // Execute the query
    $result = $connect->query($sql);
    $admin_name = "";
    $admin_name_error = "";

    // Check if any rows were returned
    if ($result->num_rows > 0) {
        // Output data for each row
        while ($row = $result->fetch_assoc()) {
            $admin_name = $row['admin_name'];
        }
    } else {
        $admin_name_error = "No results found for user ID $admin_userId";
    }


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
    </head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SIGN IN </title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src">
    </header>
    <div class="main-sidebar">
        <ul class="sidebar-outside">
            <div class="profile-container">
                <img class="profile-pic-holder" src="assets\images\sign-in\profile-pic.svg">
                <img class="profile-design" src="assets\images\sign-in\profile-design.png">
                <p class="vendor-name">Welcome, <?php echo $admin_name; ?>! </p>
            </div>
        </ul>
        <div class="sidebar-inside">
            <ul class="dashboard-sidebar">
                <li><a class="home-index" href=admin_index.php> Home </a></li>
                <li><a class="manage-vendor" href=admin_vendor_manage_accounts.php> Manage Vendor Accounts </a></li>
                <li><a class="report-management" href="#"> Report Management </a></li>
                <li><a class="help-button" href="#"> Help </a></li>
            </ul>
        </div>
        <div>
            <a href=admin_logout.php>
                <h1 class="logout-button">LOGOUT</h1>
            </a>
        </div>
    </div>
    <div class="flex-box">
    <main class="main-container">
<div class="dashboard-announcement">
        <h2>Manage Vendor Accounts</h2>
        <!-- Search form -->
        <form class="seach"id="searchForm" method="get">
            <label for="search">Search:</label>
            <input type="text" id="search" name="search" placeholder="Enter vendor name" oninput="showSuggestions()">
            <input class="seach" type="submit" value="Search">
            <div id="autocomplete"></div>
        </form>

        <table>
            <tr>
                <th>Vendor Information</th>
                <th>Action</th>
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
                    echo "<tr>";
                    echo "<td>" . $row["vendor_name"] . "<br>Vendor ID: " . $row["vendor_userid"] . "</td>";
                    echo "<td>";

                    // Check if vendor_userid exists in vendor_edit_profile table and vendor_edit column is equal to 0
                    $editButtonVisible = isVendorEditable($row["vendor_userid"]);

                    if ($editButtonVisible) {
                        echo "<button onclick='editVendor(\"" . $row["vendor_userid"] . "\")'>Edit</button>";
                    }

                    echo "<button onclick='removeVendor(\"" . $row["vendor_userid"] . "\")'>Remove</button>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No vendors found</td></tr>";
            }


            ?>
        </table>


        <!--Button to add vendors -->
        <button id="addButton" onclick="redirectToAddVendors()">Add Vendor (+)</button>

        <a href=admin_index.php><button id="Button">Back</button></a>
    </div>
        </main>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script>
            function editVendor(vendorId) {
                window.location.href = 'admin_edit_vendor.php?vendor_userid=' + encodeURIComponent(vendorId);
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
<footer></footer>
    </html>

<?php
    // Close the database connection
    $connect->close();
} else {
    header("location:admin_logout.php");
}
?>