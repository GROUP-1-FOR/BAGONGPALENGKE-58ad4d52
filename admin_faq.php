<?php
require("config.php");

if (isset($_SESSION["id"]) && $_SESSION["login"] === true && isset($_SESSION["userid"])) {
    $admin_id = $_SESSION["id"];
    $admin_userid = $_SESSION["userid"];
} else {
    header("location:admin_logout.php");
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGN IN</title>
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="javascript" type="text/script" href="js-style.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
    <header></header>
    <?php include 'sidebar.php'; ?>

    <div class="flex-row">
        <h2 class="manage-account-header">Frequently Asked Questions</h2>
        <div class="faq-table">

            <div class="flex-box1">
                <div class="main-container">
                    <br>
                    <ul>
                        <?php
                        // Fetch FAQs from the database
                        $sql = "SELECT admin_faq_question, admin_faq_answer FROM admin_faq_question";
                        $result = $connect->query($sql);

                        if ($result->num_rows > 0) {
                            // Output data of each row
                            $counter = 1; // Initialize a counter
                            while ($row = $result->fetch_assoc()) {
                                $question = htmlspecialchars($row["admin_faq_question"]);
                                $answer = htmlspecialchars($row["admin_faq_answer"]);
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
    <?php
                                $counter++; // Increment the counter
                            }
                        } else {
                            echo "0 results";
                        }

                        $connect->close();
    ?>
    </ul>
    <br>
    <br>
    <script>
        function toggleAnswer(counter, element) {
            var answer = document.getElementById('answer_' + counter);
            var isOpen = answer.style.display === 'block' || getComputedStyle(answer).display === 'block';

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

    </div>
    </div>

    </div>


    <footer></footer>
</body>

</html>