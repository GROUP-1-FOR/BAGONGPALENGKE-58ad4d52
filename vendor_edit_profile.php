<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];
} else {
    header("location:vendor_logout.php");
}

// Fetch user details from the database based on the session user ID
$query = "SELECT vendor_first_name, vendor_last_name, vendor_mobile_number, vendor_email FROM vendor_sign_in WHERE vendor_userid = '$userid'";
$result = mysqli_query($connect, $query);

if ($result) {
    $userDetails = mysqli_fetch_assoc($result);

    // Assign retrieved values to variables
    $vendorFirstName = $userDetails['vendor_first_name'];
    $vendorLastName = $userDetails['vendor_last_name'];
    $vendorMobileNumber = $userDetails['vendor_mobile_number'];
    $vendorEmail = $userDetails['vendor_email'];
} else {
    // Handle database query error
    die("Database query failed: " . mysqli_error($connect));
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
    <link rel="stylesheet" type="text/css" href="text-positions.css">
    <link rel="javascript" type="text/script" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

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

        function hasFormChanged() {
            // Check if any of the form fields have changed
            return (
                document.getElementById("vendor_first_name").value !== initialVendorFirstName ||
                document.getElementById("vendor_last_name").value !== initialVendorLastName ||
                document.getElementById("vendor_mobile_number").value !== initialVendorMobileNumber ||
                document.getElementById("vendor_email").value !== initialVendorEmail
            );
        }

        function updateSubmitButton() {
            var submitButton = document.querySelector('button[type="submit"]');
            var firstName = document.getElementById("vendor_first_name").value;
            var lastName = document.getElementById("vendor_last_name").value;
            var mobileNumber = document.getElementById("vendor_mobile_number").value;
            var email = document.getElementById("vendor_email").value;

            // Check if any of the fields are empty
            submitButton.disabled = !hasFormChanged() || firstName === "" || lastName === "" || mobileNumber === "" || email === "";

            // Check if vendor_userid exists
            <?php
            $queryCheckVendor = "SELECT COUNT(*) FROM vendor_edit_profile WHERE vendor_userid = '$userid'";
            $resultCheckVendor = mysqli_query($connect, $queryCheckVendor);
            $countVendor = mysqli_fetch_row($resultCheckVendor)[0];
            ?>
            submitButton.disabled = submitButton.disabled || <?php echo $countVendor; ?> > 0;
        }

        function validateForm() {
            return hasFormChanged() && validateVendorFirstName() && validateVendorLastName() && validateVendorMobileNumber() && validateVendorEmail();
        }
    </script>

</head>

<body>
    <header class="header2"></header>
    <?php include 'sidebar2.php'; ?>
    <br>
    <br>
    <div class="flex-row">
        <h2 class="manage-account-header">Update Vendor Account, <?php echo $userid  ?>!</h2>
        <div class="create-vendor-form">

            <form action="vendor_edit_profile_1.php" method="post" onsubmit="return validateForm()">

                <div>
                    <!-- FIRST BOX -->
                    <div class="flex-row-direction">


                        <div class="box-position2">
                            <div class="flexbox-column">
                                <label class="title-label tl1" for="Vendor First Name">Vendor First Name:</label>
                                <input class="input-info input-info-margin" type="text" name="vendor_first_name" id="vendor_first_name" required oninput="validateVendorFirstName(); updateSubmitButton()" value="<?php echo $vendorFirstName; ?>">
                                <!-- Display an error message if it exists in the session -->
                                <span class="error-message" style="color: red;" id="vendor_first_name_error_span">

                                    <?php
                                    if (isset($_SESSION['vendor_first_name_error'])) {
                                        echo $_SESSION['vendor_first_name_error'];
                                        // Unset the session variable after displaying the error
                                        unset($_SESSION['vendor_first_name_error']);
                                    }
                                    ?>
                                </span>
                            </div>
                            <br />


                            <label class="title-label tl1" for="Vendor Last Name">Vendor Last Name:</label>
                            <input class="input-info" type="text" name="vendor_last_name" id="vendor_last_name" required oninput="validateVendorLastName(); updateSubmitButton()" value="<?php echo $vendorLastName; ?>">
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


                        <div class="box-position3">
                            <label class="title-label tl1" for="Mobile Number">Mobile Number:</label>

                            <div>
                                <input class="input-info" type="tel" name="vendor_mobile_number" id="vendor_mobile_number" maxlength="11" placeholder="09XXXXXXXXX" oninput="validateVendorMobileNumber(); updateSubmitButton()" value="<?php echo $vendorMobileNumber; ?>">
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

                            <br />
                            <label class="title-label tl1" for="Email">Email:</label>
                            <input class="input-info" type="email" name="vendor_email" id="vendor_email" required oninput="validateVendorEmail(); updateSubmitButton()" value="<?php echo $vendorEmail; ?>">
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
                            <br />
            </form>
            <center><button class="submit-btn1" type="submit" disabled>Submit</button></center>
        </div>
    </div>
    <script>
        function validateForm() {
            return validateVendorFirstName() && validateVendorLastName() && validateVendorMobileNumber() && validateVendorEmail() && checkPasswordMatch();
        }
    </script>
    <br><br>
    </div>


    </div>
    </div>

    <footer></footer>
</body>

</html>