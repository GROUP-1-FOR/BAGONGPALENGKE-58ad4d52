<?php
require("config.php");
if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $id = $_SESSION["id"];
    $userid = $_SESSION["userid"];

?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Feature 5</title>
    </head>

    <body>
        <h1>FEATURE 5<?php echo $userid  ?>! </h1>

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
