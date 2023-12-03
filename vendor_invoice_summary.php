<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Summary</title>
</head>
<body>

    <div id="invoice">
        <h2>Invoice Summary</h2>
        <p><strong>Address:</strong> <span id="address">123 Main Street, Cityville</span></p>
        <p><strong>Name:</strong> <span id="name">John Doe</span></p>
        <p><strong>Total Balance to be Paid:</strong> $<span id="totalBalance">100.00</span></p>
        <button onclick="payInvoice()">Pay</button>
    </div>

    <script>
        // Function to update invoice details dynamically
        function updateInvoiceDetails(address, name, totalBalance) {
            document.getElementById('address').innerText = address;
            document.getElementById('name').innerText = name;
            document.getElementById('totalBalance').innerText = totalBalance;
        }

        // Function to handle the payment process
        function payInvoice() {
            // Add your payment processing logic here
            alert('Payment successful!');
        }

        // Example: Update invoice details
        updateInvoiceDetails('456 Oak Avenue, Townsville', 'Jane Smith', '150.00');
    </script>

</body>
</html>