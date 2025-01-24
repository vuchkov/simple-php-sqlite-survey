<?php

session_start();
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

include_once 'config.php';
$db = new DB();

if (($_SERVER['REQUEST_METHOD'] === 'POST') && !empty($_POST['email'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (($_SESSION['user_id'] = $db->getUser($email)))
            $body = 'You are successfully logged in! <a href="/">Go to Homepage</a>';
        elseif ($_SESSION['user_id'] = $db->setUser($email))
            $body = 'You are successfully authorized! <a href="/">Go to Homepage</a>';
        else {
            $body = 'The user is not found! <a href="/auth.php">Try again</a>';
            unset($_SESSION['user_id']);
        }
    }
    else
        $body = 'Please enter a valid email address! <a href="/auth.php">Try again</a>';
}
else {
    $body = <<<EOT
        <form action="auth.php" method="post">
            <label for="email">Email:</label><br>
            <input type="email" name="email" placeholder="Enter your email" required><br>
            <button type="submit">Authorize</button><br><br>
        </form>
EOT;
}

$page = new Page('Authorize', $body);
$page->render();

