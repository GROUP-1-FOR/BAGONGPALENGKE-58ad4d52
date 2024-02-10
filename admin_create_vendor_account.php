<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

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


if (isset($_GET['cancel_button']) && $_GET['cancel_button'] == 1) {
    unset($_SESSION['vendor_first_name']);
    unset($_SESSION['vendor_last_name']);
    unset($_SESSION['vendor_full_name']);
    unset($_SESSION['vendor_mobile_number']);
    unset($_SESSION['vendor_product_type']);
    unset($_SESSION['vendor_first_payment_date']);
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Vendor Account</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="text-style.css">
    <link rel="stylesheet" type="text/css" href="text-positions.css">
    <link rel="javascript" type="text/script" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

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
            var vendor_name = document.getElementById("vendor_first_name");

            vendor_name.value = vendor_name.value.replace(/[^A-Za-z\s]/g, '');

            if (vendor_name.value === "") {
                return false;
            }

            return true;
        }

        function validateVendorLastName() {
            var vendor_name = document.getElementById("vendor_last_name");

            vendor_name.value = vendor_name.value.replace(/[^A-Za-z\s]/g, '');

            if (vendor_name.value === "") {
                return false;
            }

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
            var inputElement = document.getElementById("vendor_mobile_number");
            var vendor_mobile_number = inputElement.value;
            var vendor_mobile_number_error_span = document.getElementById("vendor_mobile_number_error_span");

            // Replace non-numeric characters with an empty string
            inputElement.value = vendor_mobile_number.replace(/[^0-9]/g, '');

            // Check if the input contains any non-numeric characters
            if (inputElement.value.length > 0 && !/^09\d{9}$/.test(inputElement.value)) {
                // Show error message
                vendor_mobile_number_error_span.textContent = "Please enter a valid mobile number.";
                return false; // Prevent form submission
            } else {
                // Clear error message
                vendor_mobile_number_error_span.textContent = "";
            }

            if (inputElement.value === "") {
                return false;
            }

            // If the input is valid, you can proceed with form submission
            return true;
        }


        function validateVendorEmail() {
            var vendor_email_input = document.getElementById("vendor_email");
            var vendor_email = vendor_email_input.value.trim().toLowerCase();
            var vendor_email_error_span = document.getElementById("vendor_email_error_span");
            var isEmailTaken = false; // Flag to check if email is taken

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

            if (vendor_email_input.value === "") {
                return false;
            }

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);

                        if (response.emailTaken) {
                            vendor_email_error_span.textContent = "Email is already taken.";
                            isEmailTaken = true; // Set the flag to true if email is taken
                        } else {
                            vendor_email_error_span.textContent = "";
                        }
                    }
                }
            };
            xhr.open("POST", "create_vendor_email_check.php", false); // Use synchronous request for simplicity
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("email=" + encodeURIComponent(vendor_email));

            // If the input is valid and email is not taken, you can proceed with form submission
            return !isEmailTaken;
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
                passwordValidationMessage.textContent = "Include at least one special character from the list ! @ # $ % ^ & * ( ) _ +";
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
    <header class="header2"></header>
    <?php include 'sidebar.php'; ?>

    <div class="flex-row">
        <h1 class="manage-account-header">Create Vendor Account, <?php echo $admin_userid  ?>! </h1>
        <div class="create-vendor-form">


            <form action=" admin_create_vendor_account_1.php" method="post" onsubmit="return validateForm()">


                <!-- FIRST BOX -->
                <div class="flex-row-direction">


                    <div class="box-position4">
                        <label class="title-label tl1" for="Vendor First Name">Vendor First Name:</label>
                        <div class="flexbox-row2">
                            <input class="input-info" type="text" name="vendor_first_name" id="vendor_first_name" maxlength="35" value="<?php echo isset($_SESSION['vendor_first_name']) ? $_SESSION['vendor_first_name'] : ''; ?>" required oninput="capitalizeFirstName(); validateVendorFirstName(); updateSubmitButton()">
                            <!-- Di!splay an error message if it exists in the session -->
                            <span class="error-message2" style="color: red;" id="vendor_first_name_error_span">
                        </div>
                        <?php
                        if (isset($_SESSION['vendor_first_name_error'])) {
                            echo $_SESSION['vendor_first_name_error'];
                            // Unset the session variable after displaying the error
                            unset($_SESSION['vendor_first_name_error']);
                        }
                        ?>
                        </span>
                        <br />
                        <label class="title-label tl2" for="Vendor Last Name">Vendor Last Name:</label>
                        <div class="flexbox-row2">
                            <input class="input-info" type="text" name="vendor_last_name" id="vendor_last_name" maxlength="35" value="<?php echo isset($_SESSION['vendor_last_name']) ? $_SESSION['vendor_last_name'] : ''; ?>" required oninput="capitalizeLastName();validateVendorLastName(); updateSubmitButton()">

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
                        </div>
                        <br />
                        <label class="title-label tl3" for="Stall Number">Stall No:</label>
                        <div class="flexbox-row2">
                            <input class="input-info" type="number" id="vendor_stall_number" name="vendor_stall_number" value="<?php echo isset($_SESSION['vendor_stall_number']) ? $_SESSION['vendor_stall_number'] : ''; ?>" required readonly><br />
                        </div>
                        <label class="title-label tl4" for="Mobile Number">Mobile Number:</label>
                        <div class="flexbox-row2">
                            <input class="input-info" type="tel" name="vendor_mobile_number" id="vendor_mobile_number" maxlength="11" placeholder="09XXXXXXXXX" value="<?php echo isset($_SESSION['vendor_mobile_number']) ? $_SESSION['vendor_mobile_number'] : ''; ?>" oninput="validateVendorMobileNumber(); updateSubmitButton()">

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
                        </div>

                        <label class="title-label tl5" for="Email">Email:</label>

                        <div class="flexbox-row2">
                            <input class="input-info" type="email" name="vendor_email" id="vendor_email" maxlength="254" value="<?php echo isset($_SESSION['vendor_email']) ? $_SESSION['vendor_email'] : ''; ?>" required oninput="validateVendorEmail(); updateSubmitButton()">

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
                        </div>

                        <br />
                        <label class="title-label tl6" for="vendor_product">Products:</label>
                        <select class="input-info1" name="vendor_product" id="vendor_product" required onchange="validateVendorProductType(); updateSubmitButton()">>
                            <option class="option" disabled selected>Select Product Type</option>
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





                    </div>
                    <br />
                    <br />


                    <!-- SECOND-BOX -->
                    <div class="box-position5">

                        <label class="title-label tl1" for="vendor_userid" name="vendor_userid">Vendor User ID:</label>
                        <input class=" input-info" type="text" id="vendor_userid" name="vendor_userid" value="<?php echo $new_vendor_userid = generateUserID($pdo); ?>" readonly><br />

                        <!-- <label class="title-label tl9">Password:</label>
                        <input class="input-info" type="password" id="vendor_password" name="vendor_password" placeholder="8-16 characters" maxlength="16" oninput="validatePassword(); checkPasswordMatch(); updateSubmitButton()">
                        <input class="check-box" type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
                        <label class="show-password" for="showPassword">Show Password</label> -->
                        <label class="title-label tl1" for="vendor_first_payment_date">Select Start of Billing Period:</label>
                        <input class="input-info" type="date" id="vendor_first_payment_date" name="vendor_first_payment_date" value="<?php echo isset($_SESSION['vendor_first_payment_date']) ? $_SESSION['vendor_first_payment_date'] : ''; ?>" min="<?php echo date('Y-m-d'); ?>" required onkeydown="return false" required onchange="validateVendorFirstPaymentDate(); updateSubmitButton()">
                        <span style="color: red;" id=" vendor_first_payment_date_error_span">
                            <?php

                            if (isset($_SESSION['vendor_first_payment_date_error'])) {
                                echo $_SESSION['vendor_first_payment_date_error'];
                                // Unset the session variable after displaying the error
                                unset($_SESSION['vendor_first_payment_date_error']);
                            }
                            ?>
                        </span><br />
                        <div class="password-container">
                            <label class="title-label tl1">Password:</label>
                            <input class="input-info" type="password" id="vendor_password" name="vendor_password" placeholder="8-16 characters" maxlength="16" oninput="validatePassword(); checkPasswordMatch(); updateSubmitButton()"><br>
                            <input class="check-box" type="checkbox" id="showPassword" onclick="togglePasswordVisibility()">
                            <label class="show-password" for="showPassword">Show Password</label>
                        </div>

                        <span style="color:red" id="passwordValidationMessage"> </span> <br />



                        <label class="title-label tl1" for="vendor_confirm_password">Confirm Password:</label>
                        <input class="input-info" type="password" id="vendor_confirm_password" name="vendor_confirm_password" maxlength="16" required oninput="checkPasswordMatch(); updateSubmitButton()">
                        <span id="passwordMatchMessage"></span><br />

                        <button class="submit" type="submit" disabled>Submit</button>
                    </div>
            </form><br><br>

            <a class="back-button5" href="?cancel_button=1">
                < Back </a>

        </div>
    </div>

    </div>
    </div>
    </div>

    <footer></footer>
</body>

</html>