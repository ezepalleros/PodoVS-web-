// js/admin.js — v22 (unificado)

// ===== Firebase =====
const firebaseConfig = {
  apiKey: "AIzaSyAH-grTjHt4KxqAwuf6lY_Z5Eh_y0FNYR0",
  authDomain: "podovs-ba062.firebaseapp.com",
  projectId: "podovs-ba062",
  storageBucket: "podovs-ba062.appspot.com",
};
if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);
const auth = firebase.auth();
const db   = firebase.firestore();

// ===== Helpers UI =====
const $  = (s)=>document.querySelector(s);
const alertB = $('#alert');
const showAlert = (t,m)=>{
  if(!alertB) return;
  alertB.className='alert alert-'+t;
  alertB.textContent=m;
  alertB.classList.remove('d-none');
};
const clearAlert=()=>{
  if(alertB){
    alertB.className='alert d-none';
    alertB.textContent='';
  }
};
const escapeHtml = (s)=> String(s||'').replace(/[&<>"']/g, m=>({
  '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
}[m]));
const waitForAuthReady = ()=> new Promise(res=>{
  let done=false;
  const u=auth.onAuthStateChanged(x=>{
    if(!done){done=true; u(); res(x);}
  });
  setTimeout(()=>{
    if(!done){
      done=true;
      try{u();}catch{}
      res(auth.currentUser||null);
    }
  },3000);
});
const tsToDate = (ts)=>{
  if(!ts) return '—';
  try{
    const d = ts.toDate ? ts.toDate() :
      (typeof ts.seconds==='number'? new Date(ts.seconds*1000) : new Date(ts));
    return d.toLocaleDateString();
  }catch{ return '—'; }
};

// ================== LOGIN ==================
const form = $('#adminForm');
if (form){
  const emailI = $('#email'), passI = $('#password'), btn = $('#btnLogin');
  form.addEventListener('submit', async (e)=>{
    e.preventDefault(); clearAlert();
    if(!emailI.checkValidity() || !passI.checkValidity()){
      form.classList.add('was-validated');
      return;
    }
    btn.disabled=true; btn.textContent='Verificando...';
    try{
      const { user } = await auth.signInWithEmailAndPassword(
        emailI.value.trim(),
        passI.value
      );
      let isAdmin=false;
      try{
        const a=await db.collection('admins').doc(user.uid).get();
        isAdmin=a.exists;
      }catch{}

      if(!isAdmin){
        try{
          const u=await db.collection('users').doc(user.uid).get();
          const d=u.data()||{};
          isAdmin = d.usu_admin===true || d.usu_rol===true || d.usu_rol==='admin';
        }catch{}
      }

      if(!isAdmin){
        await auth.signOut();
        showAlert('warning','Tu usuario no tiene permisos de administrador.');
        btn.disabled=false; btn.textContent='Ingresar';
        return;
      }

      window.location.href='admin_gate.php?set=1';
    }catch(err){
      console.error(err);
      let msg='No pudimos iniciar sesión.';
      if(err.code==='auth/invalid-credential'||err.code==='auth/wrong-password')
        msg='Credenciales inválidas.';
      if(err.code==='auth/user-not-found')
        msg='El usuario no existe.';
      showAlert('danger',msg);
      btn.disabled=false; btn.textContent='Ingresar';
    }
  });
}

