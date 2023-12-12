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
    <html lang="en">


        <h2>Manage Vendor Accounts</h2>
        <div class="flex-box">
    <main class="main-container">
        <div class="dashboard-announcement">
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
        </div>
        </main>
        </div>
    </html>

<?php
    // Close the database connection
    $connect->close();
} else {
    header("location:admin_logout.php");
}
?>