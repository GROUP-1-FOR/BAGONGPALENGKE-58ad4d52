<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            vertical-align: top; /* Adjust the vertical-align property for the table cell */
        }

        .inner-table {
            width: 100%; /* Adjust the width of the inner table as needed */
            margin: 0 auto; /* Center the inner table horizontally */
        }
    </style>
</head>
<body>

<table>
    <tr>
        <td>
            <!-- Content in the outer table cell -->
            Some content in the outer table cell.
        </td>
        <td>
            <!-- Inner table -->
            <table class="inner-table">
                <tr>
                    <td>Data 1</td>
                    <td>Data 2</td>
                </tr>
                <tr>
                    <td>Data 3</td>
                    <td>Data 4</td>
                </tr>
            </table>
        </td>
        <td>
            <!-- More content in the outer table cell -->
            More content in the outer table cell.
        </td>
    </tr>
    <!-- Add more rows or content as needed -->
</table>

</body>
</html>
