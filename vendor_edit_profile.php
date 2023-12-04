<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];

    // Fetch user details from the database based on the session user ID
    $query = "SELECT vendor_first_name, vendor_last_name, vendor_mobile_number, vendor_email, vendor_product FROM vendor_sign_in WHERE vendor_userid = '$userid'";
    $result = mysqli_query($connect, $query);

    if ($result) {
        $userDetails = mysqli_fetch_assoc($result);

        // Assign retrieved values to variables
        $vendorFirstName = $userDetails['vendor_first_name'];
        $vendorLastName = $userDetails['vendor_last_name'];
        $vendorMobileNumber = $userDetails['vendor_mobile_number'];
        $vendorEmail = $userDetails['vendor_email'];
        $vendorProduct = $userDetails['vendor_product'];
    } else {
        // Handle database query error
        die("Database query failed: " . mysqli_error($connect));
    }
?>

    <!DOCTYPE html>
    <html>

    <head>
        <title>Create Vendor Account</title>
        <script>
            function validateVendorFirstName() {
                var vendor_name = document.getElementById("vendor_first_name").value;
                var vendor_name_error_span = document.getElementById("vendor_first_name_error_span");

                // Check if the input contains any numbers or symbols
                if (vendor_name.length > 0 && /[0-9!@#$%^&*(),.?":{}|<>]/.test(vendor_name)) {
                    vendor_name_error_span.textContent = "Please enter the vendor name without numbers or symbols.";
                    return false; // Prevent form submission
                } else {
                    vendor_name_error_span.textContent = "";
                }

                // If the input is valid, you can proceed with form submission
                return true;
            }

            function validateVendorLastName() {
                var vendor_name = document.getElementById("vendor_last_name").value;
                var vendor_name_error_span = document.getElementById("vendor_last_name_error_span");

                // Check if the input contains any numbers or symbols
                if (vendor_name.length > 0 && /[0-9!@#$%^&*(),.?":{}|<>]/.test(vendor_name)) {
                    vendor_name_error_span.textContent = "Please enter the vendor name without numbers or symbols.";
                    return false; // Prevent form submission
                } else {
                    vendor_name_error_span.textContent = "";
                }

                // If the input is valid, you can proceed with form submission
                return true;
            }


            function validateVendorMobileNumber() {
                var vendor_mobile_number = document.getElementById("vendor_mobile_number").value;
                var vendor_mobile_number_error_span = document.getElementById("vendor_mobile_number_error_span");

                // Check if the input contains any non-numeric characters
                if (vendor_mobile_number.length > 0 && !/^09\d{9}$/.test(vendor_mobile_number)) {
                    // Show error message
                    vendor_mobile_number_error_span.textContent = "Please enter a valid mobile number.";
                    return false; // Prevent form submission
                } else {
                    // Clear error message
                    vendor_mobile_number_error_span.textContent = "";
                }

                // If the input is valid, you can proceed with form submission
                return true;
            }

            function validateVendorEmail() {
                var vendor_email = document.getElementById("vendor_email").value;
                var vendor_email_error_span = document.getElementById("vendor_email_error_span");

                // Use the built-in email validation
                if (vendor_email.length > 0 && !document.getElementById("vendor_email").checkValidity()) {
                    // Show error message
                    vendor_email_error_span.textContent = "Please enter a valid email address.";
                    return false; // Prevent form submission
                } else {
                    // Clear error message
                    vendor_email_error_span.textContent = "";
                }

                // If the input is valid, you can proceed with form submission
                return true;
            }


        // Initialize variables to store initial values of form fields
        var initialVendorFirstName = "<?php echo $vendorFirstName; ?>";
            var initialVendorLastName = "<?php echo $vendorLastName; ?>";
            var initialVendorMobileNumber = "<?php echo $vendorMobileNumber; ?>";
            var initialVendorEmail = "<?php echo $vendorEmail; ?>";
            var initialVendorProduct = "<?php echo $vendorProduct; ?>";

            function hasFormChanged() {
                // Check if any of the form fields have changed
                return (
                    document.getElementById("vendor_first_name").value !== initialVendorFirstName ||
                    document.getElementById("vendor_last_name").value !== initialVendorLastName ||
                    document.getElementById("vendor_mobile_number").value !== initialVendorMobileNumber ||
                    document.getElementById("vendor_email").value !== initialVendorEmail ||
                    document.getElementById("vendor_product").value !== initialVendorProduct
                );
            }

            function updateSubmitButton() {
                var submitButton = document.querySelector('button[type="submit"]');
                submitButton.disabled = !hasFormChanged();
            }

            function validateForm() {
                return hasFormChanged() && validateVendorFirstName() && validateVendorLastName() && validateVendorMobileNumber() && validateVendorEmail();
            }
        </script>

    </head>

    <body>

        <h1>Update Vendor Account, <?php echo $userid  ?>! </h1>

        <form action="vendor_edit_profile_1.php" method="post" onsubmit="return validateForm()">


            <h2>Vendor Information</h2>

            <label for="Vendor First Name">Vendor First Name</label>
            <input type="text" name="vendor_first_name" id="vendor_first_name" required oninput="validateVendorFirstName(); updateSubmitButton()" value="<?php echo $vendorFirstName; ?>">
            <!-- Display an error message if it exists in the session -->
            <span style="color: red;" id="vendor_first_name_error_span">

                <?php
                if (isset($_SESSION['vendor_first_name_error'])) {
                    echo $_SESSION['vendor_first_name_error'];
                    // Unset the session variable after displaying the error
                    unset($_SESSION['vendor_first_name_error']);
                }
                ?>
            </span>
            <br />
            <label for="Vendor Last Name">Vendor Last Name</label>
            <input type="text" name="vendor_last_name" id="vendor_last_name" required oninput="validateVendorLastName(); updateSubmitButton()" value="<?php echo $vendorLastName; ?>">
            <!-- Display an error message if it exists in the session -->
            <span style="color: red;" id="vendor_last_name_error_span">

                <?php
                if (isset($_SESSION['vendor_last_name_error'])) {
                    echo $_SESSION['vendor_last_name_error'];
                    // Unset the session variable after displaying the error
                    unset($_SESSION['vendor_last_name_error']);
                }
                ?>
            </span>

            <br />

            <label for="Mobile Number">Mobile Number</label>
            <input type="tel" name="vendor_mobile_number" id="vendor_mobile_number" maxlength="11" placeholder="09XXXXXXXXX" oninput="validateVendorMobileNumber(); updateSubmitButton()" value="<?php echo $vendorMobileNumber; ?>">
            <!-- Display an error message if it exists in the session -->
            <span style="color: red;" id="vendor_mobile_number_error_span">
                <?php
                if (isset($_SESSION['vendor_mobile_number_error'])) {
                    echo $_SESSION['vendor_mobile_number_error'];
                    // Unset the session variable after displaying the error
                    unset($_SESSION['vendor_mobile_number_error']);
                }
                ?>
            </span>

            <br />
            <label for="Email">Email:</label>
            <input type="email" name="vendor_email" id="vendor_email" required oninput="validateVendorEmail(); updateSubmitButton()" value="<?php echo $vendorEmail; ?>">
            <!-- Display an error message if it exists in the session -->
            <span style="color: red;" id="vendor_email_error_span">
                <?php
                if (isset($_SESSION['vendor_email_error'])) {
                    echo $_SESSION['vendor_email_error'];
                    // Unset the session variable after displaying the error
                    unset($_SESSION['vendor_email_error']);
                }
                ?>
            </span>

            <br />
            <label for="Product Type">Products:</label>
            <select name="vendor_product" required>
                <option value="" disabled>Select Product Type</option>
                <option value="Wet" <?php if ($vendorProduct == 'Wet') echo 'selected'; ?>>Wet</option>
                <option value="Dry" <?php if ($vendorProduct == 'Dry') echo 'selected'; ?>>Dry</option>
                <option value="Other" <?php if ($vendorProduct == 'Other') echo 'selected'; ?>>Other</option>
            </select><br />

            <br />
            <br />

            <button type="submit" disabled>Submit</button>
        </form>

        <script>
            function validateForm() {
                return validateVendorFirstName() && validateVendorLastName() && validateVendorMobileNumber() && validateVendorEmail() && checkPasswordMatch();
            }
        </script>



        <a href=vendor_index.php>
            <h1>BACK</h1>
        </a>

        <a href=vendor_logout.php>
            <h1>LOGOUT</h1>
        </a>
    </body>


    </html>
<?php } else {
    header("location:vendor_login.php");
}
