<?php include __DIR__ . '/componentes/header.php'; ?>

<main>
  <!-- Hero -->
  <section class="position-relative overflow-hidden">
    <div class="hero-bg"></div>
    <div class="container py-5 py-lg-6">
      <div class="row align-items-center g-4">
        <div class="col-lg-6">
          <span class="badge rounded-pill text-bg-success-subtle border border-success-subtle mb-3">Caminar nunca fue tan divertido</span>
          <h1 class="display-5 fw-bold lh-tight">Convertí tus <span class="text-gradient-emerald">pasos</span> en <span class="text-gradient-amber">recompensas</span></h1>
          <p class="lead text-secondary mt-3">PodoVS es una app de pasos con estilo <strong>RPG casual</strong>: subí de nivel, personalizá tu avatar y competí sanamente con amigos. Metas diarias, cofres, eventos y rankings para motivarte a caminar todos los días.</p>
          <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="#descargar" class="btn btn-primary btn-lg rounded-pill">Descargar la app</a>
            <a href="#features" class="btn btn-outline-secondary btn-lg rounded-pill">Ver características</a>
          </div>
          <ul class="list-unstyled d-flex flex-wrap gap-4 mt-4 text-muted small">
            <li class="d-flex align-items-center gap-2"><img src="img/check.svg" width="18" height="18" alt="">Integración con APIs de pasos confiables</li>
            <li class="d-flex align-items-center gap-2"><img src="img/star.svg" width="18" height="18" alt="">Motivación y progreso diario</li>
          </ul>
        </div>
        <div class="col-lg-6">
          <div class="card shadow-sm rounded-4 overflow-hidden">
            <div class="ratio ratio-4x5 bg-body-tertiary d-flex align-items-center justify-content-center">
              <img src="img/avatar_placeholder.svg" class="img-fluid p-4" alt="Avatar PodoVS" style="max-height: 100%;">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features -->
  <section id="features" class="py-5">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="fw-bold h1">Características principales</h2>
        <p class="text-secondary">Todo lo que necesitás para mantener la constancia con diversión.</p>
      </div>

      <!-- 1) Avatar personalizable -->
      <div class="feature-box fb-emerald mb-4">
        <div class="row align-items-center g-4">
          <div class="col-lg-7">
            <div class="d-flex align-items-start gap-3">
              <span class="icon-bubble bg-success-subtle text-success-emphasis flex-shrink-0">
                <img src="img/crown.svg" width="20" height="20" alt="Avatar">
              </span>
              <div>
                <h3 class="h4 mb-1">Avatar personalizable</h3>
                <p class="text-secondary mb-0">
                  Equipá sombreros, remeras, jeans y zapatos. Ganá cosméticos de diferentes rarezas en cofres o compralos en la tienda.
                  La animación de la derecha se arma con <em>cosméticos al azar</em> leídos de tu carpeta <code>/img</code>.
                </p>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="avatar-stage avatar-stage--minimal" id="avatarStage" aria-label="Animación de avatar PodoVS">
              <img class="layer layer-skin"   alt="Piel base" />
              <img class="layer layer-legs"   alt="Piernas / pantalón" />
              <img class="layer layer-torso"  alt="Torso / remera" />
              <img class="layer layer-feet"   alt="Calzado" />
              <img class="layer layer-head"   alt="Cabeza / accesorio" />
            </div>
          </div>
        </div>
      </div>

      <?php
        $imgDir = __DIR__ . '/img';
        function listPrefix($dir, $prefix){
          $paths = glob($dir . '/' . $prefix . '*.png');
          return array_values(array_map(fn($p) => 'img/' . basename($p), $paths));
        }
        $assets = [
          'skin'  => listPrefix($imgDir, 'piel_'),
          'legs'  => listPrefix($imgDir, 'pierna_'),
          'torso' => listPrefix($imgDir, 'torso_'),
          'feet'  => listPrefix($imgDir, 'pies_'),
          'head'  => listPrefix($imgDir, 'cabeza_'),
        ];
      ?>
      <script>window.PODOVS_ASSETS = <?php echo json_encode($assets, JSON_UNESCAPED_SLASHES); ?>;</script>

      <!-- 2) Metas y progreso -->
      <div class="feature-box fb-sky my-4">
        <div class="row align-items-center g-4">
          <div class="col-lg-7 order-lg-1 order-2">
            <div class="d-flex align-items-start gap-3">
              <span class="icon-bubble bg-info-subtle text-info-emphasis flex-shrink-0">
                <img src="img/chart.svg" width="20" height="20" alt="Metas">
              </span>
              <div>
                <h3 class="h4 mb-1">Metas y progreso</h3>
                <p class="text-secondary mb-0">
                  Definí metas diarias y semanales. La barra se llena con tus pasos y, al completar, ¡llueven monedas!
                  En cada ciclo la meta crece un poco para mantener el desafío.
                </p>
              </div>
            </div>
          </div>
          <div class="col-lg-5 order-lg-2 order-1">
            <div class="goal-demo" id="goalDemo">
              <div class="goal-bar">
                <div class="goal-fill" id="goalFill"></div>
                <div class="goal-label"><span id="goalText">0 / 3.000 pasos</span></div>
              </div>
              <div class="goal-coins" id="goalCoins" aria-hidden="true"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- 3) Duelos 1v1 + Eventos -->
      <div class="feature-box fb-violet my-4">
        <div class="row align-items-center g-4">
          <div class="col-lg-7">
            <div class="d-flex align-items-start gap-3">
              <span class="icon-bubble bg-danger-subtle text-danger-emphasis flex-shrink-0">
                <img src="img/swords.svg" width="20" height="20" alt="Duelo">
              </span>
              <div>
                <h3 class="h4 mb-1">Duelos 1v1 y eventos cooperativos</h3>
                <p class="text-secondary mb-0">
                  <strong>Duelo 1v1:</strong> vos contra otro jugador, gana quien acumule más pasos.
                  <strong>Evento cooperativo:</strong> equipo de 4 suma pasos para vencer al <em>jefe mensual</em>.
                </p>
              </div>
            </div>
          </div>

          <div class="col-lg-5">
            <div class="duel-stage" aria-label="Animación de duelos y eventos">
              <div class="duel-viewport">

                <!-- Escena 1: 1v1 -->
                <div class="scene scene-1v1 active">
                  <div class="scene-inner vs-wrap">
                    <div class="vs-col">
                      <img class="sprite" src="img/stickrunning.png" alt="Jugador">
                      <div class="meta">
                        <div class="vs-name">Vos</div>
                        <div class="vs-steps p1">0</div>
                        <div class="vs-bar"><div class="vs-fill p1"></div></div>
                      </div>
                    </div>

                    <div class="vs-badge">VS</div>

                    <div class="vs-col">
                      <div class="meta text-end">
                        <div class="vs-name">Rival</div>
                        <div class="vs-steps p2">0</div>
                        <div class="vs-bar">
                          <div class="vs-fill p2" style="background:linear-gradient(90deg,#f59e0b,#ef4444)"></div>
                        </div>
                      </div>
                      <img class="sprite" src="img/stickrunning-vs.png" alt="Rival mirando a la izquierda">
                    </div>
                  </div>
                </div>

                <!-- Escena 2: Cooperativo -->
                <div class="scene scene-coop">
                  <div class="scene-inner coop-wrap">
                    <div class="party">
                      <div class="member">
                        <img class="sprite" src="img/stickrunning.png" alt="">
                        <div class="mini"><div class="fill"></div></div>
                      </div>
                      <div class="member">
                        <img class="sprite" src="img/stickrunning.png" alt="">
                        <div class="mini"><div class="fill"></div></div>
                      </div>
                      <div class="member">
                        <img class="sprite" src="img/stickrunning.png" alt="">
                        <div class="mini"><div class="fill"></div></div>
                      </div>
                      <div class="member">
                        <img class="sprite" src="img/stickrunning.png" alt="">
                        <div class="mini"><div class="fill"></div></div>
                      </div>
                    </div>

                    <div class="boss">
                      <img src="img/pixelmonster.png" alt="Jefe mensual">
                      <div class="boss-hp"><div class="boss-fill"></div></div>
                      <div class="small-muted">HP jefe: <span class="boss-pct">100%</span></div>
                    </div>
                  </div>
                </div>

              </div><!-- /duel-viewport -->
            </div>
          </div>
        </div>
      </div>

      <!-- 4) Rankings (animación de tabla) -->
      <div class="feature-box fb-amber mt-4">
        <div class="row align-items-center g-4">
          <div class="col-lg-7">
            <div class="d-flex align-items-start gap-3">
              <span class="icon-bubble bg-warning-subtle text-warning-emphasis flex-shrink-0">
                <img src="img/trophy.svg" width="20" height="20" alt="Rankings">
              </span>
              <div>
                <h3 class="h4 mb-1">Rankings y competitividad</h3>
                <p class="text-secondary mb-0">
                  Subí posiciones en la tabla diaria, semanal o mensual. Cada paso cuenta para mantener tu lugar en el podio 🏆
                </p>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="ranking-stage" aria-label="Animación de tabla de clasificación">
              <div class="ranking-list">
                <div class="ranking-entry you"><span>Vos</span><span class="points">4.231</span></div>
                <div class="ranking-entry"><span>Martín</span><span class="points">4.980</span></div>
                <div class="ranking-entry"><span>Julieta</span><span class="points">3.742</span></div>
                <div class="ranking-entry"><span>Rodolfo</span><span class="points">3.210</span></div>
                <div class="ranking-entry"><span>Sofía</span><span class="points">2.985</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- Screens -->
  <section id="screens" class="py-5 bg-body-tertiary">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="fw-bold h1">Un vistazo a la app</h2>
      </div>
      <div class="row g-3">
        <?php for($i=0;$i<6;$i++): ?>
          <div class="col-6 col-lg-4">
            <div class="ratio ratio-9x16 bg-white rounded-4 border d-flex align-items-center justify-content-center">
              <img src="img/phone_placeholder.svg" alt="Pantalla" class="p-4" style="max-height: 100%;">
            </div>
          </div>
        <?php endfor; ?>
      </div>
    </div>
  </section>

  <!-- How it works -->
  <section id="how" class="py-5">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="fw-bold h1">Cómo funciona</h2>
      </div>
      <div class="row g-4">
        <?php
          $steps = [
            ['icon'=>'footprints.svg','title'=>'Asigná tu meta','desc'=>'Elegí metas diarias/semanales que se adapten a vos.'],
            ['icon'=>'phone.svg','title'=>'Caminá y sumá','desc'=>'La app registra pasos con APIs confiables.'],
            ['icon'=>'gift.svg','title'=>'Recibí recompensas','desc'=>'Monedas, XP y cofres por tu constancia.'],
            ['icon'=>'trophy.svg','title'=>'Competí o cooperá','desc'=>'Rankings, duelos y eventos mensuales.'],
          ];
          foreach($steps as $s):
        ?>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 rounded-4">
            <div class="card-body">
              <div class="icon-bubble bg-info-subtle text-info-emphasis mb-2">
                <img src="img/<?php echo $s['icon']; ?>" width="24" height="24" alt="">
              </div>
              <h3 class="h6"><?php echo $s['title']; ?></h3>
              <p class="text-secondary small mb-0"><?php echo $s['desc']; ?></p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section id="descargar" class="py-5 bg-body-tertiary">
    <div class="container">
      <div class="card rounded-4 shadow-sm">
        <div class="card-body p-4 p-lg-5">
          <div class="row align-items-center g-4">
            <div class="col-lg-7">
              <h3 class="fw-bold">Listo para empezar a caminar con motivación</h3>
              <p class="text-secondary">Descargá PodoVS y convertí tus pasos en progreso. Compatible con Android. Próximamente versión web con progresión cruzada.</p>
              <div class="d-flex flex-wrap gap-2">
                <a href="#" class="btn btn-primary btn-lg rounded-pill">Google Play (próximamente)</a>
                <a href="#" class="btn btn-outline-secondary btn-lg rounded-pill">Descargar APK (beta)</a>
              </div>
            </div>
            <div class="col-lg-5">
              <ul class="list-unstyled small">
                <li class="d-flex align-items-start gap-2 mb-2"><img src="img/check.svg" width="18" height="18" alt=""><span>Registro e inicio de sesión simple</span></li>
                <li class="d-flex align-items-start gap-2 mb-2"><img src="img/check.svg" width="18" height="18" alt=""><span>Metas diarias/semanales y barra de progreso</span></li>
                <li class="d-flex align-items-start gap-2 mb-2"><img src="img/check.svg" width="18" height="18" alt=""><span>Cofres con sistema pity</span></li>
                <li class="d-flex align-items-start gap-2 mb-0"><img src="img/check.svg" width="18" height="18" alt=""><span>Eventos mensuales cooperativos (4 jugadores)</span></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq" class="py-5">
    <div class="container">
      <div class="text-center mb-4">
        <h2 class="fw-bold h1">Preguntas frecuentes</h2>
      </div>
      <div class="row g-3">
        <?php
          $faqs = [
            ['q'=>'¿Cómo cuenta los pasos?','a'=>'Integra APIs de pasos confiables del dispositivo (p. ej. Health Connect).'],
            ['q'=>'¿Se puede hacer trampa?','a'=>'Tenemos heurísticas básicas de detección de fraude (picos imposibles, velocidad, etc.).'],
            ['q'=>'¿Necesito internet?','a'=>'Funciona en modo lectura offline; al reconectar sincroniza el progreso.'],
            ['q'=>'¿Qué plataformas soporta?','a'=>'Android primero. Próximamente Web con panel e integración cruzada.'],
          ];
          foreach($faqs as $f):
        ?>
        <div class="col-md-6">
          <div class="card rounded-4 h-100">
            <div class="card-body">
              <h3 class="h6"><?php echo $f['q']; ?></h3>
              <p class="text-secondary small mb-0"><?php echo $f['a']; ?></p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/componentes/footer.php'; ?>