// ================== USUARIOS ==================
if (document.getElementById('usersTable')) {
  const loading   = $('#loading');
  const tableWrap = $('#tableWrap');
  const emptyMsg  = $('#empty');
  const tbody     = $('#usersTable');

  (async ()=>{
    clearAlert();
    loading?.classList.remove('d-none');
    tableWrap?.classList.add('d-none');
    emptyMsg?.classList.add('d-none');

    try{
      const user = await waitForAuthReady();
      if (!user) {
        showAlert('warning','No hay sesión de Firebase activa. Volvé a iniciar sesión.');
        setTimeout(()=>{ window.location.href = '../admin.php'; }, 900);
        return;
      }

      const snap = await db.collection('users').get();
      let totalKm = 0, totalVs = 0;
      const rows = [];

      async function getStats(uid, uDoc){
        if (uDoc && uDoc.usu_stats) return uDoc.usu_stats;
        try {
          const s = await db.collection('usu_stats').doc(uid).get();
          if (s.exists) return s.data()||{};
        } catch(_){}
        try {
          const s2 = await db.collection('users')
            .doc(uid).collection('stats').doc('current').get();
          if (s2.exists) return s2.data()||{};
        } catch(_){}
        return {};
      }

      for (const d of snap.docs) {
        const u = { id: d.id, ...(d.data()||{}) };
        const st = await getStats(d.id, u);
        const km  = Number(st.km_total || 0);
        const vs  = Number(st.carreras_ganadas || st.vs_ganados || 0);
        const mis = Number(st.metas_diarias_total||0) + Number(st.metas_semana_total||0);
        totalKm += km; totalVs += vs;

        const isAdmin = (u.usu_admin===true || u.usu_rol===true || u.usu_rol==='admin');
        const isSusp  = (u.usu_suspendido===true || u.usu_estado==='suspendido');

        rows.push({
          id: d.id,
          nombre: (u.usu_nombre || '—'),
          email:  (u.usu_email  || '—'),
          km, vs, misiones: mis,
          nivel: (u.usu_nivel ?? st.nivel ?? 0),
          rol:   isAdmin ? 'Admin' : 'Usuario',
          estado: isSusp ? 'Suspendido' : 'Activo',
          isAdmin, isSusp
        });
      }

      rows.sort((a,b)=>a.nombre.localeCompare(b.nombre));
      tbody.innerHTML = rows.map(r => {
        const rolBadge = r.rol === 'Admin' ? 'text-bg-primary' : 'text-bg-secondary';
        const estBadge = r.estado === 'Suspendido' ? 'text-bg-warning' : 'text-bg-success';
        const suspendBtn = r.isSusp
          ? `<button class="btn btn-sm btn-outline-success" data-action="unsuspend" data-uid="${r.id}" data-name="${escapeHtml(r.nombre)}">Reactivar</button>`
          : `<button class="btn btn-sm btn-outline-warning" data-action="suspend" data-uid="${r.id}" data-name="${escapeHtml(r.nombre)}">Suspender</button>`;
        return `
          <tr>
            <td>${escapeHtml(r.nombre)}</td>
            <td class="text-secondary small">${escapeHtml(r.email)}</td>
            <td>${r.km.toFixed(2)}</td>
            <td>${r.vs}</td>
            <td>${r.nivel}</td>
            <td>${r.misiones}</td>
            <td><span class="badge ${rolBadge}">${r.rol}</span></td>
            <td><span class="badge ${estBadge}">${r.estado}</span></td>
            <td class="text-end">
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary"   data-action="admin"  data-uid="${r.id}" data-name="${escapeHtml(r.nombre)}">Admin</button>
                <button class="btn btn-sm btn-outline-secondary" data-action="user"   data-uid="${r.id}" data-name="${escapeHtml(r.nombre)}">Usuario</button>
                ${suspendBtn}
                <button class="btn btn-sm btn-outline-danger"    data-action="del"    data-uid="${r.id}" data-name="${escapeHtml(r.nombre)}">Eliminar</button>
              </div>
            </td>
          </tr>`;
      }).join('');

      $('#totalUsers').textContent = rows.length.toString();
      $('#totalKm').textContent    = totalKm.toFixed(2);
      $('#totalVs').textContent    = totalVs.toString();

      tableWrap?.classList.remove('d-none');
      emptyMsg?.classList.toggle('d-none', rows.length>0);

      tbody.addEventListener('click', async (e)=>{
        const btn = e.target.closest('button[data-action]');
        if(!btn) return;
        const { action, uid } = btn.dataset;
        const name = btn.dataset.name || '';

        try{
          if (action === 'admin') {
            await db.collection('users').doc(uid).set({
              usu_admin:true, usu_rol:'admin',
              usu_suspendido:false, usu_estado:'activo'
            }, { merge:true });
            showAlert('success', `“${name}” ahora es administrador.`);
          } else if (action === 'user') {
            await db.collection('users').doc(uid).set({
              usu_admin:false, usu_rol:'user'
            }, { merge:true });
            showAlert('success', `“${name}” volvió a rol usuario.`);
          } else if (action === 'suspend') {
            if (!confirm(`¿Suspender a “${name}”?`)) return;
            await db.collection('users').doc(uid).set({
              usu_suspendido:true,  usu_estado:'suspendido'
            }, { merge:true });
            showAlert('success', `Cuenta de “${name}” suspendida.`);
          } else if (action === 'unsuspend') {
            await db.collection('users').doc(uid).set({
              usu_suspendido:false, usu_estado:'activo'
            }, { merge:true });
            showAlert('success', `Cuenta de “${name}” reactivada.`);
          } else if (action === 'del') {
            if (!confirm(`¿Eliminar definitivamente a “${name}”?`)) return;
            await db.collection('users').doc(uid).delete();
            showAlert('success','Usuario eliminado.');
            btn.closest('tr')?.remove();
          }
          setTimeout(()=>location.reload(), 400);
        }catch(err){
          console.error(err);
          showAlert('danger','No se pudo completar la acción.');
        }
      });

    }catch(err){
      console.error(err);
      const msg = (err && err.code === 'permission-denied')
        ? 'Permiso denegado por reglas de Firestore.'
        : 'No se pudieron cargar los usuarios.';
      showAlert('danger', msg);
    }finally{
      loading?.classList.add('d-none');
    }
  })();
}

