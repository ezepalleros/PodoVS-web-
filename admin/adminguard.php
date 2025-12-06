<?php

declare(strict_types=1);

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
    $cookieParams['path'] . '; samesite=' . $cookieParams['samesite'],
    $cookieParams['domain'],
    $cookieParams['secure'],
    $cookieParams['httponly']
  );
}

session_start();

$ok = !empty($_SESSION['admin_ok']);

if ($ok && isset($_SESSION['admin_ok_time']) && (time() - $_SESSION['admin_ok_time'] > 1800)) {
  $ok = false;
  unset($_SESSION['admin_ok'], $_SESSION['admin_ok_time']);
}

if ($ok) {
  $_SESSION['admin_ok_time'] = time();
  return;
}

header('Location: ../admin.php');
exit;
