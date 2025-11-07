// Smooth scroll
document.addEventListener('click', (e)=>{
  const a = e.target.closest('a[href^="#"]');
  if(!a) return;
  const el = document.querySelector(a.getAttribute('href'));
  if(el){
    e.preventDefault();
    el.scrollIntoView({behavior:'smooth', block:'start'});
    const nav = document.getElementById('navMain');
    if(nav && nav.classList.contains('show')){
      bootstrap.Collapse.getOrCreateInstance(nav).hide();
    }
  }
});

// Header blanco: sombra al hacer scroll + link activo
(()=>{
  const nav = document.querySelector('.nav-clean');
  const onScroll = ()=>{
    if(!nav) return;
    window.scrollY > 4 ? nav.classList.add('scrolled') : nav.classList.remove('scrolled');
  };
  onScroll(); window.addEventListener('scroll', onScroll, {passive:true});

  // resaltar sección activa
  const links = [...document.querySelectorAll('.navbar .nav-link[href^="#"]')];
  const map = new Map();
  links.forEach(a=>{
    const el = document.querySelector(a.getAttribute('href'));
    if(el) map.set(el, a);
  });
  if(map.size){
    const io = new IntersectionObserver(entries=>{
      entries.forEach(e=>{
        const a = map.get(e.target);
        if(e.isIntersecting){
          links.forEach(l=>l.classList.remove('active'));
          a?.classList.add('active');
        }
      });
    }, {rootMargin:"-45% 0px -50% 0px"});
    map.forEach((_, el)=>io.observe(el));
  }
})();

/* ===== Animación de avatar ===== */
(()=>{
  const stage = document.getElementById('avatarStage');
  if(!stage) return;

  const slots = {
    skin:  stage.querySelector('.layer-skin'),
    legs:  stage.querySelector('.layer-legs'),
    torso: stage.querySelector('.layer-torso'),
    feet:  stage.querySelector('.layer-feet'),
    head:  stage.querySelector('.layer-head'),
  };

  const ASSETS = (window.PODOVS_ASSETS && Object.values(window.PODOVS_ASSETS).some(a => a.length))
    ? window.PODOVS_ASSETS
    : {
        skin:  ['img/piel_startskin.png','img/piel_deepebony.png','img/piel_minion.png','img/piel_pale.png','img/piel_pinker.png','img/piel_sunburn.png'],
        legs:  ['img/pierna_bluejeans.png','img/pierna_ninja.png','img/pierna_nikeair.png'],
        torso: ['img/torso_greenshirt.png','img/torso_ninja.png','img/torso_nikeair.png'],
        feet:  ['img/pies_comicallylong.png','img/pies_ninja.png','img/pies_nikeair.png'],
        head:  ['img/cabeza_bwcap.png','img/cabeza_horns.png','img/cabeza_patch.png','img/cabeza_ninja.png','img/cabeza_nikeair.png'],
      };

  const pick = arr => arr[Math.floor(Math.random()*arr.length)];
  const shuffle = arr => { for(let i=arr.length-1;i>0;i--){ const j=Math.floor(Math.random()*(i+1)); [arr[i],arr[j]]=[arr[j],arr[i]];} return arr; };
  const preload = urls => Promise.all([...new Set(urls)].map(u => new Promise(res=>{ const i=new Image(); i.onload=res; i.onerror=res; i.src=u; })));

  function wear(slot, src){
    const img = slots[slot]; if(!img) return;
    img.src = src;
    img.classList.remove('popping','show');
    requestAnimationFrame(()=>{ img.classList.add('show','popping'); setTimeout(()=>img.classList.remove('popping'),280); });
  }
  function getRandomLook(){
    return {
      skin:  ASSETS.skin.length  ? pick(ASSETS.skin)  : null,
      legs:  ASSETS.legs.length  ? pick(ASSETS.legs)  : null,
      torso: ASSETS.torso.length ? pick(ASSETS.torso) : null,
      feet:  ASSETS.feet.length  ? pick(ASSETS.feet)  : null,
      head:  ASSETS.head.length  ? pick(ASSETS.head)  : null,
    };
  }
  const ALL = [...ASSETS.skin, ...ASSETS.legs, ...ASSETS.torso, ...ASSETS.feet, ...ASSETS.head];

  (async function playLoop(){
    await preload(ALL);
    while(true){
      const look1 = getRandomLook();
      const order1 = shuffle(['skin','legs','torso','feet','head']);
      for(const slot of order1){ if(look1[slot]) wear(slot, look1[slot]); await new Promise(r=>setTimeout(r, 240 + Math.random()*160)); }
      await new Promise(r=>setTimeout(r, 1000));
      ['legs','torso','feet','head'].forEach(k => slots[k].classList.remove('show'));
      await new Promise(r=>setTimeout(r, 250));
      const look2 = getRandomLook();
      const order2 = shuffle(['legs','torso','feet','head']);
      for(const slot of order2){ if(look2[slot]) wear(slot, look2[slot]); await new Promise(r=>setTimeout(r, 230 + Math.random()*150)); }
      await new Promise(r=>setTimeout(r, 1100));
    }
  })().catch(err => console.error('[Avatar]', err));
})();

