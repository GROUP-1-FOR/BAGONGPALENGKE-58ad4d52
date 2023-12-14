<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SIGN IN </title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="box-style.css">
    <link rel="stylesheet" type="type/js-style" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
<header><img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src"></header>

    <div class="main-sidebar">
        <ul class="sidebar-outside">
        <div class="profile-container">
        <img class="profile-pic-holder" src="assets\images\sign-in\profile-pic.svg">
            <img class="profile-design" src="assets\images\sign-in\profile-design.png">
        </div>
        </ul>
        <div class="sidebar-inside">
            <ul class="dashboard-sidebar">
            <li><a class="home-index" href=admin_index.php> Home </a></li>
                <li><a class="manage-vendor" href=admin_vendor_manage_accounts.php> Manage Vendor Accounts </a></li>
                <li><a class="report-management" href="admin_send_report.php"> Report Management </a></li>
                <li><a class="help-button" href="admin_faq.php"> Help </a></li>
            </ul>
        </div>

        <div>
            <a href=admin_logout.php>
                <h1 class="logout-button">LOGOUT</h1>
            </a>
        </div>
    </div>
