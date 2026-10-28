<?php
session_start();
// Unset admin flag and destroy session
unset($_SESSION['is_admin']);
session_destroy();
header('Location: login.php');
exit;
?>