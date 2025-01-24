<?php
session_start();
if (empty($_SESSION['user_id']) || empty($_GET['id'])) {
    header('Location: /');
    exit;
}
$id = ((int)trim($_GET['id']));

include_once 'config.php';

$title = 'Survey';
$html = <<<EOT
    <div style="text-align:left;">
        <form id="pollForm">
            {{ answers }}
            <button type="button" onclick="submitPoll()" class="btn btn-primary">Vote</button>
        </form>
        <br><br>
        <h2>Results</h2>
        <div id="results">Loading...</div>
    </div> 
    <script>
        // Fetch and display poll results
        async function fetchResults() {
            const response = await fetch('api.php');
            const results = await response.json();
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = '';
            for (const [answer, votes] of Object.entries(results)) {
                resultsDiv.innerHTML += '<p>' + votes +' votes</p>';
            }
        }
    
        // Submit poll vote
        async function submitPoll() {
            const form = document.getElementById('pollForm');
            const formData = new FormData(form);
            const response = await fetch('api.php', {
                method: 'POST',
                body: formData,
            });
            if (response.ok) {
                alert('Thank you for voting!');
                fetchResults(); // Refresh results
            } else {
                alert('Something went wrong. Please try again.');
            }
        }
    
        // Load initial results
        fetchResults();
    </script>
EOT;

$db = new DB();
$poll = $db->getPoll($id);
$answers = $db->getAnswers($id);

if (empty($poll) || empty($answers)) {
    $html = 'There are no poll or poll\'s answers.';
}
else {
    $title = $poll; $body = '';
    foreach ($answers as $answer) {
        $body .= '<div class="mb-3"><div class="form-check">
                      <input name="answer" type="radio" value="'.$answer['id'].'" class="form-check-input" checked="" required="">
                      <label class="form-check-label" for="answer">'.$answer['answer'].'</label>
                  </div></div>';
    //'<label><input type="radio" name="answer" value="'.$answer['id'].'">'.$answer['answer'].'</label><br>';
    }
    $html = str_replace('{{ answers }}', $body, $html);
}

$page = new Page($title, $html);
$page->render();