/* ===== Metas y progreso ===== */
(()=>{
  const demo  = document.getElementById('goalDemo');
  if(!demo) return;

  const bar   = demo.querySelector('.goal-bar');
  const fill  = document.getElementById('goalFill');
  const coins = document.getElementById('goalCoins');
  const text  = document.getElementById('goalText');

  const INITIAL_WIDTH = 60;
  const INITIAL_GOAL  = 3000;
  let baseWidth = INITIAL_WIDTH;
  let stepsGoal = INITIAL_GOAL;

  let speed = 0.35;
  let pct = 0;
  let current = 0;
  let completionsSinceReset = 0;

  const fmt = n => n.toLocaleString('es-AR');
  const updateLabel = () => text.textContent = `${fmt(Math.min(current, stepsGoal))} / ${fmt(stepsGoal)} pasos`;

  function burstCoins(count){
    if(count<=0) return;
    const rectBar = bar.getBoundingClientRect();
    const originX = rectBar.width;
    const originY = rectBar.height/2;

    for(let i=0;i<count;i++){
      const img = document.createElement('img');
      img.src = 'img/coin.png';
      img.alt = '';
      img.className = 'goal-coin';
      img.style.left = `${originX - 14}px`;
      img.style.top  = `${originY - 14}px`;

      const ang = (-Math.PI/2) + (Math.random()*Math.PI*0.6 - Math.PI*0.3);
      const pow = 80 + Math.random()*60;
      const dx  = Math.cos(ang)*pow;
      const dy  = Math.sin(ang)*pow;
      const rot = (Math.random()<.5?-1:1) * (180 + Math.random()*180);
      const dur = 550 + Math.random()*400;
      img.style.setProperty('--dx',  `${dx}px`);
      img.style.setProperty('--dy',  `${dy}px`);
      img.style.setProperty('--rot', `${rot}deg`);
      img.style.setProperty('--dur', `${dur}ms`);
      coins.appendChild(img);
      setTimeout(()=> img.remove(), dur+120);
    }
  }

  function resetCycle(){
    completionsSinceReset = 0;
    baseWidth = INITIAL_WIDTH;
    stepsGoal = INITIAL_GOAL;
    pct = 0; current = 0;
    bar.style.setProperty('--bar-width', `${baseWidth}%`);
    fill.style.width = '0%';
    updateLabel();
  }

  function nextLevel(){
    completionsSinceReset++;
    if(completionsSinceReset >= 3){
      burstCoins(4);
      resetCycle();
      return;
    }
    baseWidth = Math.min(95, baseWidth + 7);
    bar.style.setProperty('--bar-width', `${baseWidth}%`);
    stepsGoal = Math.round(INITIAL_GOAL + completionsSinceReset*800 + Math.random()*400);
    pct = 0; current = 0;
    fill.style.width = '0%';
    updateLabel();
    burstCoins(2 + completionsSinceReset);
  }

  function tick(){
    pct += speed + Math.random()*0.18;
    pct = Math.min(pct, 100);
    fill.style.width = `${pct}%`;

    const deltaSteps = Math.round((stepsGoal / 240) * (0.45 + Math.random()*0.7));
    current = Math.min(stepsGoal, current + deltaSteps);
    updateLabel();

    if(pct >= 100){
      burstCoins(2 + completionsSinceReset);
      setTimeout(nextLevel, 150);
    }
    requestAnimationFrame(tick);
  }

  bar.style.setProperty('--bar-width', `${baseWidth}%`);
  updateLabel();
  requestAnimationFrame(tick);
})();

