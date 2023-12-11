
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>



function handleBoxClick(tableNumber) {
        var confirmAddition = confirm('Add vendor to Stall ' + tableNumber + '?');
        if (confirmAddition) {
            var url = 'admin_create_vendor_account.php?stall_number=' + tableNumber;
            window.location.href = url;
        }
    }

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



function confirmAndArchive(vendorUserId, vendorName, paymentDate, modeOfPayment, transactionId, row) {
    // Display a confirmation dialog with the vendor's name
    var isConfirmed = confirm("Are you sure you want to confirm and archive for vendor: " + vendorName + "?");

    // Check the user's response
    if (isConfirmed) {
        // User confirmed, proceed with the AJAX call
        $.ajax({
            type: "POST",
            url: "confirm_and_archive_db.php",
            data: {
                vendorUserId: vendorUserId,
                vendorName: vendorName,
                paymentDate: paymentDate,
                modeOfPayment: modeOfPayment,
                transactionId: transactionId,
                balance: $(row).closest('tr').find('td:eq(1)').text() // Fetch balance from the second cell of the current row
            },
            success: function(response) {
                alert(response);
                $(row).closest('tr').find('.action-cell').html('Paid');
            },
            error: function() {
                alert("Error confirming payment and archiving");
            }
        });
    } else {
        // User canceled, you can handle this as needed
        console.log("Action canceled by the user");
    }
}


function confirmRemoveAll() {
    var confirmDelete = confirm("Are you sure you want to remove all confirmed payments?");
    if (confirmDelete) {
        removeAllConfirmedPayments();
    }
}

function removeAllConfirmedPayments() {
    $.ajax({
        type: "POST",
        url: "remove_all_confirmed_payments.php", // Create this file to handle the removal
        success: function(response) {
            alert(response); // Display the server's response (if needed)
            // Reload the page or update the table if needed
            location.reload();
        },
        error: function() {
            alert("Error removing confirmed payments");
        }
    });
}
