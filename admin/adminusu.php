<?php
// admin/adminusu.php
require __DIR__ . '/adminguard.php'; // protege por sesión PHP del panel
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestionar usuarios • PodoVS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/stylesadmin.css?v=1" rel="stylesheet">
</head>
<body class="bg-body" data-page="admin-usuarios">
  <?php include __DIR__ . '/../componentes/headeradmin.php'; ?>

  <main class="container py-4">
    <div id="alert" class="alert d-none" role="alert"></div>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-0">Gestión de usuarios</h1>
        <div class="text-secondary">Altas, bajas, roles y métricas</div>
      </div>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="adminpage.php">← Panel</a>
        <a class="btn btn-outline-dark" href="../index.php">Inicio</a>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-4">
        <div class="card shadow-sm rounded-4">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <div class="text-secondary small">Usuarios registrados</div>
                <div id="totalUsers" class="h3 mb-0">—</div>
              </div>
              <span class="badge badge-muted align-self-start">users</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm rounded-4">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <div class="text-secondary small">KM totales acumulados</div>
                <div class="h3 mb-0"><span id="totalKm">—</span> <small class="text-secondary">km</small></div>
              </div>
              <span class="badge badge-muted align-self-start">stats</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm rounded-4">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <div class="text-secondary small">VS ganados (suma)</div>
                <div id="totalVs" class="h3 mb-0">—</div>
              </div>
              <span class="badge badge-muted align-self-start">stats</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm rounded-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="h5 mb-0">Usuarios</h2>
          <input id="search" class="form-control" placeholder="Buscar por nombre o email" style="max-width:320px">
        </div>

        <div id="loading" class="text-center py-5">
          <div class="spinner-border" role="status"></div>
          <div class="text-secondary mt-2">Cargando usuarios…</div>
        </div>

        <div class="table-responsive d-none" id="tableWrap">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Usuario</th>
                <th>Email</th>
                <th>Kilómetros</th>
                <th>VS ganados</th>
                <th>Nivel</th>
                <th>Misiones</th>
                <th>Rol</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody id="usersTable"></tbody>
          </table>
          <div id="empty" class="text-center text-secondary py-4 d-none">No se encontraron usuarios.</div>
        </div>
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/../componentes/footeradmin.php'; ?>

  <!-- Firebase compat -->
  <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-auth-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore-compat.js"></script>

  <!-- Lógica del panel admin -->
  <script src="../js/admin.js?v=25" type="module"></script>
</body>
</html>
