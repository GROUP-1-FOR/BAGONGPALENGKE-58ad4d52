<?php
require("config.php");

$sql = "SELECT report_message FROM report_bug";
$result = $connect->query($sql);

if ($result->num_rows > 0) {
    // Initialize an array to store phrases
    $phrases = array();

    // Process each row and count phrases
    while ($row = $result->fetch_assoc()) {
        $text = $row['report_message'];

        // Remove non-alphanumeric characters and convert to lowercase
        $cleaned_text = strtolower(preg_replace("/[^a-zA-Z0-9]+/", " ", $text));

        // Split the text into an array of words
        $word_array = explode(" ", $cleaned_text);

        // Determine the length of phrases (e.g., 2 for 2-word phrases)
        $phrase_length = 2;

        // Create and count occurrences of each phrase
        for ($i = 0; $i <= count($word_array) - $phrase_length; $i++) {
            $phrase = implode(" ", array_slice($word_array, $i, $phrase_length));

            if (!empty($phrase)) {
                if (array_key_exists($phrase, $phrases)) {
                    $phrases[$phrase]++;
                } else {
                    $phrases[$phrase] = 1;
                }
            }
        }
    }

    // Sort the array by phrase frequency in descending order
    arsort($phrases);

    // Display the top 10 most used phrases
    $top10 = array_slice($phrases, 0, 10);

    echo "Top 10 most used phrases:<br>";
    foreach ($top10 as $phrase => $count) {
        echo "$phrase: $count<br>";
    }
} else {
    echo "No data found.";
}

$connect->close();
