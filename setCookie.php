<?php
// require_once './login.php';
//setCookie.php
//TODO: Fix the security concern here
if (isset($_POST['email'])) {
    $user = $_POST['logged_user'];
    setcookie("logged_user", $user, time() + 60 * 60 * 24 * 30);
    echo 'Set cookie successfully<br/>';
    echo 'Click <a href="index.php">here</a> to go back';
}
?>
