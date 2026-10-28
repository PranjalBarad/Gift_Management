<?php
// Simple admin authentication guard
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// If already logged in as admin, allow
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
  // Not logged in, redirect to login page
  header('Location: login.php');
  exit;
}

?>