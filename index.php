<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

// Get a count of the number of users
/*$userCount = $db->querySingle('SELECT COUNT(DISTINCT "id") FROM "users"');
echo("User count: $userCount\n");// Close the connection
$db->close();*/

include_once 'config.php';

$db = new DB();

$users = $db->users();
$body = '<h2>Users ('.(!empty($users) ? count($users) : '0').') | ';

$polls = $db->polls();
$body .= 'Polls ('.(!empty($polls) ? count($polls) : '0').') | ';

$votes = $db->votes();
$body .= 'Votes ('.(!empty($votes) ? count($votes) : '0').')</h2>';

$page = new Page('Survey', $body ?? '');
$page->render();
