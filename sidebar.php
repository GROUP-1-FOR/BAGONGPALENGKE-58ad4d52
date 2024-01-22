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


    <div class="main-sidebar">
        <ul class="sidebar-outside">
            <div class="profile-container">
                <img class="logo-holder" src="assets\images\sign-in\Santa-Rosa-Logo.svg">
                <img class="profile-design" src="assets\images\sign-in\profile-design.png">
            </div>
        </ul>
        <div class="sidebar-inside">
            <ul class="dashboard-sidebar">

                <li><a href=admin_index.php>
                        <button class="home-index"> Home </button>
                    </a></li>
                <li><a href=admin_vendor_manage_accounts.php>
                        <button class="manage-vendor"> Manage Accounts </button>
                    </a></li>
                <li><a href="admin_send_report.php">
                        <button class="report-management"> Report </button>
                    </a></li>
                <li><a href="admin_faq.php">
                        <button class="help-button"> Help </button>
                    </a></li>
            </ul>
        </div>

        <a class="signout-button" href=admin_logout.php>
            <img class="signout-icon" src="assets\images\sign-in\signout-icon.svg">
            <h1 class="signout-text">Sign out</h1>
        </a>
    </div>