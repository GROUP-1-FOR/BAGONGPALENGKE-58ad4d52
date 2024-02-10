<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> SIGN IN </title>
  <link rel="stylesheet" type="text/css" href="index.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <style>
    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      /* Semi-transparent black overlay */
      backdrop-filter: blur(10px);
      /* background-color: gray; */

      /* Add a blur effect */
      z-index: 999;
    }

    .notification {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 80%;
      /* Adjust the width as needed */
      max-width: 400px;
      /* Set a maximum width if desired */
      background-color: #808080;
      /* Gray background color */
      color: white;
      text-align: center;
      padding: 20px;
      z-index: 1000;
      border-radius: 10px;
      /* Add rounded corners */
    }

    .maroon-background {
      background-color: maroon;
      /* Maroon background color */
    }

    .resizable-image {
      max-width: 100%;
      /* Ensure the image doesn't exceed its natural size */
      height: auto;
      /* Maintain the aspect ratio */
    }
  </style>
</head>

<body class="bagongpalengke-bg">
  <header>
    <img src="assets\images\sign-in\Santa-Rosa-Logo.svg" class="logo-src resizable-image">
  </header>
  <!-- <div class="overlay" id="overlay"></div>
  <div id="desktopModeNotification" class="notification">
    <p>Please switch to desktop mode to proceed.</p>
  </div> -->

  <div class="website-title">
     <h1 class="title1"> WELCOME TO </h1>
     <h1 class="title2"> SANTA ROSA PUBLIC MARKET </h1>
  </div>
  <div>
    <img class="tech-line-tr resizable-image" src="assets\\images\\sign-in\\tech-line-tr.png" alt="tech-line-tr">
    <img class="tech-line-bl resizable-image" src="assets\\images\\sign-in\\tech-line-bl.png" alt="tech-line-bl">
    <img class="front-layer resizable-image" src="assets\\images\\sign-in\\front.svg" alt="front-layer">
    <img class="back-layer resizable-image" src="assets\\images\\sign-in\\back.svg" alt="back-layer">
  </div>
  <div>
    <a href="vendor_login.php"><button class="vendor-button"> VENDOR </button></a>
    <a href="admin_login.php"><button class="admin-button"> TREASURY </button></a>
    <a href="first_signin.php"><button class="back-button"> &lt; BACK </button></a>
  </div>

  <script>
    function checkScreenSize() {
      const overlay = document.getElementById('overlay');
      const notification = document.getElementById('desktopModeNotification');
      const bodyElement = document.body;
      const zoomLevel = window.devicePixelRatio || 1; // Get the device's pixel ratio (zoom level)

      if (window.innerWidth < 768 && zoomLevel >= 1.25 || window.innerWidth < 768 || window.innerHeight > 897 || window.innerHeight < 574) {
        // Apply blur for screen width below 768 pixels and zoom level 125% and above
        overlay.style.display = 'block';
        notification.style.display = 'block';
        bodyElement.classList.add('maroon-background');

      } else {
        overlay.style.display = 'none';
        notification.style.display = 'none';
        bodyElement.classList.remove('maroon-background');
      }
    }

    // Check on window resize
    window.addEventListener('resize', checkScreenSize);

    // Initial check on page load
    document.addEventListener('DOMContentLoaded', checkScreenSize);
  </script>
  <section> </section>
  <footer> </footer>
</body>

</html>