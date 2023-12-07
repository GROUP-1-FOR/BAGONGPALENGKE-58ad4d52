<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

    if (isset($_GET['stall_number'])) {
        $stallNumber = $_GET['stall_number'];
        // Use $stallNumber as needed in your code
        // For example, you can store it in the session for further processing
        $_SESSION['vendor_stall_number'] = $stallNumber;
    }

    function generateUserID($pdo)
    {
        while (true) {
            // Generate a random 5-digit number
            $randomNumber = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

            // Form the user ID
            $userID = "VSR-" . $randomNumber;

            // Check if the user ID is unique in the database
            if (isUniqueUserID($pdo, $userID)) {
                return $userID;
            }
        }
    }

    // Function to check if the generated user ID is unique in the database
    function isUniqueUserID($pdo, $userID)
    {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM vendor_sign_in WHERE vendor_userid = ?");
        $stmt->execute([$userID]);

        return $stmt->fetchColumn() == 0;
    }

    // Create  Vendor User ID
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=bagong_palengke_db", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }


    if (isset($_GET['cancel'])) {
        unset($_SESSION['vendor_first_name']);
        unset($_SESSION['vendor_last_name']);
        unset($_SESSION['vendor_full_name']);
        unset($_SESSION['vendor_stall_number']);
        unset($_SESSION['vendor_mobile_number']);
        unset($_SESSION['vendor_product_type']);
        unset($_SESSION['vendor_email']);
        unset($_SESSION['vendor_userid']);
        unset($_SESSION['vendor_hashed_password']);
        unset($_SESSION['vendor_transaction_id']);

        // Redirect to another page after cancellation
        header("Location: admin_vendor_manage_accounts.php");
        exit();
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

            function checkPasswordMatch() {
                var password = document.getElementsByName("vendor_password")[0].value;
                var confirmPassword = document.getElementsByName("vendor_confirm_password")[0].value;
                var messageElement = document.getElementById("passwordMatchMessage");
                var confirmPasswordInput = document.getElementsByName("vendor_confirm_password")[0];

                // Enable or disable Confirm Password based on whether Password is empty
                confirmPasswordInput.disabled = password.length === 0;

                // Check if the "Password" field is not empty
                if (password.length > 0) {
                    // Check if the "Confirm Password" field is also not empty
                    if (confirmPassword.length > 0) {
                        // Check if passwords match
                        if (password === confirmPassword) {
                            // Check if passwords have at least 8 characters
                            if (password.length >= 8 && confirmPassword.length >= 8) {
                                messageElement.innerHTML = "Passwords match and meet the minimum length requirement.";
                                messageElement.style.color = "green";
                            } else {
                                messageElement.innerHTML = "Passwords match but do not meet the minimum length requirement (8 characters).";
                                messageElement.style.color = "red";
                            }
                        } else {
                            messageElement.innerHTML = "Passwords do not match.";
                            messageElement.style.color = "red";
                        }
                    } else {
                        // "Confirm Password" field is empty, clear the message
                        messageElement.innerHTML = "";
                    }
                } else {
                    // "Password" field is empty, clear the message and "Confirm Password" field
                    messageElement.innerHTML = "";
                    confirmPasswordInput.value = "";
                }

                return password === confirmPassword && password.length >= 8 && confirmPassword.length >= 8;
            }




            function updateSubmitButton() {
                var submitButton = document.querySelector('button[type="submit"]');


                var formIsValid = validateVendorFirstName() && validateVendorLastName() && validateVendorMobileNumber() && validateVendorEmail() && checkPasswordMatch();
                submitButton.disabled = !formIsValid;
            }
        </script>

    </head>

    <body>

        <h1>Create Vendor Account, <?php echo $admin_userid  ?>! </h1>

        <form action="admin_create_vendor_account_1.php" method="post" onsubmit="return validateForm()">


            <h2>Vendor Information</h2>

            <label for="Vendor First Name">Vendor First Name</label>
            <input type="text" name="vendor_first_name" id="vendor_first_name" value="<?php echo isset($_SESSION['vendor_first_name']) ? $_SESSION['vendor_first_name'] : ''; ?>" required oninput="validateVendorFirstName(); updateSubmitButton()">
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
            <input type="text" name="vendor_last_name" id="vendor_last_name" value="<?php echo isset($_SESSION['vendor_last_name']) ? $_SESSION['vendor_last_name'] : ''; ?>" required oninput="validateVendorLastName(); updateSubmitButton()">
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
            <label for="Stall Number">Stall No:</label>
            <input type="number" name="vendor_stall_number" value="<?php echo isset($_SESSION['vendor_stall_number']) ? $_SESSION['vendor_stall_number'] : ''; ?>" required><br />

            <label for="Mobile Number">Mobile Number</label>
            <input type="tel" name="vendor_mobile_number" id="vendor_mobile_number" maxlength="11" placeholder="09XXXXXXXXX" value="<?php echo isset($_SESSION['vendor_mobile_number']) ? $_SESSION['vendor_mobile_number'] : ''; ?>" oninput="validateVendorMobileNumber(); updateSubmitButton()">
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
            <input type="email" name="vendor_email" id="vendor_email" value="<?php echo isset($_SESSION['vendor_email']) ? $_SESSION['vendor_email'] : ''; ?>" required oninput="validateVendorEmail(); updateSubmitButton()">
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
                <option value="" disabled selected>Select Product Type</option>
                <option value="Wet" <?php echo (isset($_SESSION['vendor_product_type']) && $_SESSION['vendor_product_type'] == 'Wet') ? 'selected' : ''; ?>>Wet</option>
                <option value="Dry" <?php echo (isset($_SESSION['vendor_product_type']) && $_SESSION['vendor_product_type'] == 'Dry') ? 'selected' : ''; ?>>Dry</option>
                <option value="Other" <?php echo (isset($_SESSION['vendor_product_type']) && $_SESSION['vendor_product_type'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select><br />

            <br />
            <br />


            <h2>Vendor Account</h2>
            <label>Vendor User ID</label>
            <input type="text" name="vendor_userid" value="<?php echo $new_vendor_userid = generateUserID($pdo); ?>" readonly><br />

            <label>Password</label>
            <input type="password" name="vendor_password" placeholder="8 characters and above" oninput="checkPasswordMatch(); updateSubmitButton()"><br />

            <label>Confirm Password</label>
            <input type="password" name="vendor_confirm_password" required oninput="checkPasswordMatch(); updateSubmitButton()">
            <span id="passwordMatchMessage"></span><br />



            <button type="submit" disabled>Submit</button>
        </form>

        <script>
            function validateForm() {
                return validateVendorFirstName() && validateVendorLastName() && validateVendorMobileNumber() && validateVendorEmail() && checkPasswordMatch();
            }
        </script>



        <a href="admin_create_vendor_account.php?cancel=1">Back</a>

        <a href=admin_logout.php>
            <h1>LOGOUT</h1>
        </a>
    </body>


    </html>
<?php } else {
    header("location:admin_logout.php");
}
