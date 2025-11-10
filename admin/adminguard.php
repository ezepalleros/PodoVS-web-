<?php
// Guard de páginas admin
session_start();

$ok = isset($_SESSION['admin_ok']) && $_SESSION['admin_ok'] === true;

// (opcional) expira a los 30 min
if ($ok && isset($_SESSION['admin_ok_time']) && (time() - $_SESSION['admin_ok_time'] > 1800)) {
  $ok = false;
  unset($_SESSION['admin_ok'], $_SESSION['admin_ok_time'], $_SESSION['admin_login_nonce']);
}

if (!$ok) {
  header('Location: ../index.php');
  exit;
}
