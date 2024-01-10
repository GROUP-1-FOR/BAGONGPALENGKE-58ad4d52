
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
          this.classList.toggle('yellow');

          // Prompt for add button (you can customize this part)
          if (this.classList.contains('blue')) {
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
                    // messageElement.style.color = "green";
                    submitButton.disabled = false; // Enable the button
                } else {
                    messageElement.innerHTML = "Passwords match but do not meet the minimum length requirement (8 characters).";
                    // messageElement.style.color = "red";
                    submitButton.disabled = true; // Enable the button
                }
            } else {
                messageElement.innerHTML = "Passwords do not match.";
                // messageElement.style.color = "red";
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


function editAnnouncement(announcementId) {
    // Redirect to the edit page with the announcementId
    window.location.href = "admin_edit_announcement.php?id=" + announcementId;
}

function removeAnnouncement(announcementId) {
    // Redirect to the remove page with the announcementId
    var confirmed = confirm("Are you sure you want to remove this announcement?");
    if (confirmed) {
        window.location.href = "admin_remove_announcement.php?id=" + announcementId;
    }
}

function validateForm() {
    var title = document.getElementById("admin_announcement_title").value;
    var subject = document.getElementById("admin_announcement_subject").value;
    var message = document.getElementById("admin_announcement").value;
    var date = document.getElementById("admin_announcement_time").value;

    document.getElementById("error_title").innerHTML = "";
    document.getElementById("error_subject").innerHTML = "";
    document.getElementById("error_message").innerHTML = "";
    document.getElementById("error_date").innerHTML = "";

    var isValid = true;

    if (title.trim() === "") {
        document.getElementById("error_title").innerHTML = "Title is required";
        isValid = false;
    } else if (title.length > 50) {
        document.getElementById("error_title").innerHTML = "Title cannot exceed 50 characters";
        isValid = false;
    }

    if (subject.trim() === "") {
        document.getElementById("error_subject").innerHTML = "Subject is required";
        isValid = false;
    } else if (subject.length > 100) {
        document.getElementById("error_subject").innerHTML = "Subject cannot exceed 100 characters";
        isValid = false;
    }

    if (message.trim() === "") {
        document.getElementById("error_message").innerHTML = "Message is required";
        isValid = false;
    } else if (message.length > 500) {
        document.getElementById("error_message").innerHTML = "Message cannot exceed 500 characters";
        isValid = false;
    }

    if (date.trim() === "") {
        document.getElementById("error_date").innerHTML = "Date is required";
        isValid = false;
    }

    return isValid;
}

function updateCounter(inputId, counterId, maxLength) {
    var input = document.getElementById(inputId);
    var counter = document.getElementById(counterId);
    var currentChars = input.value.length;
    var remainingChars = maxLength - currentChars;

    counter.innerHTML = currentChars + '/' + maxLength;
}
function validateVendorFirstName() {
    var vendor_name = document.getElementById("vendor_first_name").value;
    var vendor_name_error_span = document.getElementById("vendor_first_name_error_span");

    // Check if the input contains any numbers or symbols
    if (vendor_name.length > 0 && /[0-9!@#$%^&*(),.?":{}|<>]/.test(vendor_name)) {
        vendor_name_error_span.textContent = "Please enter the vendor name without numbers or symbols.";
        return false; // Prevent form submission
    } else {
        vendor_name_error_span.textContent = "";
    }

    // If the input is valid, you can proceed with form submission
    return true;
}

function validateVendorLastName() {
    var vendor_name = document.getElementById("vendor_last_name").value;
    var vendor_name_error_span = document.getElementById("vendor_last_name_error_span");

    // Check if the input contains any numbers or symbols
    if (vendor_name.length > 0 && /[0-9!@#$%^&*(),.?":{}|<>]/.test(vendor_name)) {
        vendor_name_error_span.textContent = "Please enter the vendor name without numbers or symbols.";
        return false; // Prevent form submission
    } else {
        vendor_name_error_span.textContent = "";
    }

    // If the input is valid, you can proceed with form submission
    return true;
}


function validateVendorMobileNumber() {
    var vendor_mobile_number = document.getElementById("vendor_mobile_number").value;
    var vendor_mobile_number_error_span = document.getElementById("vendor_mobile_number_error_span");

    // Check if the input contains any non-numeric characters
    if (vendor_mobile_number.length > 0 && !/^09\d{9}$/.test(vendor_mobile_number)) {
        // Show error message
        vendor_mobile_number_error_span.textContent = "Please enter a valid mobile number.";
        return false; // Prevent form submission
    } else {
        // Clear error message
        vendor_mobile_number_error_span.textContent = "";
    }

    // If the input is valid, you can proceed with form submission
    return true;
}

function validateVendorEmail() {
    var vendor_email = document.getElementById("vendor_email").value;
    var vendor_email_error_span = document.getElementById("vendor_email_error_span");

    // Use the built-in email validation
    if (vendor_email.length > 0 && !document.getElementById("vendor_email").checkValidity()) {
        // Show error message
        vendor_email_error_span.textContent = "Please enter a valid email address.";
        return false; // Prevent form submission
    } else {
        // Clear error message
        vendor_email_error_span.textContent = "";
    }

    // If the input is valid, you can proceed with form submission
    return true;
}

function checkPasswordMatch() {
    var password = document.getElementsByName("vendor_password")[0].value;
    var confirmPassword = document.getElementsByName("vendor_confirm_password")[0].value;
    var messageElement = document.getElementById("passwordMatchMessage");
    var confirmPasswordInput = document.getElementsByName("vendor_confirm_password")[0];

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
                } else {
                    messageElement.innerHTML = "Passwords match but do not meet the minimum length requirement (8 characters).";
                    messageElement.style.color = "red";
                }
            } else {
                messageElement.innerHTML = "Passwords do not match.";
                messageElement.style.color = "red";
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




function updateSubmitButton() {
    var submitButton = document.querySelector('button[type="submit"]');


    var formIsValid = validateVendorFirstName() && validateVendorLastName() && validateVendorMobileNumber() && validateVendorEmail() && checkPasswordMatch();
    submitButton.disabled = !formIsValid;
}