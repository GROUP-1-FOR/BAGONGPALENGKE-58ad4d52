<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Frequently Asked Questions</title>
        <style>
            body {
                text-align: center;
                margin: 50px;
                background-color: #f2f2f2;
            }

            #faq-container {
                width: 50%;
                margin: auto;
                border: 3px solid #ccc;
                padding: 20px;
                background-color: #fff;
            }

            #faq-container h1 {
                color: #850F16;
            }

            ul {
                list-style-type: none;
                /* Remove bullets */
                padding: 0;
            }

            .question {
                cursor: pointer;
                padding: 10px;
                background-color: #f0f0f0;
                margin-bottom: 5px;
                border: 1px solid #ccc;
                border-radius: 5px;
                position: relative;
                overflow: hidden;
            }

            .question::before {
                content: '>';
                /* Always 'V' */
                position: absolute;
                right: 10px;
                font-weight: bold;
                transition: transform 0.3s;
                /* Add transition property */
            }

            .question.clicked::before {

                /* Change to 'V' when clicked, points down */
                transform: rotate(90deg);
                /* Rotate the arrow when clicked */
            }

            .question.clicked {
                background-color: #b3b3b3;
                /* Change the color when clicked */
            }

            .answer {
                display: none;
                padding: 10px;
                background-color: #e0e0e0;
                margin-top: 5px;
                border: 1px solid #ccc;
                border-radius: 5px;
                max-height: none;
                /* No fixed max-height */
            }
        </style>
    </head>

    <body>

        <div id="faq-container">
            <h1>Frequently Asked Questions</h1>

            <ul>
                <?php
                // Fetch FAQs from the database
                $sql = "SELECT admin_faq_question, admin_faq_answer FROM admin_faq";
                $result = $connect->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        $question = $row["admin_faq_question"];
                        $answer = $row["admin_faq_answer"];
                ?>
                        <li>
                            <div class="question" onclick="toggleAnswer('<?php echo $question; ?>', this)"><?php echo $question; ?></div>
                            <div class="answer" id="<?php echo $question; ?>"><?php echo $answer; ?></div>
                        </li>
                <?php
                    }
                } else {
                    echo "0 results";
                }

                $connect->close();
                ?>
            </ul>

            <h3><i>Select Topic</i></h3>
            <a href=admin_index.php>
                <h3>Back</h3>
            </a>

            <script>
                function toggleAnswer(question, element) {
                    var answer = document.getElementById(question);
                    var isOpen = answer.style.display === 'block';

                    // Close all answers
                    var questions = document.querySelectorAll('.question');
                    questions.forEach(function(q) {
                        q.classList.remove('clicked');
                        q.nextElementSibling.style.display = 'none';
                    });

                    // Open the clicked answer if it was closed
                    if (!isOpen) {
                        element.classList.add('clicked');
                        answer.style.display = 'block';
                    }
                }
            </script>

        </div>

    </body>

    </html>




<?php
} else {
    header("location:admin_logout.php");
}
?>