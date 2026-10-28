<?php
session_start();
require_once '../../project/db.php';
if (isset($_SESSION['user'])) {
    $stmt = $conn->prepare("UPDATE users SET remember_token=NULL WHERE id=?");
    $stmt->bind_param("i", $_SESSION['user']['id']);
    $stmt->execute();
}
setcookie('remember_token', '', time() - 3600, "/");
session_destroy();
header('Location: log.php');
exit;
?>