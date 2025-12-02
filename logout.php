<?php
session_start();

// Xóa toàn bộ session
session_unset();
session_destroy();
// xóa cookie
setcookie('logged_user','', time()-3600, '/');
// xóa cookie session trên client
setcookie(session_name(), '', time()-3600, '/');
// Chuyển về trang login
header("Location: login.php");
exit();
?>
