<?php
// admin.php — LOGIN MINIMAL
session_start();
if (!empty($_SESSION['admin_ok'])) {
  header('Location: admin/adminpage.php');
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PodoVS • Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/stylesadmin.css?v=10" rel="stylesheet">
</head>
<body class="bg-body page-login">
  <?php include __DIR__ . '/componentes/headeradmin.php'; ?>

  <main class="container">
    <div class="card card-admin shadow-sm rounded-4">
      <div class="card-body p-4">
        <div class="text-center mb-3">
          <img src="img/icon_podovs.png" width="64" height="64" class="rounded-circle shadow-sm" alt="">
          <h1 class="h4 mt-2 mb-0 fw-bold">Panel administrativo</h1>
          <div class="text-secondary small">Inicio de sesión</div>
        </div>

        <div id="alert" class="alert d-none" role="alert"></div>

        <form id="adminForm" novalidate>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input id="email" type="email" class="form-control form-control-lg" required>
            <div class="invalid-feedback">Ingresá un email válido.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input id="password" type="password" class="form-control form-control-lg" required>
            <div class="invalid-feedback">Ingresá tu contraseña.</div>
          </div>
          <div class="d-grid gap-2">
            <button id="btnLogin" class="btn btn-primary py-2" type="submit">Ingresar</button>
            <a class="btn btn-outline-secondary py-2" href="index.php">← Volver al inicio</a>
          </div>
          <div class="form-text mt-2">Se validará que tu usuario tenga permisos de administrador.</div>
        </form>
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/componentes/footeradmin.php'; ?>

  <!-- Firebase compat -->
  <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-auth-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore-compat.js"></script>

  <!-- Lógica común Admin -->
  <script src="js/admin.js?v=10" type="module"></script>
</body>
</html>
