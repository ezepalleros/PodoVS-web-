<?php
session_start();
header('Content-Type: application/json');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'err' => 'method']);
  exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: [];

if (!empty($data['set'])) {
  // Validar nonce emitido en admin.php
  $nonce = $data['nonce'] ?? '';
  if (!$nonce || empty($_SESSION['admin_login_nonce']) || !hash_equals($_SESSION['admin_login_nonce'], $nonce)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'err' => 'nonce']);
    exit;
  }
  $_SESSION['admin_ok'] = true;
  $_SESSION['admin_ok_time'] = time();
  echo json_encode(['ok' => true]);
  exit;
}

if (!empty($data['unset'])) {
  unset($_SESSION['admin_ok'], $_SESSION['admin_ok_time'], $_SESSION['admin_login_nonce']);
  echo json_encode(['ok' => true]);
  exit;
}

http_response_code(400);
echo json_encode(['ok' => false]);
