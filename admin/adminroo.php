<?php
// admin/adminroo.php
require __DIR__ . '/adminguard.php'; // protege por sesión PHP del panel
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Salas y eventos • PodoVS Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/stylesadmin.css?v=12" rel="stylesheet">
  <style>
    .table thead th{ white-space:nowrap; }
    .badge-mono{ background:#111827; color:#fff; }
    .mono-cell{
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
  </style>
</head>
<body class="bg-body" data-page="admin-rooms">
<?php include __DIR__ . '/../componentes/headeradmin.php'; ?>

<main class="container py-4">

  <div id="alert" class="alert d-none" role="alert"></div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 mb-0">Salas, versus, rankings y eventos</h1>
      <div class="text-secondary small">Visor general para moderación rápida.</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="adminpage.php">← Panel</a>
      <a class="btn btn-outline-dark" href="../index.php">Inicio</a>
    </div>
  </div>

  <!-- VERSUS -->
  <section class="mb-4">
    <div class="card shadow-sm rounded-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h2 class="h5 mb-0">Versus (colección <code>versus</code>)</h2>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Creado</th>
              <th>Jugadores</th>
              <th>Modalidad</th>
              <th>Target</th>
              <th>Pasos por jugador</th>
              <th class="text-end"></th>
            </tr>
            </thead>
            <tbody id="tblVersus">
            <tr><td colspan="7" class="text-secondary">Cargando…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- ROOMS -->
  <section class="mb-4">
    <div class="card shadow-sm rounded-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h2 class="h5 mb-0">Salas (colección <code>rooms</code>)</h2>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Creada</th>
              <th>Código</th>
              <th>Creador</th>
              <th>Modalidad</th>
              <th>Target</th>
              <th>Pública</th>
              <th class="text-end"></th>
            </tr>
            </thead>
            <tbody id="tblRooms">
            <tr><td colspan="8" class="text-secondary">Cargando…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- RANKINGS -->
  <section class="mb-4">
    <div class="card shadow-sm rounded-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h2 class="h5 mb-0">Rankings (colección <code>rankings</code>)</h2>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Creado</th>
              <th>Week key</th>
              <th>Jugadores</th>
              <th class="text-end"></th>
            </tr>
            </thead>
            <tbody id="tblRankings">
            <tr><td colspan="5" class="text-secondary">Cargando…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- EVENTS ABAJO DE TODO -->
  <section>
    <div class="card shadow-sm rounded-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h2 class="h5 mb-0">Eventos (colección <code>events</code>)</h2>
          <span id="evtActiveInfo" class="badge text-bg-secondary">0 activos</span>
        </div>

        <!-- Botón + collapse para nuevo evento -->
        <div class="mb-3">
          <button class="btn btn-sm btn-success" type="button"
                  data-bs-toggle="collapse" data-bs-target="#evFormWrap"
                  aria-expanded="false" aria-controls="evFormWrap">
            + Nuevo evento
          </button>
        </div>

        <div class="collapse mb-3" id="evFormWrap">
          <form id="evForm" class="row g-2 align-items-end border rounded-4 p-3 bg-light">
            <div class="col-md-3">
              <label for="evStart" class="form-label">Inicio</label>
              <input type="datetime-local" id="evStart" class="form-control">
            </div>
            <div class="col-md-3">
              <label for="evEnd" class="form-label">Fin</label>
              <input type="datetime-local" id="evEnd" class="form-control">
            </div>
            <div class="col-md-2">
              <label for="evTarget" class="form-label">Meta pasos</label>
              <input type="number" min="0" step="1000" id="evTarget" class="form-control" value="100000">
            </div>
            <div class="col-md-2">
              <label for="evReward" class="form-label">Recompensa</label>
              <input type="number" min="0" step="1000" id="evReward" class="form-control" value="50000">
            </div>
            <div class="col-md-2">
              <label class="form-label d-block">Activo</label>
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="evActive" checked>
                <label class="form-check-label" for="evActive">Sí</label>
              </div>
            </div>
            <div class="col-12">
              <label for="evBoss" class="form-label">URL monstruo (Cloudinary PNG)</label>
              <input type="url" id="evBoss" class="form-control" placeholder="https://res.cloudinary.com/.../boss.png">
                <a href="https://console.cloudinary.com/app/c-515f713fee005110d89bc50c716548/assets/media_library/folders/cd07c8cae48901f00987849f2f2c6a4059?view_mode=mosaic"
                 target="_blank" rel="noopener">Abrir Cloudinary</a>
            </div>
            <div class="col-12 text-end">
              <button type="submit" class="btn btn-success btn-sm" id="btnEvCreate">
                Guardar evento
              </button>
            </div>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Activo</th>
              <th>Inicio</th>
              <th>Fin</th>
              <th>Meta pasos</th>
              <th>Recompensa</th>
              <th>Monstruo</th>
              <th class="text-end"></th>
            </tr>
            </thead>
            <tbody id="tblEvents">
            <tr><td colspan="8" class="text-secondary">Cargando…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

</main>

<?php include __DIR__ . '/../componentes/footeradmin.php'; ?>

<!-- Firebase compat -->
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore-compat.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Lógica del panel admin -->
<script src="../js/admin.js?v=23" type="module"></script>
</body>
</html>
