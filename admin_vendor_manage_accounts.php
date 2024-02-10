<?php
require_once "config.php";

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

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
    <html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SIGN IN</title>
        <link rel="stylesheet" type="text/css" href="index.css">
        <link rel="stylesheet" type="text/css" href="text-style.css">
        <link rel="javascript" type="text/script" href="js-style.js">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    </head>

    <body>
        <header class="header2"></header>
        <?php include 'sidebar.php'; ?>

        <div class="flex-row">

            <h1 class="manage-account-header">MANAGE ACCOUNTS</h1>
            <div>
                <form class="form-search" id="searchForm" method="get">
                    <div class="search-container">
                        <input class="search-box" type="text" id="search" name="search" placeholder="Enter vendor name" oninput="showSuggestions()" maxlength="15">
                        <!-- <input type="submit" value="Search"> -->
                        <input class="search-icon" type="image" src="assets\images\sign-in\search-button.svg" alt="Search" onclick="submitSearchForm()">
                        <!-- <a><img class="search-button" src="assets\images\sign-in\search-button.svg"></a> -->
                        <div id="autocomplete"></div>
                    </div>
                </form>
            </div>
            <div class="manage-account">

                <div class="flex-box1">
                    <div class="main-container">



                        <table>
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
                                    echo "<div class='vendor-container'>";
                                    echo "<tr data-vendorid='" . $row["vendor_userid"] . "'>";
                                    echo "<td class= 'account-box'>" . "<h3 class='vendor-account'>" . $row["vendor_name"] . "</h3>" . "<br>Vendor ID:" . $row["vendor_userid"] . "</td>";
                                    echo "<td>";

                                    // Check if vendor_userid exists in vendor_edit_profile table and vendor_edit column is equal to 0
                                    $editButtonVisible = isVendorEditable($row["vendor_userid"]);

                                    if ($editButtonVisible) {
                                        echo "<button class='edit-button' onclick='editVendor(\"" . $row["vendor_userid"] . "\")'>Edit</button>";
                                    }

                                    // if ($editButtonVisible) {
                                    //     echo "<button onclick='removeVendor(\"" . $row["vendor_userid"] . "\")'>Remove Vendor</button>";
                                    // }

                                    echo "</td>";
                                    echo "<td class='check-boxes'><input type='checkbox' class='vendorCheckbox' name='selectedVendors[]' value='" . $row["vendor_userid"] . "' onchange='handleCheckboxSelection()'></td>"; // Add onchange attribute here
                                    echo "</tr>";
                                    echo "</div>";
                                    echo "<tr><td colspan='3'><hr></td></tr>"; // Add hr after each row
                                }
                            } else {
                                echo "<tr><td colspan='3'>No vendors found</td></tr>";
                            }
                            ?>
                        </table>
                        <!-- <div class="parent-container">
                            <div id="addButton">
                                <a onclick="redirectToAddVendors()"><img class="add-button" src="assets\images\sign-in\add-button.svg"></a>
                            </div>
                            <div id=" addButton">
    
                                <button id="removeButton" onclick="removeSelectedVendors()" disabled>Remove Vendor</button><br>
                            </div>
                            <a href=admin_index.php><button id="Button">Back</button></a>
                        </div> -->

                        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

                    </div>

                </div>
            </div>
            <div class="parent-container">
                <div id="addButton">
                    <a onclick="redirectToAddVendors()"><img class="add-button" src="assets\images\sign-in\add-button.svg"></a>
                </div>
                <div id=" addButton">
                    <!-- Remove Vendor button -->
                    <button class="remove-button" id="removeButton" onclick="removeSelectedVendors()" disabled>Remove</button><br>
                </div>
                <a href=admin_index.php><button id="Button">Back</button></a>
            </div>

        </div>
        </div>

    </body>

    <footer></footer>
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
                const hrToRemove = rowToRemove.nextElementSibling; // Select the <hr> element
                if (rowToRemove) {
                    rowToRemove.remove();
                }
                if (hrToRemove) {
                    hrToRemove.remove();
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
                        suggestionDiv.innerHTML = suggestion.vendor_name + ' (' + suggestion.vendor_userid + ') '; // Add space after each item
                        suggestionDiv.onclick = function() {
                            searchInput.value = suggestion.vendor_name;
                            document.getElementById('searchForm').submit(); // Submit the search form
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

    </html>
<?php
    // Close the database connection
    $connect->close();
} else {
    header("location:admin_logout.php");
}
?>