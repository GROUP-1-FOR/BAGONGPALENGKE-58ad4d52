<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Scrollbar Example</title>
    <style>
        .scroll-container {
            width: 300px;
            height: 200px;
            overflow: auto;
            scrollbar-width: thin; /* For Firefox and Edge */
            scrollbar-color: maroon grey; /* For Firefox and Edge */
            /* For WebKit browsers (Chrome, Safari) */
            scrollbar-face-color: maroon;
            scrollbar-track-color: grey;
        }

        /* For WebKit browsers (Chrome, Safari) */
        .scroll-container::-webkit-scrollbar {
            width: 12px;
        }

        .scroll-container::-webkit-scrollbar-thumb {
            background-color: maroon;
            border-radius: 10px;
        }

        .scroll-container::-webkit-scrollbar-track {
            background-color: grey;
        }

        /* Some content to make the scrollbar appear */
        .content {
            height: 400px;
        }
    </style>
</head>
<body>

<div class="scroll-container">
    <div class="content">
        <!-- Your content goes here -->
        <p>This is a long content that will make the scrollbar appear.</p>
    </div>
</div>

</body>
</html>
