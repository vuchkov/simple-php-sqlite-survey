<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

include_once 'config.php';
$db = new DB();

$users = $db->users();
$body = '<h2>Users ('.(!empty($users) ? count($users) : '0').') | ';

$polls = $db->polls();
$body .= 'Polls ('.(!empty($polls) ? count($polls) : '0').') | ';

$votes = $db->votes();
$body .= 'Votes ('.(!empty($votes) ? count($votes) : '0').')</h2>';

$body .= '<h3>+ Create a Poll</h3>';
if (!empty($polls))
    foreach ($polls as $poll) {
        $body .= '<br>'.$poll['id'].' | <a href="vote-poll.php?id='.$poll['id'].'">'.$poll['question'].'</a><br>';
    }

$page = new Page('Survey', $body);
$page->render();