/* ===== Duelos / Coop ===== */
(()=>{
  const root   = document.querySelector('.duel-stage');
  if(!root) return;

  const view      = root.querySelector('.duel-viewport');
  const scene1v1  = view.querySelector('.scene-1v1');
  const sceneCoop = view.querySelector('.scene-coop');

  const p1Fill  = scene1v1.querySelector('.vs-fill.p1');
  const p2Fill  = scene1v1.querySelector('.vs-fill.p2');
  const p1Txt   = scene1v1.querySelector('.vs-steps.p1');
  const p2Txt   = scene1v1.querySelector('.vs-steps.p2');

  const coopMembers = [...sceneCoop.querySelectorAll('.member')];
  const bossFill = sceneCoop.querySelector('.boss-fill');
  const bossPct  = sceneCoop.querySelector('.boss-pct');

  function setActive(scene){ [scene1v1, sceneCoop].forEach(s => s.classList.remove('active')); scene.classList.add('active'); }
  const fmt = n => n.toLocaleString('es-AR');

  async function play1v1(){
    setActive(scene1v1);
    const p1 = 2200 + Math.floor(Math.random()*2500);
    const p2 = 2200 + Math.floor(Math.random()*2500);
    let p1w = Math.round((p1/(p1+p2))*100);
    let p2w = 100 - p1w;
    const MIN = 12;
    if (p1w < MIN){ p1w = MIN; p2w = 100 - p1w; }
    if (p2w < MIN){ p2w = MIN; p1w = 100 - p2w; }
    p1Fill.style.width = '0%'; p2Fill.style.width = '0%';
    p1Txt.textContent = '0';   p2Txt.textContent = '0';
    void p1Fill.offsetWidth;   void p2Fill.offsetWidth;
    const t0 = performance.now();
    await new Promise(res=>{
      const step = now=>{
        const p = Math.min(1, (now - t0)/1200);
        const ease = p<.5 ? 2*p*p : -1+(4-2*p)*p;
        p1Fill.style.width = `${Math.round(p1w*ease)}%`;
        p2Fill.style.width = `${Math.round(p2w*ease)}%`;
        p1Txt.textContent = fmt(Math.round(p1*ease));
        p2Txt.textContent = fmt(Math.round(p2*ease));
        if(p<1) requestAnimationFrame(step); else res();
      };
      requestAnimationFrame(step);
    });
    await new Promise(r=>setTimeout(r, 900));
  }

  async function playCoop(){
    setActive(sceneCoop);
    const fills = [];
    coopMembers.forEach(m=>{
      const bar = m.querySelector('.mini .fill');
      const v = 25 + Math.random()*70;
      bar.style.width = '0%'; void bar.offsetWidth; bar.style.width = `${v}%`;
      fills.push(v);
    });
    const total = fills.reduce((a,b)=>a+b,0);
    let hp = Math.max(0, Math.round(100 - (total/3.6)));
    if (hp > 0 && total > 340 && Math.random() < 0.35) hp = 0;
    bossFill.style.width = `${hp}%`;
    bossPct.textContent  = `${hp}%`;
    await new Promise(r=>setTimeout(r, 1600));
  }

  (async function loop(){
    ['img/stickrunning.png','img/stickrunning-vs.png','img/pixelmonster.png'].forEach(src=>{ const i=new Image(); i.src=src; });
    while(true){ await play1v1(); await playCoop(); }
  })();
})();

/* ===== Ranking dinámico (FLIP) ===== */
(()=>{
  const stage = document.querySelector('.ranking-stage');
  if(!stage) return;
  const list = stage.querySelector('.ranking-list');
  let items = [...list.children].map((el, idx) => ({
    el, idx,
    name: el.firstElementChild.textContent.trim(),
    points: parseInt(el.querySelector('.points').textContent.replace(/\D/g,''),10) || 3000,
    vel: (Math.random()*20+10) * (Math.random()<.5?-1:1)
  }));
  const clamp = (v,min,max)=>Math.max(min,Math.min(max,v));
  const fmt   = n => n.toLocaleString('es-AR');

  function updatePoints(dt){
    items.forEach(it=>{
      const jitter = (Math.random()-0.5)*30;
      it.points += (it.vel*dt*0.06) + jitter;
      if(it.points < 2000){ it.points = 2000; it.vel = Math.abs(it.vel); }
      if(it.points > 7000){ it.points = 7000; it.vel = -Math.abs(it.vel); }
      it.el.querySelector('.points').textContent = fmt(Math.round(it.points));
    });
  }
  function reorderWithFLIP(){
    const first = new Map(items.map(it => [it.el, it.el.getBoundingClientRect().top]));
    const prevIndex = new Map(items.map((it,i)=>[it.el,i]));
    items.sort((a,b)=>b.points - a.points);
    items.forEach(it => list.appendChild(it.el));
    items.forEach((it,i)=>{
      const lastTop = it.el.getBoundingClientRect().top;
      const inv = first.get(it.el) - lastTop;
      it.el.style.transform = `translateY(${inv}px)`;
      it.el.style.transition = 'transform 0s';
      const from = prevIndex.get(it.el);
      if(from != null){
        if(i < from){ it.el.classList.add('overtake'); setTimeout(()=> it.el.classList.remove('overtake'), 700); }
        else if(i > from){ it.el.classList.add('overtaken'); setTimeout(()=> it.el.classList.remove('overtaken'), 700); }
      }
      requestAnimationFrame(()=>{ it.el.style.transition = 'transform 520ms cubic-bezier(.2,.7,.2,1)'; it.el.style.transform = 'translateY(0)'; });
    });
  }
  let last = performance.now();
  let accum = 0;
  function loop(now){
    const dt = clamp((now - last)/16.67, 0, 3);
    last = now;
    updatePoints(dt);
    accum += now - (now - dt*16.67);
    if(accum >= 700){ accum = 0; reorderWithFLIP(); }
    requestAnimationFrame(loop);
  }
  reorderWithFLIP(); requestAnimationFrame(loop);
})();

