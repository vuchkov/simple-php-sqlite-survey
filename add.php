<?php
if (empty($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

include_once 'config.php';
$title = 'Create a Poll';

if (($_SERVER['REQUEST_METHOD'] === 'POST') && !empty($question = trim($_POST['question'])) && !empty($_POST['answers'])) {
    $db = new DB();
    $question_id = $db->setPoll($question);
    if (!empty($question_id)) {
        $answers = $_POST['answers'];
        $db->setAnswers($question_id, $answers);
        $title = 'Poll Created Successfully!';
        $body = "<p><strong>Question:</strong> " . htmlspecialchars($question) . "</p>"
            . "<p><strong>Answers:</strong></p>"
            . "<ul>";
        foreach ($answers as $answer)
            if (!empty(trim($answer)))
                $body .= "<li>" . htmlspecialchars(trim($answer)) . "</li>";
        $body .= '</ul><br><br><a href="/" class="btn btn-secondary">Go to Homepage</a>';
    }
    else
        $body = 'Please enter a valid question! <a href="/add.php" class="btn btn-secondary">Try again</a>';
}
else {
    $body = <<<EOT
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .answers-container { margin-top: 10px; }
            .answer-field { margin-bottom: 5px; }
        </style>
        <form action="/add.php" method="post">
            <label for="question">Poll Question:</label><br>
            <input type="text" id="question" name="question" required><br><br>
            <div id="answers-container" class="answers-container">
                <label>Answers:</label><br>
                <input type="text" name="answers[]" placeholder="Enter an answer" class="answer-field"><br>
            </div>
            <button type="button" class="btn btn-secondary" onclick="addAnswerField()">Add Another Answer</button><br><br>
            <button type="submit" class="btn btn-primary btn-lg">Submit Poll</button>
            <br><br>
            <a href="/" class="btn btn-link">Cancel</a>
        </form>
        <script>
            function addAnswerField() {
                const container = document.getElementById('answers-container');
                const inputField = document.createElement('input');
                inputField.type = 'text';
                inputField.name = 'answers[]';
                inputField.placeholder = 'Enter an answer';
                inputField.classList.add('answer-field');
                container.appendChild(inputField);
                const separator = document.createElement('br');
                container.appendChild(separator);
            }
        </script>
EOT;
}

$page = new Page($title, $body);
$page->render();
