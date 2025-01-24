<?php

if (empty($_POST['id']) && empty($_GET['id'])) {
    http_response_code(404);
    exit;
}
$poll_id = !empty($_POST['id']) ? ((int)$_POST['id']) : (int)$_GET['id'];

include_once 'config.php';
$db = new Db();

if (($_SERVER['REQUEST_METHOD'] === 'POST') && !empty($_POST['answer']) && !empty($_POST['user_id']) && ($poll_id > 0)) {
    $answer_id = (int) $_POST['answer'];
    $user_id = (int) $_POST['user_id'];
    $db->setVote($user_id, $poll_id, $answer_id);
}

$answers = $db->getAnswers($poll_id);
$votes = $db->votes($poll_id, $answers);
foreach ($answers as $answer) {
    $data[$answer['answer']] = $votes[$answer['id']][1];
}

// Handle GET request to fetch poll results
header('Content-Type: application/json');
http_response_code(200);
echo json_encode($data);
exit;
