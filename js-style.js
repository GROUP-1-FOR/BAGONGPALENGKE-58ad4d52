document.addEventListener("DOMContentLoaded", function () {
  var boxes = document.querySelectorAll('.box');

  boxes.forEach(function (box) {
      box.addEventListener('click', function () {
          // Change color to green
          this.classList.toggle('green');

          // Prompt for add button (you can customize this part)
          if (this.classList.contains('green')) {
              var addButton = confirm("Do you want to add something?");
              if (addButton) {
                  // Add your logic for the add button action
                  // This can include an AJAX request to a server or any other functionality
                  console.log("Add button clicked for box " + this.textContent);
              }
          }
      });
  });
});

// Path: js-style.js
function checkPasswordMatch() {
    var password = document.getElementsByName("admin_new_password")[0].value;
    var confirmPassword = document.getElementsByName("admin_confirm_new_password")[0].value;
    var messageElement = document.getElementById("passwordMatchMessage");
    var confirmPasswordInput = document.getElementsByName("admin_confirm_new_password")[0];
    var submitButton = document.querySelector('input[type="submit"]');

    // Enable or disable Confirm Password based on whether Password is empty
    confirmPasswordInput.disabled = password.length === 0;

    // Check if the "Password" field is not empty
    if (password.length > 0) {
        // Check if the "Confirm Password" field is also not empty
        if (confirmPassword.length > 0) {
            // Check if passwords match
            if (password === confirmPassword) {
                // Check if passwords have at least 8 characters
                if (password.length >= 8 && confirmPassword.length >= 8) {
                    messageElement.innerHTML = "Passwords match and meet the minimum length requirement.";
                    messageElement.style.color = "green";
                    submitButton.disabled = false; // Enable the button
                } else {
                    messageElement.innerHTML = "Passwords match but do not meet the minimum length requirement (8 characters).";
                    messageElement.style.color = "red";
                    submitButton.disabled = true; // Enable the button
                }
            } else {
                messageElement.innerHTML = "Passwords do not match.";
                messageElement.style.color = "red";
                submitButton.disabled = true; // Enable the button
            }
        } else {
            // "Confirm Password" field is empty, clear the message
            messageElement.innerHTML = "";
        }
    } else {
        // "Password" field is empty, clear the message and "Confirm Password" field
        messageElement.innerHTML = "";
        confirmPasswordInput.value = "";
    }

    return password === confirmPassword && password.length >= 8 && confirmPassword.length >= 8;
}

function togglePassword() {
    const passwordInput = document.getElementById('passwordInput');

    // Toggle between text and password
    passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
}


