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
        {{ pollForm }}
        <br><br>
        <h2>Results</h2>
        <div id="results">Loading...</div>
    </div> 
    <script>
        // Fetch and display poll results
        async function fetchResults() {
            const resultsDiv = document.getElementById('results');
            const pollId = document.getElementById('id');
            const response = await fetch('api.php?id=' + pollId.value);
            const results = await response.json();
            resultsDiv.innerHTML = '';
            for (const [answer, votes] of Object.entries(results)) {
                resultsDiv.innerHTML += '<p>&bull; <b>' + answer + '</b> (' + votes +' votes)</p>';
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
    $title = $poll;
    if (empty($db->getVote($_SESSION['user_id'], $id))) {
        $body = '<form id="pollForm">
                  <div class="mb-3">';
        foreach ($answers as $answer) {
            $body .= '<div class="form-check">
                      <input name="answer" type="radio" value="' . $answer['id'] . '" class="form-check-input" checked="" required="">
                      <label class="form-check-label" for="answer">' . $answer['answer'] . '</label>
                  </div>';
        }
        $body .= '</div>
              <input type="hidden" id="id" name="id" value="' . $id . '">
              <input type="hidden" id="question" name="question" value="' . $poll . '">
              <input type="hidden" id="user_id" name="user_id" value="' . $_SESSION['user_id'] . '">
              <button type="button" onclick="submitPoll()" class="btn btn-primary">Vote</button>
          </form>';
    }
    else $body = '<input type="hidden" id="id" name="id" value="' . $id . '">';
    $html = str_replace('{{ pollForm }}', $body, $html);
}

$page = new Page($title, $html);
$page->render();
