<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];

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

    // Create 
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=bagong_palengke_db", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }


?>

    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Create Vendor Account</title>
    </head>

    <body>

        <h1>Create Vendor Account, <?php echo $admin_userid  ?>! </h1>

        <form action="admin_create_vendor_account_1.php" method="post">


            <h2>Vendor Information</h2>

            <label for="Vendor Name">Vendor Name</label>
            <input type="text" name="vendor_name" required> <br />

            <label for="Stall Number">Stall No:</label>
            <input type="number" name="vendor_stall_number" required><br />

            <label for="Mobile Number">Mobile Number</label>
            <input type="tel" name="vendor_mobile_number" maxlength="11" placeholder="09XXXXXXXXX" required><br />

            <label for="Email">Email:</label>
            <input type="email" name="vendor_email" required><br />

            <label for="Product Type">Products:</label>
            <select name="vendor_product" required>
                <option value="" disabled selected>Select Product Type</option>
                <option value="Wet">Wet</option>
                <option value="Dry">Dry</option>
                <option value="Other">Other</option>
            </select><br />

            <br />
            <br />


            <h2>Vendor Account</h2>
            <label>Vendor User ID</label>
            <input type="text" name="vendor_userid" value="<?php echo $new_vendor_userid = generateUserID($pdo); ?>" readonly><br />

            <label>Password</label>
            <input type="password" name="vendor_password" required><br />

            <label>Confirm Password</label>
            <input type="password" name="vendor_confirm_password" required oninput="checkPasswordMatch()">
            <span id="passwordMatchMessage"></span><br />

            <script>
                function checkPasswordMatch() {
                    var password = document.getElementsByName("vendor_password")[0].value;
                    var confirmPassword = document.getElementsByName("vendor_confirm_password")[0].value;
                    var messageElement = document.getElementById("passwordMatchMessage");
                    var submitButton = document.querySelector('button[type="submit"]');

                    if (password === confirmPassword) {
                        messageElement.innerHTML = "Passwords match";
                        messageElement.style.color = "green";
                        submitButton.disabled = false; // Enable the button
                    } else {
                        messageElement.innerHTML = "Passwords do not match";
                        messageElement.style.color = "red";
                        submitButton.disabled = true; // Disable the button
                    }
                }
            </script>

            <button type="submit" disabled>Submit</button>


        </form>



        <a href=admin_index.php>
            <h1>BACK</h1>
        </a>

        <a href=admin_logout.php>
            <h1>LOGOUT</h1>
        </a>
    </body>


    </html>
<?php } else {
    header("location:admin_login.php");
}
