<?php
// admin/adminpage.php — DASHBOARD MINIMAL
require __DIR__ . '/adminguard.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PodoVS • Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/stylesadmin.css?v=10" rel="stylesheet">
</head>
<body class="bg-body">
  <?php include __DIR__ . '/../componentes/headeradmin.php'; ?>

  <main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Panel administrativo</h1>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="../index.php">← Inicio</a>
        <a id="btnLogout" class="btn btn-outline-danger" href="../admin_gate.php?unset=1">Cerrar sesión</a>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-4">
        <a href="adminusu.php" class="text-decoration-none">
          <div class="card shadow-sm h-100 rounded-4">
            <div class="card-body">
              <h2 class="h5 mb-1">Gestionar usuarios</h2>
              <p class="text-secondary mb-0">Altas, bajas, roles y bloqueos.</p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-4">
        <a href="admincos.php" class="text-decoration-none">
          <div class="card shadow-sm h-100 rounded-4">
            <div class="card-body">
              <h2 class="h5 mb-1">Gestionar cosméticos</h2>
              <p class="text-secondary mb-0">Skins, rarezas, precios y stock.</p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-4">
        <a href="adminroo.php" class="text-decoration-none">
          <div class="card shadow-sm h-100 rounded-4">
            <div class="card-body">
              <h2 class="h5 mb-1">Gestionar salas</h2>
              <p class="text-secondary mb-0">Eventos, rankings y versus.</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/../componentes/footeradmin.php'; ?>

  <!-- Firebase compat (solo para logout de Auth si querés) -->
  <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-auth-compat.js"></script>

  <script src="../js/admin.js?v=10" type="module"></script>
</body>
</html>
