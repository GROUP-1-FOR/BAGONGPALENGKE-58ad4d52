admin_index.php<?php
require("config.php");
$_SESSION = [];
session_unset();
session_destroy();
header("location: admin_login.php");
