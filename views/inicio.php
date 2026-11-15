<?php $anio = date('Y'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de gestión integral para panaderías: ventas, producción, inventario y pedidos en un solo lugar.">
    <title>Panadería · Sistema de Gestión</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/inicio.css">
</head>
<body>
    <!-- ============================================
         HEADER
         ============================================ -->
    <header class="landing-header">
        <a class="landing-brand" href="index.php" aria-label="Panadería, inicio">
            <span class="brand-mark">🍞</span>
            <span class="brand-name">Panadería <b>Sistema de Gestión</b></span>
        </a>
        <nav class="landing-nav" aria-label="Navegación principal">
            <a href="#ventajas">Ventajas</a>
            <a href="#modulos">Módulos</a>
            <a href="#roles">Roles</a>
        </nav>
        <a class="login-link" href="views/login/login.php">
            Ingresar <span aria-hidden="true">↗</span>
        </a>
    </header>

    <main>
        <!-- ============================================
             HERO
             ============================================ -->
        <section class="hero-section">
            <div class="hero-copy">
                <p class="eyebrow"><span></span> Gestión hecha para el oficio</p>
                <h1>Gestión integral<br>para <em>panaderías.</em></h1>
                <p class="hero-text">Ventas, producción, inventario y pedidos en un solo sistema. Todo lo que pasa en tu panadería, en una sola pantalla.</p>
                
                <div class="hero-badges">
                    <span><i class="bi bi-cash"></i> POS</span>
                    <span><i class="bi bi-gear"></i> Producción</span>
                    <span><i class="bi bi-box"></i> Inventario</span>
                    <span><i class="bi bi-graph-up"></i> Reportes</span>
                </div>
                
                <div class="hero-actions">
                    <a class="button button-primary" href="views/login/login.php">
                        Ingresar al sistema <span>→</span>
                    </a>
                    <a class="text-link" href="#modulos">Ver módulos <span>↓</span></a>
                </div>
                
                <p class="hero-note"><span class="status-dot"></span> Disponible en PC, tablet y móvil</p>
            </div>

            <!-- Mockup del dashboard hecho en HTML/CSS -->
            <div class="hero-visual" aria-hidden="true">
                <div class="mockup-window">
                    <div class="mockup-bar">
                        <span class="dot red"></span>
                        <span class="dot yellow"></span>
                        <span class="dot green"></span>
                        <span class="mockup-url">panaderia.app/dashboard</span>
                    </div>
                    <div class="mockup-body">
                        <div class="mockup-sidebar">
                            <div class="mockup-logo">🍞</div>
                            <div class="mockup-menu">
                                <span class="active"></span>
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                        <div class="mockup-content">
                            <div class="mockup-title">Dashboard</div>
                            <div class="mockup-stats">
                                <div class="mockup-stat">
                                    <div class="mockup-stat-label">Ventas del día</div>
                                    <div class="mockup-stat-value">$184.500</div>
                                    <div class="mockup-stat-change up">↑ 12%</div>
                                </div>
                                <div class="mockup-stat">
                                    <div class="mockup-stat-label">Pedidos</div>
                                    <div class="mockup-stat-value">12</div>
                                    <div class="mockup-stat-change">pendientes</div>
                                </div>
                                <div class="mockup-stat">
                                    <div class="mockup-stat-label">Stock bajo</div>
                                    <div class="mockup-stat-value">3</div>
                                    <div class="mockup-stat-change down">⚠ revisar</div>
                                </div>
                            </div>
                            <div class="mockup-chart">
                                <div class="mockup-chart-title">Ventas de la semana</div>
                                <div class="mockup-bars">
                                    <span style="height: 40%"></span>
                                    <span style="height: 65%"></span>
                                    <span style="height: 50%"></span>
                                    <span style="height: 80%"></span>
                                    <span style="height: 95%"></span>
                                    <span style="height: 70%"></span>
                                    <span style="height: 55%"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================
             VENTAJAS
             ============================================ -->
        <section class="ventajas-section" id="ventajas">
            <div class="section-intro">
                <p class="eyebrow"><span></span> Por qué elegirlo</p>
                <h2>Ventajas para tu <em>panadería.</em></h2>
                <p class="section-sub">No es solo software: es ganar claridad operativa, control financiero y tranquilidad en el día a día.</p>
            </div>
            <div class="ventajas-grid">
                <article>
                    <span class="ventaja-icon">⏱️</span>
                    <h3>Más tiempo para hornear</h3>
                    <p>Menos horas en planillas. Inventario, cierres y seguimiento automatizados.</p>
                </article>
                <article>
                    <span class="ventaja-icon">📈</span>
                    <h3>Márgenes que podés explicar</h3>
                    <p>Recetas, ingredientes y precios conectados: ves el costo real y la rentabilidad por producto.</p>
                </article>
                <article>
                    <span class="ventaja-icon">🛡️</span>
                    <h3>Menos errores y pérdidas</h3>
                    <p>Pedidos, stock y movimientos con trazabilidad: menos faltantes, menos compras de más.</p>
                </article>
                <article>
                    <span class="ventaja-icon">📍</span>
                    <h3>Donde esté tu negocio, está la info</h3>
                    <p>Consultá y operá desde PC, tablet o móvil. La misma información para todos.</p>
                </article>
                <article>
                    <span class="ventaja-icon">🔗</span>
                    <h3>Todo integrado</h3>
                    <p>Ventas, producción, pedidos e inventario comparten la misma base. Se acabó el Excel paralelo.</p>
                </article>
                <article>
                    <span class="ventaja-icon">👥</span>
                    <h3>Un equipo ordenado</h3>
                    <p>Cada persona accede a lo que necesita según su rol, con menos fricción entre caja y producción.</p>
                </article>
            </div>
        </section>

        <!-- ============================================
             MÓDULOS
             ============================================ -->
        <section class="modulos-section" id="modulos">
            <div class="section-intro">
                <p class="eyebrow"><span></span> Todo lo que necesitás</p>
                <h2>Un sistema,<br><em>todos los módulos.</em></h2>
                <p class="section-sub">Cada parte de tu operación diaria, conectada.</p>
            </div>
            <div class="modulos-grid">
                <article>
                    <div class="modulo-icon">📊</div>
                    <h3>Dashboard</h3>
                    <p>Resumen del día, indicadores clave y accesos rápidos.</p>
                </article>
                <article>
                    <div class="modulo-icon">🍞</div>
                    <h3>Productos</h3>
                    <p>Catálogo con precios, stock, imágenes y categorías.</p>
                </article>
                <article>
                    <div class="modulo-icon">📦</div>
                    <h3>Ingredientes</h3>
                    <p>Materias primas con stock, costos y proveedores.</p>
                </article>
                <article>
                    <div class="modulo-icon">📋</div>
                    <h3>Recetas</h3>
                    <p>Fórmulas con ingredientes y cálculo automático de costos.</p>
                </article>
                <article>
                    <div class="modulo-icon">🏭</div>
                    <h3>Producción</h3>
                    <p>Órdenes con descuento automático de stock y registro de mermas.</p>
                </article>
                <article>
                    <div class="modulo-icon">💰</div>
                    <h3>Ventas / POS</h3>
                    <p>Cobro en mostrador con caja y medios de pago.</p>
                </article>
                <article>
                    <div class="modulo-icon">📝</div>
                    <h3>Pedidos</h3>
                    <p>Pedidos con estados, fechas de entrega y señas.</p>
                </article>
                <article>
                    <div class="modulo-icon">🏢</div>
                    <h3>Compras</h3>
                    <p>Órdenes a proveedores con recepción y actualización de precios.</p>
                </article>
                <article>
                    <div class="modulo-icon">📊</div>
                    <h3>Reportes</h3>
                    <p>Ventas, inventario y rentabilidad para decidir con datos.</p>
                </article>
            </div>
        </section>

        <!-- ============================================
             ROLES
             ============================================ -->
        <section class="roles-section" id="roles">
            <div class="section-intro">
                <p class="eyebrow"><span></span> Cada uno en lo suyo</p>
                <h2>Cada rol,<br><em>su pantalla.</em></h2>
                <p class="section-sub">Cada persona ve exactamente lo que necesita según su función.</p>
            </div>
            <div class="roles-grid">
                <article>
                    <div class="rol-badge">Admin</div>
                    <h3>Control total</h3>
                    <p>Acceso completo al sistema: usuarios, configuración, reportes y todos los módulos.</p>
                </article>
                <article>
                    <div class="rol-badge">Encargado</div>
                    <h3>Operación diaria</h3>
                    <p>Productos, inventario, compras, pedidos, clientes, caja y reportes operativos.</p>
                </article>
                <article>
                    <div class="rol-badge">Vendedor</div>
                    <h3>Mostrador y caja</h3>
                    <p>Ventas, pedidos, clientes y apertura/cierre de caja.</p>
                </article>
                <article>
                    <div class="rol-badge">Panadero</div>
                    <h3>Recetas y producción</h3>
                    <p>Recetas, ingredientes, órdenes de producción y registro de mermas.</p>
                </article>
                <article>
                    <div class="rol-badge">Producción</div>
                    <h3>Elaboración</h3>
                    <p>Consulta de recetas, ejecución de órdenes y consumo de ingredientes.</p>
                </article>
            </div>
        </section>

        <!-- ============================================
             CTA FINAL
             ============================================ -->
        <section class="cta-section">
            <p class="eyebrow"><span></span> Empezá hoy</p>
            <h2>¿Listo para ordenar<br><em>tu panadería?</em></h2>
            <a class="button button-dark" href="views/login/login.php">
                Ingresar al sistema <span>↗</span>
            </a>
        </section>
    </main>

    <!-- ============================================
         FOOTER
         ============================================ -->
    <footer class="landing-footer">
        <span>Panadería · Sistema de Gestión</span>
        <span>Gestión integral para panaderías</span>
        <span>&copy; <?= $anio ?></span>
    </footer>

    <!-- ============================================
         BURBUJA DE WHATSAPP
         ============================================ -->
    <div class="whatsapp-bubble" id="whatsappBubble">
        <div class="whatsapp-message" id="whatsappMessage">
            <button class="whatsapp-close" id="whatsappClose" aria-label="Cerrar">×</button>
            <p><strong>¿Querés probar el sistema?</strong></p>
            <p>Pedí tu usuario para gestionar tu panadería.</p>
            <a href="https://wa.me/543537515522?text=Hola%20Benjam%C3%ADn%2C%20estoy%20interesado%20en%20el%20sistema%20de%20gesti%C3%B3n%20de%20la%20panader%C3%ADa" 
               target="_blank" 
               rel="noopener"
               class="whatsapp-cta">
                <i class="bi bi-whatsapp"></i> Escribir por WhatsApp
            </a>
        </div>
        <button class="whatsapp-button" id="whatsappButton" aria-label="Contactar por WhatsApp">
            <i class="bi bi-whatsapp"></i>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bubble = document.getElementById('whatsappBubble');
            const message = document.getElementById('whatsappMessage');
            const button = document.getElementById('whatsappButton');
            const close = document.getElementById('whatsappClose');

            // Mostrar el mensaje después de 3 segundos
            setTimeout(function() {
                bubble.classList.add('visible');
            }, 3000);

            // Toggle del mensaje al hacer clic en el botón
            button.addEventListener('click', function() {
                message.classList.toggle('open');
            });

            // Cerrar el mensaje
            close.addEventListener('click', function(e) {
                e.stopPropagation();
                message.classList.remove('open');
            });
        });
    </script>
</body>
</html>