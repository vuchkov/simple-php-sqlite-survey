<?php

session_start();
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (($_SERVER['REQUEST_METHOD'] === 'POST') && !empty($_POST['email'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        include_once 'config.php';
        $db = new DB();

        if (($_SESSION['user_id'] = $db->getUser($email)))
            $body = 'You are successfully logged in!';
        elseif ($_SESSION['user_id'] = $db->setUser($email))
            $body = 'You are successfully authorized!';
        else unset($_SESSION['user_id']);
    }
    else {
        $body = 'Please enter a valid email address!';
    }
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

$template = '';
if (file_exists('template.html'))
    $template = file_get_contents('template.html');
$template = str_replace('{title}', 'Authorize', $template);
$template = str_replace('{content}', $body ?? '', $template);
echo $template;

