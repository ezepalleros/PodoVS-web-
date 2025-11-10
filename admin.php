<?php
// Emite un nonce de sesión para el login admin
session_start();
if (empty($_SESSION['admin_login_nonce'])) {
  $_SESSION['admin_login_nonce'] = bin2hex(random_bytes(16));
}
$ADMIN_NONCE = $_SESSION['admin_login_nonce'];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin • PodoVS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/styles.css?v=6" rel="stylesheet">
  <style>
    body { background: linear-gradient(135deg,#ecfeff,#f5f3ff); min-height:100vh; }
    .card-admin { max-width: 460px; margin: 6rem auto; }
  </style>
</head>
<body>
  <div class="container">
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
            <label class="form-label fw-semibold">Email</label>
            <input id="email" type="email" class="form-control form-control-lg" placeholder="admin@ejemplo.com" required>
            <div class="invalid-feedback">Ingresá un email válido.</div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Contraseña</label>
            <input id="password" type="password" class="form-control form-control-lg" placeholder="••••••••" required>
            <div class="invalid-feedback">Ingresá tu contraseña.</div>
          </div>
          <div class="d-grid gap-2">
            <button id="btnLogin" class="btn btn-primary py-2" type="submit">Ingresar</button>
            <a class="btn btn-outline-secondary py-2" href="index.php">← Volver al inicio</a>
          </div>
          <div class="form-text mt-2">Se validará que tu usuario tenga <code>usu_admin = true</code>.</div>
        </form>
      </div>
    </div>
  </div>

  <!-- Firebase (compat) -->
  <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-auth-compat.js"></script>
  <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore-compat.js"></script>

  <script>
  const firebaseConfig = {
    apiKey: "AIzaSyAH-grTjHt4KxqAwuf6lY_Z5Eh_y0FNYR0",
    authDomain: "podovs-ba062.firebaseapp.com",
    projectId: "podovs-ba062",
    storageBucket: "podovs-ba062.appspot.com",
  };
  firebase.initializeApp(firebaseConfig);
  const auth = firebase.auth();
  const db   = firebase.firestore();

  const form   = document.getElementById('adminForm');
  const emailI = document.getElementById('email');
  const passI  = document.getElementById('password');
  const alertB = document.getElementById('alert');
  const btn    = document.getElementById('btnLogin');

  // nonce emitido desde PHP
  const ADMIN_NONCE = <?php echo json_encode($ADMIN_NONCE); ?>;

  function showAlert(type, msg){
    alertB.className = 'alert alert-' + type;
    alertB.textContent = msg;
    alertB.classList.remove('d-none');
  }
  function clearAlert(){
    alertB.className = 'alert d-none';
    alertB.textContent = '';
  }

  form.addEventListener('submit', async (e)=>{
    e.preventDefault();
    clearAlert();
    if(!emailI.checkValidity() || !passI.checkValidity()){
      form.classList.add('was-validated');
      return;
    }
    btn.disabled = true; btn.textContent = 'Verificando...';

    try{
      const { user } = await auth.signInWithEmailAndPassword(emailI.value.trim(), passI.value);
      const uid = user.uid;

      const snap = await db.collection('users').doc(uid).get();
      if(!snap.exists){
        await auth.signOut();
        showAlert('danger', 'La cuenta no existe en la colección users.');
        btn.disabled = false; btn.textContent = 'Ingresar';
        return;
      }
      const data = snap.data() || {};
      const isAdmin = (data.usu_admin === true) || (data.usu_rol === true) || (data.usu_rol === 'admin');
      if(!isAdmin){
        await auth.signOut();
        showAlert('warning', 'Tu usuario no tiene permisos de administrador.');
        btn.disabled = false; btn.textContent = 'Ingresar';
        return;
      }

      // Enviamos el nonce al servidor para setear la sesión
      const res = await fetch('admin_gate.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ set: true, nonce: ADMIN_NONCE })
      });
      if(!res.ok){
        showAlert('danger', 'No se pudo establecer la sesión de administrador.');
        btn.disabled = false; btn.textContent = 'Ingresar';
        return;
      }
      window.location.href = 'admin/adminpage.php';
    }catch(err){
      console.error(err);
      let msg = 'No pudimos iniciar sesión.';
      if (err.code === 'auth/invalid-credential' || err.code === 'auth/wrong-password') msg = 'Credenciales inválidas.';
      if (err.code === 'auth/user-not-found') msg = 'El usuario no existe.';
      showAlert('danger', msg);
      btn.disabled = false; btn.textContent = 'Ingresar';
    }
  });
  </script>
</body>
</html>
