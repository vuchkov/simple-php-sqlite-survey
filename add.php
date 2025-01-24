<?php
include_once 'config.php';
//$db = new DB();

$body = <<<EOT
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }
    .answers-container {
        margin-top: 10px;
    }
    .answer-field {
        margin-bottom: 5px;
    }
</style>
<form action="api.php" method="post" class="">
    <label for="poll-question">Poll Question:</label><br>
    <input type="text" id="poll-question" name="poll_question" required><br><br>

    <div id="answers-container" class="answers-container">
        <label>Answers:</label><br>
        <input type="text" name="answers[]" placeholder="Enter an answer" class="answer-field"><br>
    </div>
    <button type="button" class="btn btn-secondary" onclick="addAnswerField()">Add Another Answer</button><br><br>
    <button type="submit" class="btn btn-primary btn-lg">Submit Poll</button>
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

$page = new Page('Create a Poll', $body);
$page->render();
