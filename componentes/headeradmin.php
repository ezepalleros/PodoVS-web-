<?php
$inAdmin = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false);
$P = $inAdmin ? '..' : '.';
?>
<header class="border-bottom bg-white">
  <nav class="container d-flex align-items-center justify-content-between py-2">
    <div class="d-flex align-items-center gap-2">
      <img src="<?php echo $P; ?>/img/icon_podovs.png" width="32" height="32" alt="PodoVS" class="rounded-circle">
      <strong>PodoVS • Admin</strong>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-sm btn-outline-secondary" href="<?php echo $P; ?>/admin/adminpage.php">Panel</a>
      <a class="btn btn-sm btn-outline-dark" href="<?php echo $P; ?>/index.php">Inicio</a>
    </div>
  </nav>
</header>