<?php

require("config.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> VENDOR SIGN IN </title>


</head>

<body>
    <!-- External stylesheet link -->

    <header class=>
        <h2> LOGO </h2>

        <h1 class="title1"> WELCOME TO </h1>
        <h1 class="title2"> SANTA ROSA PUBLIC MARKET </h1>
        <!--<div class="nav">
      <a href="">HOME</a>
      <a href="">BLOG</a>
      <a href="">About</a>
      <a href="">FAQs</a>
      <a href="">Info</a>
      <a href="">Tricks</a>
      <a href="">LOGIn</a>

    </div> -->
    </header>

    <div class="main-content">

        <form class="" action="vendor_login_1.php" method="post" autocomplete="off">
            <label for="Vendor User ID"> Vendor User ID: </label>
            <input type="text" name="vendor_userid" id="vendor_userid" required value=""><br />
            <label for="Password"> Password: </label>
            <input type="password" name="vendor_password" id="vendor_password" required value=""><br />
            <button class="signin-button" type="submit" name="vendor_login_submit">Enter</button>
        </form>

        <a href="vendor_admin_select.php"> Back</a>

    </div>

    <footer>

    </footer>
    <main>

    </main>
</body>

</html>