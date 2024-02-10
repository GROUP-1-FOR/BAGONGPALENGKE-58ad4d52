<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $vendor_id = $_SESSION["id"];
    $vendor_userid = $_SESSION["userid"];
} else {
    header("location:vendor_logout.php");
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequently Asked Questions</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="javascript" type="text/script" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <header class="header2"></header>
    <?php include 'sidebar2.php'; ?>

    <div class="flex-row">
        <div class="faq-table">

            <div class="flex-box1">
                <div class="main-container">
                    <br>
                    <ul>
                        <?php
                        // Fetch FAQs from the database
                        $sql = "SELECT vendor_faq_question, vendor_faq_answer FROM vendor_faq_question";
                        $result = $connect->query($sql);

                        if ($result->num_rows > 0) {
                            // Output data of each row
                            $counter = 1; // Initialize a counter
                            while ($row = $result->fetch_assoc()) {
                                $question = htmlspecialchars($row["vendor_faq_question"]);
                                $answer = htmlspecialchars($row["vendor_faq_answer"]);
                        ?>
                                <li>
                                    <strong>
                                        <div class="question" onclick="toggleAnswer(<?php echo $counter; ?>, this)"><?php echo $question; ?>
                                    </strong>


                </div>
                <i>
                    <div class="answer" id="answer_<?php echo $counter; ?>"><?php echo $answer; ?>
                </i>
            </div>

            </li>
    <?php $counter++; // Increment the counter
                            }
                        } else {
                            echo "0 results";
                        }

                        $connect->close();
    ?>
    </ul>



        </div>

    </div>
    <br>
    <br>

    <script>
        function toggleAnswer(counter, element) {
            var answer = document.getElementById('answer_' + counter);
            var isOpen = answer.style.display === 'block' || getComputedStyle(answer).display === 'block';

            // Close all answers if they are open
            var allAnswers = document.querySelectorAll('.answer');
            allAnswers.forEach(function(ans) {
                ans.style.display = 'none';
            });

            // Toggle the display of the clicked answer
            if (!isOpen) {
                answer.style.display = 'block';
            } else {
                answer.style.display = 'none';
            }
        }
    </script>



    <footer></footer>
</body>

</html>