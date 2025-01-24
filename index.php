<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

include_once 'config.php';
$db = new DB();

$users = $db->users();
$body = '<br><h3>Users ('.(!empty($users) ? count($users) : '0').')';
$polls = $db->polls();
$body .= ' | Polls ('.(!empty($polls) ? count($polls) : '0').')';
//$votes = $db->votes();
//$body .= ' | Votes ('.(!empty($votes) ? count($votes) : '0').')';
$body .= '</h3>';

$body .= '<br><a href="/add.php" class="btn btn-primary">+ Create a Poll</a><br>';
if (!empty($polls))
    foreach ($polls as $poll) {
        $poll_votes = $db->getVotes($poll['id']) ?: 0;
        $body .= '<br><a href="poll.php?id='.$poll['id'].'">'.$poll['question'].'</a> ('.$poll_votes.')<br>';
    }

$page = new Page('Survey', $body);
$page->render();
