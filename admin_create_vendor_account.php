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
        unset($_SESSION['vendor_mobile_number']);
        unset($_SESSION['vendor_product_type']);
        //unset($_SESSION['vendor_payment_basis']);
        unset($_SESSION['vendor_email']);
        unset($_SESSION['vendor_userid']);
        unset($_SESSION['vendor_hashed_password']);
        unset($_SESSION['vendor_transaction_id']);

        // Redirect to another page after cancellation
        header("Location: interactive_map.php");
        exit();
    }



?>


    <!DOCTYPE html>
    <html>

    <head>
        <title>Create Vendor Account</title>
        <script>
            function validateForm() {
                return (
                    validateVendorFirstName() &&
                    validateVendorLastName() &&
                    validateVendorMobileNumber() &&
                    validateVendorEmail() &&
                    validateVendorProductType() &&
                    validateVendorFirstPaymentDate() &&
                    validatePassword() &&
                    checkPasswordMatch()
                );
            }

            function validateVendorFirstName() {
                var vendor_name = document.getElementById("vendor_first_name").value;
                var vendor_name_error_span = document.getElementById("vendor_first_name_error_span");

                // Check if the input contains any numbers or symbols
                if (vendor_name.length > 0 && /[^a-zA-Z\s]/.test(vendor_name)) {
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
                if (vendor_name.length > 0 && /[^a-zA-Z\s]/.test(vendor_name)) {
                    vendor_name_error_span.textContent = "Please enter the vendor name without numbers or symbols.";
                    return false; // Prevent form submission
                } else {
                    vendor_name_error_span.textContent = "";
                }

                // If the input is valid, you can proceed with form submission
                return true;
            }

            function capitalizeFirstName() {
                var firstNameInput = document.getElementById("vendor_first_name");
                var names = firstNameInput.value.split(' ');

                // Capitalize the first letter of each name
                for (var i = 0; i < names.length; i++) {
                    names[i] = names[i].charAt(0).toUpperCase() + names[i].slice(1).toLowerCase();
                }

                // Join the names back together with spaces
                firstNameInput.value = names.join(' ');
            }

            function capitalizeLastName() {
                var lastNameInput = document.getElementById("vendor_last_name");
                var names = lastNameInput.value.split(' ');

                // Capitalize the first letter of each name
                for (var i = 0; i < names.length; i++) {
                    names[i] = names[i].charAt(0).toUpperCase() + names[i].slice(1).toLowerCase();
                }

                // Join the names back together with spaces
                lastNameInput.value = names.join(' ');
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
                var vendor_email_input = document.getElementById("vendor_email");
                var vendor_email = vendor_email_input.value.trim().toLowerCase();
                var vendor_email_error_span = document.getElementById("vendor_email_error_span");

                // Check if the input is not empty and contains a valid email address
                if (vendor_email.length > 0 && !vendor_email_input.checkValidity()) {
                    // Show error message for general email validation
                    vendor_email_error_span.textContent = "Please enter a valid email address.";
                    return false; // Prevent form submission
                } else {
                    // Clear general email error message
                    vendor_email_error_span.textContent = "";
                }

                // Check if the email ends with gmail.com
                if (vendor_email.length > 0 && !vendor_email.endsWith("@gmail.com")) {
                    // Show error message for Gmail.com validation
                    vendor_email_error_span.textContent = "only @gmail.com is accepted";
                    return false; // Prevent form submission
                } else {
                    // Clear Gmail.com email error message
                    vendor_email_error_span.textContent = "";
                }

                // If the input is valid, you can proceed with form submission
                return true;
            }

            function validateVendorProductType() {
                var productType = document.getElementById("vendor_product").value;
                var productType_error_span = document.getElementById("vendor_product_type_error_span");

                if (productType === "") {
                    productType_error_span.textContent = "";
                    return false; // Prevent form submission
                } else {
                    productType_error_span.textContent = "";
                }

                return true;
            }

            function validateVendorFirstPaymentDate() {
                var firstPaymentDate = document.getElementById("vendor_first_payment_date").value;
                var first_payment_error_span = document.getElementById("vendor_first_payment_date_error_span");

                if (firstPaymentDate === "") {
                    first_payment_error_span = "";
                    return false; // Prevent form submission
                } else {
                    first_payment_error_span = "";
                }

                return true;

            }

            function validatePassword() {
                var passwordInput = document.getElementById("vendor_password");
                var password = passwordInput.value;
                var passwordValidationMessage = document.getElementById("passwordValidationMessage");

                // Define the password patterns
                var lengthPattern = /.{8,16}/;
                var uppercasePattern = /[A-Z]/;
                var lowercasePattern = /[a-z]/;
                var digitPattern = /\d/;
                var specialCharPattern = /[!@#$%^&*()_+]/;

                // Check each pattern and provide feedback
                var isValid = true;
                if (!lengthPattern.test(password)) {
                    isValid = false;
                    passwordValidationMessage.textContent = "Password must be 8-16 characters.";
                } else if (!uppercasePattern.test(password)) {
                    isValid = false;
                    passwordValidationMessage.textContent = "Include at least one uppercase letter.";
                } else if (!lowercasePattern.test(password)) {
                    isValid = false;
                    passwordValidationMessage.textContent = "Include at least one lowercase letter.";
                } else if (!digitPattern.test(password)) {
                    isValid = false;
                    passwordValidationMessage.textContent = "Include at least one number.";
                } else if (!specialCharPattern.test(password)) {
                    isValid = false;
                    passwordValidationMessage.textContent = "Include at least one special character.";
                } else {
                    passwordValidationMessage.textContent = "";
                }

                return isValid;
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
                            messageElement.innerHTML = "Passwords match";
                            messageElement.style.color = "green";
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

                return password === confirmPassword;
            }

            function togglePasswordVisibility() {
                var passwordInput = document.getElementById("vendor_password");
                var showPasswordCheckbox = document.getElementById("showPassword");

                // Toggle the password visibility
                passwordInput.type = showPasswordCheckbox.checked ? "text" : "password";
            }




            function updateSubmitButton() {
                var submitButton = document.querySelector('button[type="submit"]');
                var formIsValid = validateVendorFirstName() && validateVendorLastName() && validateVendorMobileNumber() && validateVendorEmail() && validateVendorProductType() && validateVendorFirstPaymentDate() && validatePassword() && checkPasswordMatch();
                submitButton.disabled = !formIsValid;

                console.log("Update submit button called. Form is valid: ", formIsValid);
            }
        </script>

    </head>

    <body>

        <h1>Create Vendor Account, <?php echo $admin_userid  ?>! </h1>

        <form action="admin_create_vendor_account_1.php" method="post" onsubmit="return validateForm()">


            <h2>Vendor Information</h2>

            <label for="vendor_first_name">First Name</label>
            <input type="text" name="vendor_first_name" id="vendor_first_name" maxlength="35" value="<?php echo isset($_SESSION['vendor_first_name']) ? $_SESSION['vendor_first_name'] : ''; ?>" required oninput="capitalizeFirstName(); validateVendorFirstName(); updateSubmitButton()">
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
            <label for="vendor_last_name">Last Name</label>
            <input type="text" name="vendor_last_name" id="vendor_last_name" maxlength="35" value="<?php echo isset($_SESSION['vendor_last_name']) ? $_SESSION['vendor_last_name'] : ''; ?>" required oninput="capitalizeLastName();validateVendorLastName(); updateSubmitButton()">
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
            <label for="vendor_stall_number">Stall No:</label>
            <input type="number" id="vendor_stall_number" name="vendor_stall_number" value="<?php echo isset($_SESSION['vendor_stall_number']) ? $_SESSION['vendor_stall_number'] : ''; ?>" required readonly><br />

            <label for="vendor_mobile_number">Mobile Number</label>
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
            <label for="vendor_email">Email:</label>
            <input type="email" name="vendor_email" id="vendor_email" maxlength="254" value="<?php echo isset($_SESSION['vendor_email']) ? $_SESSION['vendor_email'] : ''; ?>" required oninput="validateVendorEmail(); updateSubmitButton()">
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
            <label for="vendor_product">Products:</label>
            <select name="vendor_product" id="vendor_product" required onchange="validateVendorProductType(); updateSubmitButton()">
                <option value="" disabled selected>Select Product Type</option>
                <option value="Wet" <?php echo (isset($_SESSION['vendor_product_type']) && $_SESSION['vendor_product_type'] == 'Wet') ? 'selected' : ''; ?>>Wet</option>
                <option value="Dry" <?php echo (isset($_SESSION['vendor_product_type']) && $_SESSION['vendor_product_type'] == 'Dry') ? 'selected' : ''; ?>>Dry</option>
                <option value="Other" <?php echo (isset($_SESSION['vendor_product_type']) && $_SESSION['vendor_product_type'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select><span style="color: red;" id="vendor_product_type_error_span">
                <?php
                if (isset($_SESSION['vendor_product_type_error'])) {
                    echo $_SESSION['vendor_product_type_error'];
                    // Unset the session variable after displaying the error
                    unset($_SESSION['vendor_product_type_error']);
                }
                ?>
            </span><br />

            <!--
            <label for="Vendor Payment basis">Vendor Payment Basis:</label>
            <select name="vendor_payment_basis" required>
                <option value="" disabled selected>Select Payment Basis</option>
                <option value="Daily" <?php //echo (isset($_SESSION['vendor_payment_basis']) && $_SESSION['vendor_payment_basis'] == 'Daily') ? 'selected' : ''; 
                                        ?>>Daily</option>
                <option value="Monthly" <?php //echo (isset($_SESSION['vendor_payment_basis']) && $_SESSION['vendor_payment_basis'] == 'Monthly') ? 'selected' : ''; 
                                        ?>>Monthly</option>
            </select><br />
            -->




            <label for="vendor_first_payment_date">Select Start of Billing Period:</label>
            <input type="date" id="vendor_first_payment_date" name="vendor_first_payment_date" value="<?php echo isset($_SESSION['vendor_first_payment_date']) ? $_SESSION['vendor_first_payment_date'] : ''; ?>" required onkeydown="return false" required onchange="validateVendorFirstPaymentDate(); updateSubmitButton()">
            <span style="color: red;" id=" vendor_first_payment_date_error_span">
                <?php

                if (isset($_SESSION['vendor_first_payment_date_error'])) {
                    echo $_SESSION['vendor_first_payment_date_error'];
                    // Unset the session variable after displaying the error
                    unset($_SESSION['vendor_first_payment_date_error']);
                }
                ?>
            </span><br />


            <br />
            <br />


            <h2>Vendor Account</h2>
            <label for="vendor_userid">User ID</label>
            <input type="text" id="vendor_userid" name="vendor_userid" value="<?php echo $new_vendor_userid = generateUserID($pdo); ?>" readonly><br />

            <label for="vendor_password">Password</label>
            <input type="password" id="vendor_password" name="vendor_password" placeholder="8-16 characters" maxlength="16" oninput="validatePassword(); checkPasswordMatch(); updateSubmitButton()">
            <input type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
            <label for="showPassword">Show Password</label>

            <span style="color:red" id="passwordValidationMessage"> </span> <br />


            <label for="vendor_confirm_password">Confirm Password</label>
            <input type="password" id="vendor_confirm_password" name="vendor_confirm_password" maxlength="16" required oninput="checkPasswordMatch(); updateSubmitButton()">
            <span id="passwordMatchMessage"></span><br />



            <button type="submit" disabled>Submit</button>
        </form>



        <a href="interactive_map.php?cancel=1">Back</a>

        <a href=admin_logout.php>
            <h1>LOGOUT</h1>
        </a>
    </body>


    </html>
<?php } else {
    header("location:admin_logout.php");
}