/* ===== Anotador: flechas laterales ===== */
(()=>{
  document.querySelectorAll('.color-annotator').forEach(row=>{
    const phone  = row.querySelector('.phone-annotator');
    const img    = phone.querySelector('.phone-shot');
    const marks  = [...phone.querySelectorAll('.hotspot')];
    const leftC  = [...row.querySelectorAll('.side.left  .callout')];
    const rightC = [...row.querySelectorAll('.side.right .callout')];
    const svg    = row.querySelector('.row-arrows');

    function fitImageContain(){ img.style.objectFit = 'contain'; }

    function placeCallouts(){
      const rRow = row.getBoundingClientRect();
      const rPhone = phone.getBoundingClientRect();
      const getHotY = (i)=> {
        const hs = marks[i];
        if(!hs) return 50;
        const r = hs.getBoundingClientRect();
        return ((r.top + r.height/2) - rPhone.top) / rPhone.height * 100;
      };
      leftC.forEach((c,i)=>{ c.style.top  = `${getHotY(i)}%`; });
      rightC.forEach((c,i)=>{ c.style.top = `${getHotY(leftC.length + i)}%`; });

      while(svg.firstChild) svg.removeChild(svg.firstChild);
      svg.setAttribute('viewBox', `0 0 ${Math.round(rRow.width)} ${Math.round(rRow.height)}`);

      function arrow(x1,y1,x2,y2,color){
        const path = document.createElementNS('http://www.w3.org/2000/svg','path');
        const midX = (x1+x2)/2;
        path.setAttribute('d', `M ${x1} ${y1} C ${midX} ${y1}, ${midX} ${y2}, ${x2} ${y2}`);
        path.setAttribute('stroke', color); path.setAttribute('stroke-width','3');
        path.setAttribute('fill','none'); path.setAttribute('stroke-linecap','round');
        svg.appendChild(path);
        const ang = Math.atan2(y2-y1, x2-x1), s=10;
        const p2x = x2 - Math.cos(ang - Math.PI/7)*s;
        const p2y = y2 - Math.sin(ang - Math.PI/7)*s;
        const p3x = x2 - Math.cos(ang + Math.PI/7)*s;
        const p3y = y2 - Math.sin(ang + Math.PI/7)*s;
        const head = document.createElementNS('http://www.w3.org/2000/svg','polygon');
        head.setAttribute('points', `${x2},${y2} ${p2x},${p2y} ${p3x},${p3y}`);
        head.setAttribute('fill', color);
        svg.appendChild(head);
      }

      leftC.forEach((c,i)=>{
        const hs = marks[i]; if(!hs) return;
        const rc = c.getBoundingClientRect();
        const rp = phone.getBoundingClientRect();
        const rh = hs.getBoundingClientRect();
        const from = { x: rp.left - rRow.left, y: rh.top + rh.height/2 - rRow.top };
        const to   = { x: rc.right - rRow.left, y: rc.top + rc.height/2 - rRow.top };
        arrow(from.x, from.y, to.x, to.y, getComputedStyle(hs).getPropertyValue('--c').trim() || '#0f172a');
      });

      rightC.forEach((c,i)=>{
        const hs = marks[leftC.length + i]; if(!hs) return;
        const rc = c.getBoundingClientRect();
        const rp = phone.getBoundingClientRect();
        const rh = hs.getBoundingClientRect();
        const from = { x: rp.right - rRow.left, y: rh.top + rh.height/2 - rRow.top };
        const to   = { x: rc.left - rRow.left,  y: rc.top + rc.height/2 - rRow.top };
        arrow(from.x, from.y, to.x, to.y, getComputedStyle(hs).getPropertyValue('--c').trim() || '#0f172a');
      });
    }

    const ro = new ResizeObserver(()=>{ fitImageContain(); placeCallouts(); });
    ro.observe(row);
    window.addEventListener('load', ()=>{ fitImageContain(); placeCallouts(); }, {once:true});
    window.addEventListener('resize', ()=>{ fitImageContain(); placeCallouts(); });
  });
})();
