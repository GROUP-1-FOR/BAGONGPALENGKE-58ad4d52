<?php
require("config.php");
require("admin_check_login.php");

?>
<!DOCTYPE html>
<html>

<head>
    <title>Feature 2</title>
</head>

<body>
    <h1>VIEW MARKET RENTAL STALLS, <?php echo $admin_userid  ?>! </h1>

    <a href=admin_index.php>
        <h1>BACK</h1>
    </a>



    <a href=admin_logout.php>
        <h1>LOGOUT</h1>
    </a>
</body>


</html>