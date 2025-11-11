<?php
// /admin_gate.php — SET/UNSET sesión vía GET y REDIRECCIÓN
declare(strict_types=1);

// Mismos parámetros de cookie
$cookieParams = [
  'lifetime' => 0,
  'path'     => '/',
  'domain'   => '',
  'secure'   => false,
  'httponly' => true,
  'samesite' => 'Lax',
];
if (PHP_VERSION_ID >= 70300) {
  session_set_cookie_params($cookieParams);
} else {
  session_set_cookie_params(
    $cookieParams['lifetime'],
    $cookieParams['path'].'; samesite='.$cookieParams['samesite'],
    $cookieParams['domain'],
    $cookieParams['secure'],
    $cookieParams['httponly']
  );
}

session_start();

if (isset($_GET['set'])) {
  // Marca sesión de admin y redirige al panel
  session_regenerate_id(true);
  $_SESSION['admin_ok'] = true;
  $_SESSION['admin_ok_time'] = time();
  session_write_close();
  header('Location: admin/adminpage.php');
  exit;
}

if (isset($_GET['unset'])) {
  // Cierra sesión y redirige al inicio
  unset($_SESSION['admin_ok'], $_SESSION['admin_ok_time']);
  session_write_close();
  header('Location: index.php');
  exit;
}

// fallback
header('Location: index.php');
exit;
