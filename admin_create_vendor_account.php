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
    <title>Create Vendor Account</title>
    <script src="create_vendor_validation.js"></script>

</head>

<body>

    <h1>Create Vendor Account, <?php echo $admin_userid  ?>! </h1>

    <form action="admin_create_vendor_account_1.php" method="post" onsubmit="return validateForm()">


        <h2>Vendor Information</h2>

        <label for="vendor_first_name">First Name</label>
        <input type="text" name="vendor_first_name" id="vendor_first_name" maxlength="35" value="<?php echo isset($_SESSION['vendor_first_name']) ? $_SESSION['vendor_first_name'] : ''; ?>" required oninput="capitalizeFirstName(); validateVendorFirstName(); updateSubmitButton()">

        <br />
        <label for="vendor_last_name">Last Name</label>
        <input type="text" name="vendor_last_name" id="vendor_last_name" maxlength="35" value="<?php echo isset($_SESSION['vendor_last_name']) ? $_SESSION['vendor_last_name'] : ''; ?>" required oninput="capitalizeLastName();validateVendorLastName(); updateSubmitButton()">

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



    <a href="?cancel_button=1"> Back </a>

    <a href=admin_logout.php>
        <h1>LOGOUT</h1>
    </a>
</body>


</html>