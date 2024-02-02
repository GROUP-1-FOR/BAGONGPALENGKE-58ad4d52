
        function validateForm() {
            return (
                validateVendorFirstName() &&
                validateVendorLastName() &&
                validateVendorMobileNumber() &&
                validateVendorEmail() &&
                validateVendorProductType() &&
                validateVendorFirstPaymentDate() &&
                validatePassword() &&
                checkPasswordMatch()
            );
        }

        function validateVendorFirstName() {
            var vendor_name = document.getElementById("vendor_first_name");

            vendor_name.value = vendor_name.value.replace(/[^A-Za-z\s]/g, '');

            if (vendor_name.value === "") {
                return false;
            }

            return true;
        }

        function validateVendorLastName() {
            var vendor_name = document.getElementById("vendor_last_name");

            vendor_name.value = vendor_name.value.replace(/[^A-Za-z\s]/g, '');

            if (vendor_name.value === "") {
                return false;
            }

            return true;
        }

        function capitalizeFirstName() {
            var firstNameInput = document.getElementById("vendor_first_name");
            var names = firstNameInput.value.split(' ');

            // Capitalize the first letter of each name
            for (var i = 0; i < names.length; i++) {
                names[i] = names[i].charAt(0).toUpperCase() + names[i].slice(1).toLowerCase();
            }

            // Join the names back together with spaces
            firstNameInput.value = names.join(' ');
        }

        function capitalizeLastName() {
            var lastNameInput = document.getElementById("vendor_last_name");
            var names = lastNameInput.value.split(' ');

            // Capitalize the first letter of each name
            for (var i = 0; i < names.length; i++) {
                names[i] = names[i].charAt(0).toUpperCase() + names[i].slice(1).toLowerCase();
            }

            // Join the names back together with spaces
            lastNameInput.value = names.join(' ');
        }


        function validateVendorMobileNumber() {
            var inputElement = document.getElementById("vendor_mobile_number");
            var vendor_mobile_number = inputElement.value;
            var vendor_mobile_number_error_span = document.getElementById("vendor_mobile_number_error_span");

            // Replace non-numeric characters with an empty string
            inputElement.value = vendor_mobile_number.replace(/[^0-9]/g, '');

            // Check if the input contains any non-numeric characters
            if (inputElement.value.length > 0 && !/^09\d{9}$/.test(inputElement.value)) {
                // Show error message
                vendor_mobile_number_error_span.textContent = "Please enter a valid mobile number.";
                return false; // Prevent form submission
            } else {
                // Clear error message
                vendor_mobile_number_error_span.textContent = "";
            }

            if (inputElement.value === "") {
                return false;
            }

            // If the input is valid, you can proceed with form submission
            return true;
        }




        function validateVendorEmail() {
            var vendor_email_input = document.getElementById("vendor_email");
            var vendor_email = vendor_email_input.value.trim().toLowerCase();
            var vendor_email_error_span = document.getElementById("vendor_email_error_span");
            var isEmailTaken = false; // Flag to check if email is taken
        
            // Check if the input is not empty and contains a valid email address
            if (vendor_email.length > 0 && !vendor_email_input.checkValidity()) {
                // Show error message for general email validation
                vendor_email_error_span.textContent = "Please enter a valid email address.";
                return false; // Prevent form submission
            } else {
                // Clear general email error message
                vendor_email_error_span.textContent = "";
            }
        
            // Check if the email ends with gmail.com
            if (vendor_email.length > 0 && !vendor_email.endsWith("@gmail.com")) {
                // Show error message for Gmail.com validation
                vendor_email_error_span.textContent = "only @gmail.com is accepted";
                return false; // Prevent form submission
            } else {
                // Clear Gmail.com email error message
                vendor_email_error_span.textContent = "";
            }
        
            if (vendor_email_input.value === "") {
                return false;
            }
        
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
        
                        if (response.emailTaken) {
                            vendor_email_error_span.textContent = "Email is already taken.";
                            isEmailTaken = true; // Set the flag to true if email is taken
                        } else {
                            vendor_email_error_span.textContent = "";
                        }
                    }
                }
            };
        
            xhr.open("POST", "create_vendor_email_check.php", false); // Use synchronous request for simplicity
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("email=" + encodeURIComponent(vendor_email));
        
            // If the input is valid and email is not taken, you can proceed with form submission
            return !isEmailTaken;
        }
        
        

        function validateVendorProductType() {
            var productType = document.getElementById("vendor_product").value;
            var productType_error_span = document.getElementById("vendor_product_type_error_span");

            if (productType === "") {
                productType_error_span.textContent = "";
                return false; // Prevent form submission
            } else {
                productType_error_span.textContent = "";
            }

            return true;
        }

        function validateVendorFirstPaymentDate() {
            var firstPaymentDate = document.getElementById("vendor_first_payment_date").value;
            var first_payment_error_span = document.getElementById("vendor_first_payment_date_error_span");

            if (firstPaymentDate === "") {
                first_payment_error_span = "";
                return false; // Prevent form submission
            } else {
                first_payment_error_span = "";
            }

            return true;

        }

        function validatePassword() {
            var passwordInput = document.getElementById("vendor_password");
            var password = passwordInput.value;
            var passwordValidationMessage = document.getElementById("passwordValidationMessage");

            // Define the password patterns
            var lengthPattern = /.{8,16}/;
            var uppercasePattern = /[A-Z]/;
            var lowercasePattern = /[a-z]/;
            var digitPattern = /\d/;
            var specialCharPattern = /[!@#$%^&*()_+]/;

            // Check each pattern and provide feedback
            var isValid = true;
            if (!lengthPattern.test(password)) {
                isValid = false;
                passwordValidationMessage.textContent = "Password must be 8-16 characters.";
            } else if (!uppercasePattern.test(password)) {
                isValid = false;
                passwordValidationMessage.textContent = "Include at least one uppercase letter.";
            } else if (!lowercasePattern.test(password)) {
                isValid = false;
                passwordValidationMessage.textContent = "Include at least one lowercase letter.";
            } else if (!digitPattern.test(password)) {
                isValid = false;
                passwordValidationMessage.textContent = "Include at least one number.";
            } else if (!specialCharPattern.test(password)) {
                isValid = false;
                passwordValidationMessage.textContent = "Include at least one special character from the list ! @ # $ % ^ & * ( ) _ +";
            } else {
                passwordValidationMessage.textContent = "";
            }

            return isValid;
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
                        messageElement.innerHTML = "Passwords match";
                        messageElement.style.color = "green";
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

            return password === confirmPassword;
        }

        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("vendor_password");
            var showPasswordCheckbox = document.getElementById("showPassword");

            // Toggle the password visibility
            passwordInput.type = showPasswordCheckbox.checked ? "text" : "password";
        }




        function updateSubmitButton() {
            var submitButton = document.querySelector('button[type="submit"]');
            var formIsValid = validateVendorFirstName() && validateVendorLastName() && validateVendorMobileNumber() && validateVendorEmail() && validateVendorProductType() && validateVendorFirstPaymentDate() && validatePassword() && checkPasswordMatch();
            submitButton.disabled = !formIsValid;

            console.log("Update submit button called. Form is valid: ", formIsValid);
        }
    