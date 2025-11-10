<?php require __DIR__ . '/adminguard.php'; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel Admin • PodoVS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/styles.css?v=6" rel="stylesheet">
</head>
<body class="bg-body">
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Panel administrativo</h1>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="../index.php">← Volver al inicio</a>
        <button id="btnLogout" class="btn btn-outline-danger">Cerrar sesión</button>
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
        <a href="admineve.php" class="text-decoration-none">
          <div class="card shadow-sm h-100 rounded-4">
            <div class="card-body">
              <h2 class="h5 mb-1">Gestionar eventos</h2>
              <p class="text-secondary mb-0">Mensuales, cooperativos y 1v1.</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <script>
  // Cerrar sesión del guard del servidor
  document.getElementById('btnLogout')?.addEventListener('click', async ()=>{
    try { await fetch('../admin_gate.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({unset:true})}); }
    catch(e){}
    // Volver al inicio
    window.location.href = '../index.php';
  });
  </script>
</body>
</html>
