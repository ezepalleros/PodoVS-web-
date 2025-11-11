<?php
// admin/admincos.php
require __DIR__ . '/adminguard.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gestionar cosméticos • PodoVS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/stylesadmin.css?v=8" rel="stylesheet">
</head>
<body class="bg-body" data-page="admin-cos">
<?php include __DIR__ . '/../componentes/headeradmin.php'; ?>

<main class="container py-4">
  <div id="alert" class="alert d-none" role="alert"></div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 mb-0">Gestión de cosméticos</h1>
      <div class="text-secondary">Ver, editar, eliminar o <strong>crear</strong> cosméticos por tipo.</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="adminpage.php">← Panel</a>
      <a class="btn btn-outline-dark" href="../index.php">Inicio</a>
    </div>
  </div>

  <?php
  function bloque($titulo, $tipoKey, $badge) {
    echo <<<HTML
      <section class="mb-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h2 class="h5 mb-0">$titulo</h2>
          <span class="badge rounded-pill text-bg-light small">$badge</span>
        </div>

        <div class="table-responsive border rounded-4 bg-white">
          <table class="table align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:72px">Img</th>
                <th>Nombre</th>
                <th>Origen</th>
                <th>Creado</th>
                <th class="text-end">Precio</th>
                <th>Asset/Link</th>
                <th>Evento</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody id="tb-$tipoKey">
              <tr><td colspan="8" class="text-center text-secondary py-4">Cargando…</td></tr>
            </tbody>
          </table>
        </div>

        <div class="text-end mt-2">
          <button class="btn btn-primary" data-action="new" data-tipo="$tipoKey">+ Nuevo $titulo</button>
        </div>
      </section>
    HTML;
  }

  bloque('Cabeza',     'cabeza',     'cabeza');
  bloque('Remera',     'remera',     'remera');
  bloque('Pantalón',   'pantalon',   'pantalon');
  bloque('Zapatillas', 'zapatillas', 'zapatillas');
  bloque('Piel',       'piel',       'piel');
  ?>
</main>

<?php include __DIR__ . '/../componentes/footeradmin.php'; ?>

<!-- Modal Crear/Editar Cosmético -->
<div class="modal fade" id="cosEditModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">
      <div class="modal-header">
        <h5 class="modal-title" id="cosModalTitle">Editar cosmético</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="cosId">

        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Nombre</label>
            <input id="cosNombre" class="form-control" required>
          </div>

          <div class="col-sm-6">
            <label class="form-label">Tipo</label>
            <select id="cosTipo" class="form-select">
              <option value="cabeza">cabeza</option>
              <option value="remera">remera</option>
              <option value="pantalon">pantalon</option>
              <option value="zapatillas">zapatillas</option>
              <option value="piel">piel</option>
            </select>
          </div>

          <div class="col-sm-6">
            <label class="form-label d-block">Origen de imagen</label>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="swOrigen">
              <label class="form-check-label" for="swOrigen">
                <span id="lblOrigen">Local (Android drawable)</span>
              </label>
            </div>
          </div>

          <!-- LOCAL: solo nombre sin .png -->
          <div id="groupLocal" class="col-12">
            <label class="form-label">Asset local (solo <strong>nombre</strong>, sin .png)</label>
            <div class="input-group">
              <span class="input-group-text">res/drawable/</span>
              <input id="cosAssetName" class="form-control" placeholder="torso_greenshirt">
              <span class="input-group-text">.png</span>
            </div>
            <div class="form-text">
              Subí el PNG a <code>app/src/main/res/drawable/</code> con ese nombre (snake_case).
            </div>
          </div>

          <!-- CLOUDINARY: URL PNG + acceso rápido -->
          <div id="groupCloud" class="col-12 d-none">
            <label class="form-label">URL Cloudinary (PNG)</label>
            <input id="cosUrl" class="form-control" placeholder="https://res.cloudinary.com/.../image/upload/.../archivo.png">
            <div class="form-text">
              Acceso rápido a tu Media Library:&nbsp;
              <a href="https://console.cloudinary.com/app/c-515f713fee005110d89bc50c716548/assets/media_library/folders/cd07c8cae48901f00987849f2f2c6a4059?view_mode=mosaic"
                 target="_blank" rel="noopener">Abrir Cloudinary</a>
            </div>
          </div>

          <div class="col-sm-6">
            <label class="form-label">Precio</label>
            <select id="cosPrecio" class="form-select">
              <option value="0">Evento (0)</option>
              <option value="20000">20.000</option>
              <option value="50000">50.000</option>
              <option value="100000">100.000</option>
            </select>
            <div class="form-text">Si es evento, queda siempre en 0.</div>
          </div>

          <div class="col-sm-6">
            <label class="form-label d-block">¿Es de evento?</label>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="swEvento">
              <label class="form-check-label" for="swEvento"><span id="lblEvento">No</span></label>
            </div>
          </div>

          <div class="col-12">
            <div class="p-2 rounded border bg-light">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <strong>Plantilla Pixil</strong> <span class="text-secondary">según el tipo</span>
                </div>
                <a id="tplLink" class="btn btn-sm btn-outline-secondary" href="#" download>Descargar plantilla</a>
              </div>
              <div class="small text-secondary mt-1">
                Abrí en <a href="https://pixilart.com/draw" target="_blank" rel="noopener">PixilArt</a> y exportá PNG x20.
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button id="btnCosCreate" class="btn btn-success d-none">Crear</button>
        <button id="btnCosSave"   class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>

<!-- Firebase compat -->
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore-compat.js"></script>
<!-- Lógica -->
<script src="../js/admin.js?v=20" type="module"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
