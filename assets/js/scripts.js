// ============================================
// SIDEBAR TOGGLE PARA MÓVIL
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('active');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }

    // Cerrar sidebar al hacer clic en un enlace (móvil)
    document.querySelectorAll('.sidebar-menu .nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 992) {
                sidebar.classList.remove('open');
                if (overlay) overlay.classList.remove('active');
            }
        });
    });

    // Marcar enlace activo en sidebar
    const currentUrl = window.location.pathname + window.location.search;
    document.querySelectorAll('.sidebar-menu .nav-link').forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentUrl.includes(href.split('?')[0])) {
            link.classList.add('active');
        }
    });

    // Auto-cerrar alertas
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });

    console.log('🍞 Panadería - Sistema de Gestión v2.0');
    console.log('✅ Sistema cargado correctamente');
});