// ================== COSMÉTICOS ==================
if (document.body.dataset.page === 'admin-cos') {
  (async ()=>{
    clearAlert();
    const user = await waitForAuthReady();
    if(!user){
      showAlert('warning','No hay sesión de Firebase. Iniciá sesión.');
      setTimeout(()=>location.href='../admin.php',900);
      return;
    }

    let snap;
    try { snap = await db.collection('cosmetics').get(); }
    catch (err) {
      console.error(err);
      showAlert('danger','No se pudieron cargar los cosméticos.');
      return;
    }

    const groups = { cabeza:[], remera:[], pantalon:[], zapatillas:[], piel:[] };

    const previewUrl = (origen, asset) => {
      if (!asset) return '';
      if (origen === 'cloudinary' || /^https?:\/\//i.test(asset)) return asset;
      return `/img/cosmetics/${asset}.png`;
    };

    snap.forEach(doc=>{
      const d = doc.data()||{};
      const tipo   = String(d.cos_tipo||'').toLowerCase();
      if (!groups[tipo]) return;

      const origen = (d.cos_assetType||d.cos_storage||'').toLowerCase() ||
                     (String(d.cos_asset||'').startsWith('http') ? 'cloudinary':'local');

      const asset  = d.cos_asset || '';
      const img    = previewUrl(origen, asset);
      const creado = d.cos_createdAt || d.cos_fecha || d.createdAt || null;
      const precio = Number(d.cos_precio || 0);
      const evento = d.cos_evento === true;

      groups[tipo].push({
        id: doc.id,
        nombre: d.cos_nombre || '—',
        origen, img, creado, precio, evento, asset
      });
    });

    Object.keys(groups).forEach(k => groups[k].sort((a,b)=>a.nombre.localeCompare(b.nombre)));

    const render = (tipoKey)=>{
      const tbody = document.getElementById('tb-'+tipoKey);
      if(!tbody) return;
      const arr = groups[tipoKey];
      if(!arr.length){
        tbody.innerHTML = `<tr><td colspan="8" class="text-center text-secondary">Sin datos</td></tr>`;
        return;
      }
      tbody.innerHTML = arr.map(r=>{
        const linkHtml = (r.origen==='cloudinary' && r.asset)
          ? `<a href="${escapeHtml(r.asset)}" target="_blank" rel="noopener">Abrir</a>`
          : '—';
        return `
          <tr data-id="${r.id}" data-tipo="${tipoKey}" data-origen="${r.origen}" data-asset="${escapeHtml(r.asset)}">
            <td>${r.img?`<img src="${escapeHtml(r.img)}" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:8px">`:'—'}</td>
            <td>${escapeHtml(r.nombre)}</td>
            <td><span class="badge ${r.origen==='cloudinary'?'text-bg-info':'text-bg-secondary'}">${r.origen}</span></td>
            <td>${tsToDate(r.creado)}</td>
            <td class="text-end">$ ${r.precio.toFixed(0)}</td>
            <td class="text-truncate" style="max-width:280px">${linkHtml}</td>
            <td>${r.evento?'<span class="badge text-bg-warning">Sí</span>':'No'}</td>
            <td class="text-end">
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary" data-action="edit">Editar</button>
                <button class="btn btn-sm btn-outline-danger"  data-action="del">Eliminar</button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    };

    ['cabeza','remera','pantalon','zapatillas','piel'].forEach(render);

    // ----- Modal refs -----
    const modalEl = document.getElementById('cosEditModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

    const elId      = $('#cosId');
    const elNombre  = $('#cosNombre');
    const elTipo    = $('#cosTipo');
    const swOrigen  = $('#swOrigen');
    const lblOrigen = $('#lblOrigen');
    const groupLocal= $('#groupLocal');
    const groupCloud= $('#groupCloud');
    const elAsset   = $('#cosAssetName');
    const elUrl     = $('#cosUrl');
    const elPrecio  = $('#cosPrecio');
    const swEvento  = $('#swEvento');
    const lblEvento = $('#lblEvento');
    const swTienda  = $('#swTienda');
    const lblTienda = $('#lblTienda');
    const tplLink   = $('#tplLink');
    const btnSave   = $('#btnCosSave');
    const btnCreate = $('#btnCosCreate');
    const title     = $('#cosModalTitle');

    const templates = {
      cabeza:     '../template/cabeza_template.pixil',
      remera:     '../template/remera_template.pixil',
      pantalon:   '../template/pantalon_template.pixil',
      zapatillas: '../template/zapatillas_template.pixil',
      piel:       '../template/piel_template.pixil',
    };
    const updateTpl = ()=>{ if(tplLink) tplLink.href = templates[elTipo.value] || '#'; };

    const setOrigenUI = (isCloud)=>{
      if (!groupLocal || !groupCloud || !lblOrigen) return;
      if (isCloud) {
        lblOrigen.textContent = 'Cloudinary (URL PNG)';
        groupCloud.classList.remove('d-none');
        groupLocal.classList.add('d-none');
      } else {
        lblOrigen.textContent = 'Local (Android drawable)';
        groupLocal.classList.remove('d-none');
        groupCloud.classList.add('d-none');
      }
    };

    const setEventoUI = (isEvento)=>{
      if (!lblEvento || !elPrecio) return;
      lblEvento.textContent = isEvento ? 'Sí' : 'No';
      if (isEvento) {
        elPrecio.value = '0';
        elPrecio.setAttribute('disabled','disabled');
      } else {
        if(elPrecio.value==='0') elPrecio.value='20000';
        elPrecio.removeAttribute('disabled');
      }
    };

    const setTiendaUI = (isTienda)=>{
      if (!lblTienda) return;
      lblTienda.textContent = isTienda ? 'Sí' : 'No';
      // Exclusión mutua: tienda <-> evento
      if (isTienda && swEvento && swEvento.checked) {
        swEvento.checked = false;
        setEventoUI(false);
      }
    };

    // exclusión mutua al tocar cualquiera
    swEvento?.addEventListener('change', ()=>{
      setEventoUI(swEvento.checked);
      if (swEvento.checked && swTienda && swTienda.checked) {
        swTienda.checked = false;
        setTiendaUI(false);
      }
    });
    swTienda?.addEventListener('change', ()=> setTiendaUI(swTienda.checked));

    swOrigen?.addEventListener('change', ()=> setOrigenUI(swOrigen.checked));
    elTipo?.addEventListener('change', updateTpl);

    async function nextCosId(){
      const s = await db.collection('cosmetics').get();
      let max = 0;
      s.docs.forEach(d=>{
        const m = /^cos_id_(\d+)$/.exec(d.id);
        if (m) max = Math.max(max, parseInt(m[1],10));
      });
      return `cos_id_${max+1}`;
    }

    function openCreate(defaultTipo){
      if(!modal) return;
      title.textContent = 'Crear cosmético';
      elId.value = '';
      elNombre.value = '';
      elTipo.value = defaultTipo || 'cabeza';
      swOrigen.checked = false; setOrigenUI(false);
      swTienda.checked = true;  setTiendaUI(true);
      swEvento.checked = false; setEventoUI(false);
      elPrecio.value = '20000';
      elAsset.value = '';
      elUrl.value   = '';
      updateTpl();
      btnCreate.classList.remove('d-none');
      btnSave.classList.add('d-none');
      modal.show();
    }

    async function openEdit(tr){
      if(!modal) return;
      const id    = tr.dataset.id;
      const tipo  = tr.dataset.tipo;
      const origen= tr.dataset.origen;
      const asset = tr.dataset.asset || '';

      let tienda = true, evento = /sí/i.test(tr.children[6].textContent);
      try {
        const doc = await db.collection('cosmetics').doc(id).get();
        const d = doc.data()||{};
        if (typeof d.cos_tienda === 'boolean') tienda = d.cos_tienda;
        if (typeof d.cos_evento === 'boolean') evento = d.cos_evento;
      } catch {}

      title.textContent = 'Editar cosmético';
      elId.value     = id;
      elNombre.value = tr.children[1].textContent.trim();
      elTipo.value   = tipo;

      const isCloud  = (origen === 'cloudinary');
      swOrigen.checked = isCloud; setOrigenUI(isCloud);
      if (isCloud){ elUrl.value = asset; elAsset.value=''; }
      else { elAsset.value = asset; elUrl.value=''; }

      swTienda.checked = !!tienda; setTiendaUI(swTienda.checked);
      swEvento.checked = !!evento; setEventoUI(swEvento.checked);

      const priceParsed = parseInt((tr.children[4].textContent||'0').replace(/[^\d]/g,''), 10) || 0;
      elPrecio.value = String(priceParsed);

      updateTpl();
      btnCreate.classList.add('d-none');
      btnSave.classList.remove('d-none');
      modal.show();
    }

    document.body.addEventListener('click', (e)=>{
      const newBtn = e.target.closest('button[data-action="new"]');
      if (newBtn) {
        openCreate(newBtn.dataset.tipo);
        return;
      }

      const btn = e.target.closest('button[data-action]');
      if(!btn) return;
      const tr = btn.closest('tr[data-id]');
      if(!tr) return;

      if (btn.dataset.action === 'edit') openEdit(tr);
      if (btn.dataset.action === 'del') {
        if (!confirm('¿Eliminar definitivamente este cosmético?')) return;
        const id = tr.dataset.id;
        db.collection('cosmetics').doc(id).delete()
          .then(()=>{
            showAlert('success','Cosmético eliminado.');
            tr.remove();
          })
          .catch((err)=>{
            console.error(err);
            showAlert('danger','No se pudo eliminar.');
          });
      }
    });

    // Guardar edición
    $('#btnCosSave')?.addEventListener('click', async ()=>{
      const id = elId.value;
      const payload = {
        cos_nombre:   elNombre.value.trim(),
        cos_tipo:     elTipo.value,
        cos_evento:   swEvento.checked === true,
        cos_tienda:   swTienda.checked === true,
        cos_precio:   swEvento.checked ? 0 : parseInt(elPrecio.value,10),
        cos_assetType: swOrigen.checked ? 'cloudinary' : 'local',
      };
      payload.cos_asset = swOrigen.checked ? elUrl.value.trim() : elAsset.value.trim();

      try{
        await db.collection('cosmetics').doc(id).set(payload,{merge:true});
        showAlert('success','Cosmético actualizado.');
        modal.hide();
        setTimeout(()=>location.reload(), 400);
      }catch(err){
        console.error(err);
        showAlert('danger','No se pudo guardar.');
      }
    });

    // Crear nuevo
    $('#btnCosCreate')?.addEventListener('click', async ()=>{
      const id = await nextCosId();
      const payload = {
        cos_nombre:   elNombre.value.trim(),
        cos_tipo:     elTipo.value,
        cos_evento:   swEvento.checked === true,
        cos_tienda:   swTienda.checked === true,
        cos_precio:   swEvento.checked ? 0 : parseInt(elPrecio.value,10),
        cos_assetType: swOrigen.checked ? 'cloudinary' : 'local',
        cos_createdAt: firebase.firestore.FieldValue.serverTimestamp(),
        cos_activo: true
      };
      payload.cos_asset = swOrigen.checked ? elUrl.value.trim() : elAsset.value.trim();

      try{
        await db.collection('cosmetics').doc(id).set(payload,{merge:true});
        showAlert('success','Cosmético creado.');
        modal.hide();
        setTimeout(()=>location.reload(), 400);
      }catch(err){
        console.error(err);
        showAlert('danger','No se pudo crear.');
      }
    });

    // Inicial template link
    if (elTipo && tplLink) updateTpl();
  })();
}

// ================== ROOMS / EVENTS / VERSUS / RANKINGS ==================
if (document.body.dataset.page === 'admin-rooms') {
  (async ()=>{
    clearAlert();

    const user = await waitForAuthReady();
    if (!user) {
      showAlert('warning','No hay sesión de Firebase activa. Volvé a iniciar sesión.');
      setTimeout(()=>{ window.location.href = '../admin.php'; }, 900);
      return;
    }

    const $id = (id)=>document.getElementById(id);
    const fmtDateTime = (ts)=>{
      if (!ts) return '—';
      try{
        const d = ts.toDate ? ts.toDate() :
          (ts.seconds ? new Date(ts.seconds*1000) : new Date(ts));
        return d.toLocaleString('es-AR');
      }catch{ return '—'; }
    };

    async function borrarDoc(col, id){
      if(!confirm(`¿Eliminar ${col}/${id}?`)) return;
      await db.collection(col).doc(id).delete();
      if(col==='events')      await loadEvents();
      else if(col==='versus') await loadVersus();
      else if(col==='rooms')  await loadRooms();
      else if(col==='rankings') await loadRankings();
    }

    // --------- VERSUS ---------
    async function loadVersus(){
      const tbody = $id('tblVersus');
      if(!tbody) return;
      tbody.innerHTML = '<tr><td colspan="7" class="text-secondary">Cargando…</td></tr>';
      try{
        const snap = await db.collection('versus').get();
        tbody.innerHTML = '';
        if(snap.empty){
          tbody.innerHTML = '<tr><td colspan="7" class="text-secondary">Sin versus.</td></tr>';
          return;
        }
        snap.forEach(doc=>{
          const v = doc.data() || {};
          const playersArr = Array.isArray(v.ver_players)
            ? v.ver_players
            : (v.ver_players ? Object.keys(v.ver_players) : []);
          const playersCount = playersArr.length;

          // Modalidad
          let mode = '—';
          if (typeof v.ver_type === 'string') {
            mode = v.ver_type.toLowerCase()==='steps' ? 'Pasos' :
                   v.ver_type.toLowerCase()==='days'  ? 'Días'  : v.ver_type;
          } else if (typeof v.ver_type === 'boolean') {
            mode = v.ver_type ? 'Pasos' : 'Días';
          } else {
            if ((v.ver_targetSteps ?? 0) > 0) mode = 'Pasos';
            else if ((v.ver_days ?? 0) > 0)   mode = 'Días';
          }

          // Target según modalidad
          let targetVal = '—';
          if (mode === 'Pasos')      targetVal = v.ver_targetSteps ?? 0;
          else if (mode === 'Días')  targetVal = v.ver_days ?? 0;
          else                       targetVal = v.ver_targetSteps ?? v.ver_days ?? '—';

          // Pasos por jugador
          const prog = v.ver_progress || {};
          const stepsByPlayer = [];
          if (playersArr.length) {
            playersArr.forEach((uid, idx)=>{
              const p = prog[uid] || {};
              const steps = p.steps ?? 0;
              stepsByPlayer.push(`${idx+1}: ${steps}`);
            });
          } else {
            Object.keys(prog).forEach((uid, idx)=>{
              const p = prog[uid] || {};
              const steps = p.steps ?? 0;
              stepsByPlayer.push(`${idx+1}: ${steps}`);
            });
          }
          const stepsText = stepsByPlayer.length ? stepsByPlayer.join(' • ') : '—';

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td class="mono-cell"><code class="small">${doc.id}</code></td>
            <td>${fmtDateTime(v.ver_createdAt)}</td>
            <td>${playersCount}</td>
            <td>${mode}</td>
            <td>${targetVal}</td>
            <td class="small">${stepsText}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-danger" data-col="versus" data-id="${doc.id}">Eliminar</button>
            </td>`;
          tbody.appendChild(tr);
        });
      }catch(err){
        console.error(err);
        tbody.innerHTML =
          '<tr><td colspan="7" class="text-danger small">No se pudieron cargar los versus.</td></tr>';
        const msg = err.code === 'permission-denied'
          ? 'Permiso denegado al leer la colección versus.'
          : 'Error al cargar versus.';
        showAlert('danger', msg);
      }
    }

    // --------- ROOMS ---------
    async function loadRooms(){
      const tbody = $id('tblRooms');
      if(!tbody) return;
      tbody.innerHTML = '<tr><td colspan="8" class="text-secondary">Cargando…</td></tr>';
      try{
        const snap = await db.collection('rooms').get();
        tbody.innerHTML = '';
        if (snap.empty){
          tbody.innerHTML = '<tr><td colspan="8" class="text-secondary">Sin salas.</td></tr>';
          return;
        }
        snap.forEach(doc=>{
          const r = doc.data() || {};

          // Modalidad
          let mode = '—';
          if (typeof r.roo_type === 'string') {
            const t = r.roo_type.toLowerCase();
            mode = t==='steps' ? 'Pasos' : t==='days' ? 'Días' : r.roo_type;
          } else if (typeof r.roo_type === 'boolean') {
            mode = r.roo_type ? 'Pasos' : 'Días';
          } else {
            if ((r.roo_targetSteps ?? 0) > 0) mode = 'Pasos';
            else if ((r.roo_days ?? 0) > 0)   mode = 'Días';
          }

          let targetVal = '—';
          if (mode === 'Pasos')      targetVal = r.roo_targetSteps ?? 0;
          else if (mode === 'Días')  targetVal = r.roo_days ?? 0;
          else                       targetVal = r.roo_targetSteps ?? r.roo_days ?? '—';

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td class="mono-cell"><code class="small">${doc.id}</code></td>
            <td>${fmtDateTime(r.roo_createdAt)}</td>
            <td>${r.roo_code ? `<span class="badge badge-mono">${escapeHtml(r.roo_code)}</span>` : '—'}</td>
            <td class="small mono-cell"><code>${r.roo_user || '—'}</code></td>
            <td>${mode}</td>
            <td>${targetVal}</td>
            <td>${r.roo_public ? 'Sí' : 'No'}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-danger" data-col="rooms" data-id="${doc.id}">Eliminar</button>
            </td>`;
          tbody.appendChild(tr);
        });
      }catch(err){
        console.error(err);
        tbody.innerHTML =
          '<tr><td colspan="8" class="text-danger small">No se pudieron cargar las salas.</td></tr>';
        const msg = err.code === 'permission-denied'
          ? 'Permiso denegado al leer la colección rooms.'
          : 'Error al cargar salas.';
        showAlert('danger', msg);
      }
    }

    // --------- RANKINGS ---------
    async function loadRankings(){
      const tbody = $id('tblRankings');
      if(!tbody) return;
      tbody.innerHTML = '<tr><td colspan="5" class="text-secondary">Cargando…</td></tr>';
      try{
        const snap = await db.collection('rankings').get();
        tbody.innerHTML = '';
        if (snap.empty){
          tbody.innerHTML = '<tr><td colspan="5" class="text-secondary">Sin rankings.</td></tr>';
          return;
        }
        snap.forEach(doc=>{
          const r = doc.data() || {};
          const players = Array.isArray(r.ran_players)
            ? r.ran_players.length
            : (r.ran_players ? Object.keys(r.ran_players).length : 0);
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td class="mono-cell"><code class="small">${doc.id}</code></td>
            <td>${fmtDateTime(r.ran_createdAt)}</td>
            <td>${r.ran_weekKey ?? '—'}</td>
            <td>${players}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-danger" data-col="rankings" data-id="${doc.id}">Eliminar</button>
            </td>`;
          tbody.appendChild(tr);
        });
      }catch(err){
        console.error(err);
        tbody.innerHTML =
          '<tr><td colspan="5" class="text-danger small">No se pudieron cargar los rankings.</td></tr>';
        const msg = err.code === 'permission-denied'
          ? 'Permiso denegado al leer la colección rankings.'
          : 'Error al cargar rankings.';
        showAlert('danger', msg);
      }
    }

    // --------- EVENTS ---------
    async function loadEvents(){
      const tbody = $id('tblEvents');
      const badge = $id('evtActiveInfo');
      if(!tbody) return;
      tbody.innerHTML = '<tr><td colspan="8" class="text-secondary">Cargando…</td></tr>';
      let activeCount = 0;

      try{
        const snap = await db.collection('events').get();
        tbody.innerHTML = '';
        if (snap.empty){
          tbody.innerHTML =
            '<tr><td colspan="8" class="text-secondary">No hay eventos creados.</td></tr>';
          if (badge){
            badge.textContent = '0 activos';
            badge.className = 'badge text-bg-secondary';
          }
          return;
        }

        snap.forEach(doc=>{
          const e = doc.data() || {};
          const active = !!e.ev_active;
          if (active) activeCount++;

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td class="mono-cell"><code class="small">${doc.id}</code></td>
            <td>${active ? '<span class="badge text-bg-success">Sí</span>' : '<span class="badge text-bg-secondary">No</span>'}</td>
            <td>${fmtDateTime(e.ev_startAt)}</td>
            <td>${fmtDateTime(e.ev_endAt)}</td>
            <td>${e.ev_targetSteps ?? 0}</td>
            <td>${e.ev_rewardCoins ?? 0}</td>
            <td>${e.ev_bossImg ? `<a href="${e.ev_bossImg}" target="_blank" class="small text-decoration-underline">ver</a>` : '—'}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-danger" data-col="events" data-id="${doc.id}">Eliminar</button>
            </td>`;
          tbody.appendChild(tr);
        });

        if (badge){
          badge.textContent =
            activeCount === 1
              ? '1 activo'
              : `${activeCount} activos`;
          badge.className =
            'badge ' +
            (activeCount === 1
              ? 'text-bg-success'
              : activeCount > 1
                ? 'text-bg-danger'
                : 'text-bg-secondary');
        }
      }catch(err){
        console.error(err);
        tbody.innerHTML =
          '<tr><td colspan="8" class="text-danger small">No se pudieron cargar los eventos.</td></tr>';
        const msg = err.code === 'permission-denied'
          ? 'Permiso denegado al leer la colección events.'
          : 'Error al cargar eventos.';
        showAlert('danger', msg);
      }
    }

    // --- Crear evento desde el formulario ---
    const evForm   = $id('evForm');
    const evStart  = $id('evStart');
    const evEnd    = $id('evEnd');
    const evTarget = $id('evTarget');
    const evReward = $id('evReward');
    const evActive = $id('evActive');
    const evBoss   = $id('evBoss');

    evForm?.addEventListener('submit', async (e)=>{
      e.preventDefault();
      try{
        const payload = {
          ev_active: evActive?.checked ?? true,
          ev_targetSteps: parseInt(evTarget?.value || '0', 10) || 0,
          ev_rewardCoins: parseInt(evReward?.value || '0', 10) || 0,
        };

        if (evStart?.value) {
          payload.ev_startAt = firebase.firestore.Timestamp.fromDate(
            new Date(evStart.value)
          );
        }
        if (evEnd?.value) {
          payload.ev_endAt = firebase.firestore.Timestamp.fromDate(
            new Date(evEnd.value)
          );
        }
        const bossUrl = (evBoss?.value || '').trim();
        if (bossUrl) payload.ev_bossImg = bossUrl;

        await db.collection('events').add(payload);
        showAlert('success','Evento creado correctamente.');
        // limpiar rápido
        if (evBoss)   evBoss.value   = '';
        if (evTarget) evTarget.value = '100000';
        if (evReward) evReward.value = '50000';
        await loadEvents();
      }catch(err){
        console.error(err);
        const msg = err.code === 'permission-denied'
          ? 'No tenés permisos para crear eventos (revisar reglas / isAdmin).'
          : 'No se pudo crear el evento.';
        showAlert('danger', msg);
      }
    });

    // handler único para botones Eliminar (events/versus/rooms/rankings)
    document.addEventListener('click', (e)=>{
      const btn = e.target.closest('button[data-col][data-id]');
      if(!btn) return;
      const col = btn.dataset.col;
      const id  = btn.dataset.id;
      borrarDoc(col, id).catch(err=>console.error(err));
    });

    // boot
    await Promise.all([
      loadVersus(),
      loadRooms(),
      loadRankings(),
      loadEvents()
    ]);
  })();
}