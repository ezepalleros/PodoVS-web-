<?php
// index.php
session_start();
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$CONTACT_STATUS = $_GET['contact'] ?? '';
?>
<?php include __DIR__ . '/componentes/header.php'; ?>

<main>
  <!-- Hero -->
  <section class="position-relative overflow-hidden">
    <div class="hero-bg"></div>
    <div class="container py-5 py-lg-6">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <h1 class="display-5 fw-bold lh-tight">
            Convertí tus <span class="text-gradient-emerald">pasos</span> en <span class="text-gradient-amber">recompensas</span>
          </h1>
          <p class="lead text-secondary mt-3">
            PodoVS es una app de pasos con estilo <strong>RPG casual</strong>: subí de nivel, personalizá tu avatar y competí con amigos.
            Metas, cofres y rankings para mantenerte motivado.
          </p>

          <a href="#descargar" class="btn btn-primary btn-lg px-4 py-3 rounded-pill shadow-lg mt-3">
            <strong>Descargar la app</strong>
          </a>
        </div>

        <div class="col-lg-6 text-center">
          <img src="img/icon_podovs.png" class="floating-logo" alt="Logo PodoVS">
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
                </p>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="avatar-stage avatar-stage--minimal" id="avatarStage" aria-label="Animación de avatar PodoVS">
              <img class="layer layer-skin" alt="Piel base" />
              <img class="layer layer-legs" alt="Piernas / pantalón" />
              <img class="layer layer-torso" alt="Torso / remera" />
              <img class="layer layer-feet" alt="Calzado" />
              <img class="layer layer-head" alt="Cabeza / accesorio" />
            </div>
          </div>
        </div>
      </div>

      <?php
      $imgDir = __DIR__ . '/img';
      function listPrefix($dir, $prefix)
      {
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
      <script>
        window.PODOVS_ASSETS = <?php echo json_encode($assets, JSON_UNESCAPED_SLASHES); ?>;
      </script>

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
                  Completá las metas para ir subiendo de nivel y conseguir mejores recompensas (y pasos).
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

      <!-- 3) Duelos + Coop -->
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
                  <strong>Duelo 1v1:</strong> Vos contra otro jugador, recibirá muchas monedas el que haga más pasos.
                  <strong>Evento cooperativo:</strong> Un equipo de 4 suma pasos para vencer al jefe del mes.
                </p>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="duel-stage" aria-label="Animación de duelos y eventos">
              <div class="duel-viewport">
                <div class="scene scene-1v1 active">
                  <div class="scene-inner vs-wrap">
                    <div class="vs-col">
                      <img class="sprite" src="img/stickrunning.png" alt="Jugador">
                      <div class="meta">
                        <div class="vs-name">Vos</div>
                        <div class="vs-steps p1">0</div>
                        <div class="vs-bar">
                          <div class="vs-fill p1"></div>
                        </div>
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

                <div class="scene scene-coop">
                  <div class="scene-inner coop-wrap">
                    <div class="party">
                      <div class="member"><img class="sprite" src="img/stickrunning.png" alt="">
                        <div class="mini">
                          <div class="fill"></div>
                        </div>
                      </div>
                      <div class="member"><img class="sprite" src="img/stickrunning.png" alt="">
                        <div class="mini">
                          <div class="fill"></div>
                        </div>
                      </div>
                      <div class="member"><img class="sprite" src="img/stickrunning.png" alt="">
                        <div class="mini">
                          <div class="fill"></div>
                        </div>
                      </div>
                      <div class="member"><img class="sprite" src="img/stickrunning.png" alt="">
                        <div class="mini">
                          <div class="fill"></div>
                        </div>
                      </div>
                    </div>
                    <div class="boss">
                      <img src="img/pixelmonster.png" alt="Jefe mensual">
                      <div class="boss-hp">
                        <div class="boss-fill"></div>
                      </div>
                      <div class="small-muted">HP jefe: <span class="boss-pct">100%</span></div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 4) Rankings -->
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
                  Subí posiciones en las tablas semanales. Cada paso cuenta para mantener tu lugar en el podio.
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
        <p class="text-secondary mb-0">Deslizá para ver las pantallas.</p>
      </div>

      <div class="card rounded-4 shadow-sm overflow-hidden">
        <div class="card-body p-3 p-lg-4">

          <div class="screens-shell" aria-label="Navegación del carrusel">
            <button class="screens-arrow prev"
              type="button"
              data-bs-target="#screensCarousel"
              data-bs-slide="prev"
              aria-label="Pantalla anterior">
              <span aria-hidden="true">‹</span>
            </button>

            <div id="screensCarousel"
              class="carousel slide screens-carousel"
              data-bs-ride="carousel"
              data-bs-interval="9000"
              data-bs-pause="hover"
              data-bs-touch="true"
              data-bs-keyboard="true"
              aria-label="Carrusel de pantallas de la app">

              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img class="d-block mx-auto screens-img" src="img/screenshot_main.jpg" alt="Pantalla principal de PodoVS">
                </div>
                <div class="carousel-item">
                  <img class="d-block mx-auto screens-img" src="img/screenshot_shop.jpg" alt="Pantalla de tienda de PodoVS">
                </div>
                <div class="carousel-item">
                  <img class="d-block mx-auto screens-img" src="img/screenshot_versus.jpg" alt="Pantalla de duelos y salas de PodoVS">
                </div>
                <div class="carousel-item">
                  <img class="d-block mx-auto screens-img" src="img/screenshot_event.jpg" alt="Pantalla de evento cooperativo de PodoVS">
                </div>
                <div class="carousel-item">
                  <img class="d-block mx-auto screens-img" src="img/screenshot_ranking.jpg" alt="Pantalla de rankings de PodoVS">
                </div>
              </div>

              <div class="screen-indicator">
              </div>
            </div>

            <button class="screens-arrow next"
              type="button"
              data-bs-target="#screensCarousel"
              data-bs-slide="next"
              aria-label="Pantalla siguiente">
              <span aria-hidden="true">›</span>
            </button>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- CTA (Descarga) -->
  <section id="descargar" class="py-5">
    <div class="container">
      <div class="download-cta">
        <div class="row align-items-center g-4">
          <div class="col-lg-7">
            <span class="badge rounded-pill mb-3">Nuevo • Beta abierta</span>
            <h3 class="display-6 fw-bold mb-3">Descargá PodoVS ahora</h3>
            <p class="mb-4">Descargá PodoVS y unite a los eventos mensuales, rankings y recompensas diarias. Compatible con Android.</p>

            <div class="d-flex flex-wrap gap-2">
              <a href="#" class="btn-gplay">
                <img src="img/gplay-logo.png" alt="" aria-hidden="true">
                <span>Google Play (próximamente)</span>
              </a>
              <a href="https://www.dropbox.com/scl/fi/l5afq1my6lxpcwwi9g9lv/app-debug.apk?rlkey=fb4lpronbtwwxhiqmezq43803&st=pb4x9b1j&dl=1" class="btn-apk" target="_blank" rel="noopener">
                <img src="img/android-logo.png" alt="" aria-hidden="true">
                <span>Descargar APK (beta)</span>
              </a>
            </div>
          </div>

          <div class="col-lg-5">
            <div class="podo-logo-wrap">
              <img class="podo-logo" src="img/icon_podovs.png" alt="Ícono PodoVS">
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq" class="py-5">
    <div class="container">
      <div class="faq-outer">
        <div class="header">
          <h2 class="fw-bold h1 mb-1">Preguntas frecuentes</h2>
          <p class="subtitle">Para sacarte las dudas que te queden.</p>
        </div>

        <div class="accordion faq-accordion" id="faqAll">
          <div class="accordion-item">
            <h2 class="accordion-header" id="faq1h">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                ¿Cómo cuenta los pasos?
              </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAll">
              <div class="accordion-body">Usa los sensores del dispositivo (los mismos que usan Samsung y Google)</div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header" id="faq2h">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                ¿Se puede hacer trampa?
              </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAll">
              <div class="accordion-body">En un panel se puede ver si hay actividad sospechosa, entre más rápido se reporta mejor.</div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header" id="faq3h">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                ¿Funciona sin internet?
              </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAll">
              <div class="accordion-body">Se guardan los pasos hechos y al reconectarse se sincroniza todo.</div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header" id="faq4h">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                ¿Qué plataformas soporta?
              </button>
            </h2>
            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAll">
              <div class="accordion-body">Solo Android.</div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header" id="faq5h">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                ¿Cuánto consume de batería?
              </button>
            </h2>
            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAll">
              <div class="accordion-body">Muy poco, el conteo de pasos es lo único que se usa en segundo plano.</div>
            </div>
          </div>

          <div class="accordion-item">
            <h2 class="accordion-header" id="faq6h">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                ¿Habrá versión iOS?
              </button>
            </h2>
            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAll">
              <div class="accordion-body">Está en evaluación. La prioridad actual es Android y la web.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contacto -->
  <section id="contacto" class="py-5">
    <div class="container">
      <div class="contact-card rounded-4 p-4 p-lg-5 shadow-sm position-relative overflow-hidden">
        <div class="row g-4 align-items-center">
          <div class="col-lg-6">
            <h2 class="fw-bold display-6 mb-2">¿Tenés una pregunta?</h2>
            <p class="text-secondary mb-3">
              Escribinos y te respondemos a la brevedad. El mensaje se envía a <strong>consultas@podovs.com</strong>.
            </p>
            <ul class="list-unstyled small text-secondary mb-0">
              <li>• Tiempo de respuesta habitual: 24–48 hs.</li>
              <li>• Soporte de beta abierta y sugerencias de mejoras.</li>
              <li>• Reportes de bugs y feedback de usabilidad.</li>
            </ul>
          </div>

          <div class="col-lg-6">
            <form class="contact-form needs-validation"
              action="mailto:ezequiel.palleros@davinci.edu.ar?subject=Consulta%20desde%20la%20web%20PodoVS"
              method="post" enctype="text/plain" novalidate>
              <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off">

              <div class="mb-3">
                <label class="form-label fw-semibold">Nombre</label>
                <input type="text" name="Nombre" class="form-control form-control-lg" placeholder="Tu nombre" required>
                <div class="invalid-feedback">Ingresá tu nombre.</div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="Email" class="form-control form-control-lg" placeholder="tu@email.com" required>
                <div class="invalid-feedback">Ingresá un email válido.</div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Mensaje</label>
                <textarea name="Mensaje" class="form-control form-control-lg" rows="5" placeholder="Contanos en qué podemos ayudarte" required></textarea>
                <div class="invalid-feedback">Escribí tu consulta.</div>
              </div>

              <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <button class="btn btn-beta-cta px-4 py-3" type="submit">
                  <span class="shine" aria-hidden="true"></span>
                  Enviar consulta
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="contact-bubbles" aria-hidden="true"></div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/componentes/footer.php'; ?>