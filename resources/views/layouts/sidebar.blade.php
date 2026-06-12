<?php
$usuarioSesion = $_SESSION['usuario'] ?? [];
$rutaActual = current_path();
$rolesSesion = $usuarioSesion['roles'] ?? [];


$esAdmin = in_array('Administrador', $rolesSesion, true);
$esAdmision = $esAdmin || in_array('Encargado de admisión', $rolesSesion, true);
$esFinanciero = $esAdmin || in_array('Encargado financiero', $rolesSesion, true);
$esDocente = $esAdmin || in_array('Docente', $rolesSesion, true);
?>


<style>
    /* ===== SIGIE - SIDEBAR - SISTEMA DE ADMISIÓN ESTUDIANTIL ===== */
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap');


    :root {
        --navy:        #0d1f35;
        --navy-mid:    #162d47;
        --navy-light:  #1e3a5f;
        --blue:        #1a5f9c;
        --blue-hover:  #1e6db0;
        --gold:        #c89b3c;
        --gold-light:  #e8c06a;
        --sidebar-w:   260px;
        --text-dim:    rgba(255,255,255,0.45);
        --text-mid:    rgba(255,255,255,0.70);
        --text-full:   rgba(255,255,255,0.95);
        --border-sub:  rgba(255,255,255,0.07);
        --active-bg:   rgba(26,95,156,0.55);
        --active-glow: rgba(26,95,156,0.30);
        --hover-bg:    rgba(255,255,255,0.06);
        --topbar-h:    58px;
    }


    .sigie-topbar {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0;
        height: var(--topbar-h);
        background: var(--navy);
        border-bottom: 1px solid var(--border-sub);
        z-index: 1100;
        align-items: center;
        justify-content: space-between;
        padding: 0 1rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.3);
    }


    @media (max-width: 900px) { .sigie-topbar { display: flex; } }


    .sigie-topbar .tb-brand {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }


    .sigie-topbar .tb-logo {
        width: 30px;
        height: 30px;
        object-fit: contain;
    }


    .sigie-topbar .tb-name {
        font-family: 'Playfair Display', serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-full);
    }


    .sigie-topbar .tb-sub {
        font-family: 'DM Sans', sans-serif;
        font-size: 0.65rem;
        color: var(--text-dim);
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }


    .burger-btn {
        background: rgba(255,255,255,0.08);
        border: 1px solid var(--border-sub);
        border-radius: 9px;
        width: 38px;
        height: 38px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        cursor: pointer;
        transition: background 0.2s;
        padding: 0;
    }


    .burger-btn:hover { background: rgba(255,255,255,0.14); }


    .burger-btn span {
        display: block;
        width: 18px;
        height: 2px;
        background: var(--text-full);
        border-radius: 2px;
        transition: transform 0.25s, opacity 0.25s;
    }


    .burger-btn.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
    .burger-btn.open span:nth-child(2) { opacity: 0; }
    .burger-btn.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }


    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.55);
        z-index: 1110;
        opacity: 0;
        transition: opacity 0.25s;
    }


    .sidebar-overlay.visible {
        display: block;
        opacity: 1;
    }


    .sigie-sidebar {
        font-family: 'DM Sans', system-ui, sans-serif;
        width: var(--sidebar-w);
        background: var(--navy);
        position: fixed;
        left: 0;
        top: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
        z-index: 1120;
        transition: transform 0.28s cubic-bezier(.4,0,.2,1);
        border-right: 1px solid var(--border-sub);
        box-shadow: 4px 0 24px rgba(0,0,0,0.25);
        overflow: hidden;
    }


    .sigie-sidebar::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse at top left, rgba(26,95,156,0.12) 0%, transparent 55%),
            radial-gradient(ellipse at bottom right, rgba(200,155,60,0.06) 0%, transparent 50%);
        pointer-events: none;
    }


    @media (max-width: 900px) {
        .sigie-sidebar { transform: translateX(-100%); top: 0; }
        .sigie-sidebar.mobile-open { transform: translateX(0); }
    }


    .sigie-sidebar.collapsed { transform: translateX(-100%); }


    .sb-brand {
        padding: 1.4rem 1.25rem 1.1rem;
        border-bottom: 1px solid var(--border-sub);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }


    .sb-logo {
        width: 40px;
        height: 40px;
        object-fit: contain;
        flex-shrink: 0;
        filter: drop-shadow(0 2px 6px rgba(0,0,0,0.4));
    }


    .sb-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-full);
        line-height: 1.1;
    }


    .sb-subtitle {
        font-size: 0.67rem;
        color: var(--gold-light);
        letter-spacing: 0.06em;
        text-transform: uppercase;
        font-weight: 500;
        margin-top: 2px;
        opacity: 0.85;
    }


    .gold-line {
        height: 2px;
        background: linear-gradient(90deg, var(--gold), var(--gold-light), transparent);
        flex-shrink: 0;
        opacity: 0.6;
    }


    .sb-user {
        margin: 1rem 1rem 0.75rem;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--border-sub);
        border-radius: 12px;
        padding: 0.85rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }


    .sb-avatar {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, var(--blue), var(--navy-light));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 700;
        color: white;
        flex-shrink: 0;
        border: 1px solid rgba(255,255,255,0.12);
    }


    .sb-user-name {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-full);
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 148px;
    }


    .sb-user-username {
        font-size: 0.7rem;
        color: var(--text-dim);
        margin-top: 1px;
    }


    .sb-role-badge {
        display: inline-block;
        margin-top: 0.4rem;
        background: rgba(200,155,60,0.15);
        border: 1px solid rgba(200,155,60,0.3);
        color: var(--gold-light);
        font-size: 0.62rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 0.18rem 0.55rem;
        border-radius: 100px;
    }


    .sb-nav {
        flex: 1;
        overflow-y: auto;
        padding: 0.5rem 0.75rem 1rem;
        position: relative;
        z-index: 1;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.1) transparent;
    }


    .sb-nav::-webkit-scrollbar { width: 4px; }
    .sb-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }


    .sb-section {
        font-size: 0.65rem;
        font-weight: 700;
        color: var(--text-dim);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 0.9rem 0.5rem 0.4rem;
    }


    .sb-link {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.6rem 0.85rem;
        border-radius: 10px;
        color: var(--text-mid);
        text-decoration: none;
        font-size: 0.84rem;
        font-weight: 500;
        transition: background 0.18s, color 0.18s;
        margin-bottom: 2px;
        position: relative;
    }


    .sb-link:hover {
        background: var(--hover-bg);
        color: var(--text-full);
    }


    .sb-link.active {
        background: var(--active-bg);
        color: var(--text-full);
        font-weight: 600;
        box-shadow: 0 0 0 1px rgba(26,95,156,0.4), 0 4px 12px var(--active-glow);
    }


    .sb-link.active::before {
        content: '';
        position: absolute;
        left: 0; top: 20%; bottom: 20%;
        width: 3px;
        background: var(--gold-light);
        border-radius: 0 3px 3px 0;
    }


    .sb-icon {
        font-size: 1rem;
        width: 20px;
        text-align: center;
        flex-shrink: 0;
    }


    .sb-logout {
        padding: 0.75rem;
        border-top: 1px solid var(--border-sub);
        flex-shrink: 0;
        position: relative;
        z-index: 1;
    }


    .sb-logout a {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.65rem 0.85rem;
        border-radius: 10px;
        color: rgba(248,113,113,0.75);
        text-decoration: none;
        font-size: 0.84rem;
        font-weight: 500;
        transition: background 0.18s, color 0.18s;
        border: 1px solid transparent;
    }


    .sb-logout a:hover {
        background: rgba(185,28,28,0.15);
        color: #fca5a5;
        border-color: rgba(185,28,28,0.2);
    }


    .main-content {
        margin-left: var(--sidebar-w);
        transition: margin-left 0.28s cubic-bezier(.4,0,.2,1);
    }


    .main-content.sidebar-collapsed {
        margin-left: 0;
    }


    @media (max-width: 900px) {
        .main-content {
            margin-left: 0;
            padding-top: var(--topbar-h);
        }
    }


    .sb-collapse-btn {
        display: none;
        position: fixed;
        top: 50%;
        left: var(--sidebar-w);
        transform: translateY(-50%) translateX(-50%);
        z-index: 1070;
        width: 24px;
        height: 48px;
        background: var(--navy-mid);
        border: 1px solid var(--border-sub);
        border-radius: 0 8px 8px 0;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        color: var(--text-mid);
        font-size: 0.7rem;
        transition: left 0.28s cubic-bezier(.4,0,.2,1), background 0.2s;
    }


    .sb-collapse-btn:hover {
        background: var(--navy-light);
        color: var(--text-full);
    }


    @media (min-width: 901px) {
        .sb-collapse-btn { display: flex; }
    }


    .sb-collapse-btn.sidebar-collapsed {
        left: 0;
        border-radius: 0 8px 8px 0;
    }


    @media (hover: none) and (pointer: coarse) {
        .sb-link:active { background: var(--hover-bg); }
    }
</style>


<div class="sigie-topbar">
    <div class="tb-brand">
        <img src="https://www.ficct.uagrm.edu.bo:3000/uploads/faculty/Escudo_FICCT.png"
             alt="FICCT" class="tb-logo"
             onerror="this.style.display='none'">
        <div>
            <div class="tb-name">SIGIE</div>
            <div class="tb-sub">FICCT · UAGRM</div>
        </div>
    </div>


    <button class="burger-btn" id="burgerBtn" aria-label="Menú">
        <span></span>
        <span></span>
        <span></span>
    </button>
</div>


<div class="sidebar-overlay" id="sidebarOverlay"></div>


<aside class="sigie-sidebar" id="sigieSidebar">


    <div class="sb-brand">
        <img src="https://www.ficct.uagrm.edu.bo:3000/uploads/faculty/Escudo_FICCT.png"
             alt="FICCT" class="sb-logo"
             onerror="this.style.display='none'">


        <div class="sb-brand-text">
            <div class="sb-title">CUP</div>
            <div class="sb-subtitle">FICCT · UAGRM</div>
        </div>
    </div>


    <div class="gold-line"></div>


    <div class="sb-user">
        <div class="sb-avatar" id="sb-initials">U</div>


        <div class="sb-user-info">
            <div class="sb-user-name"><?= e($usuarioSesion['nombre_completo'] ?? 'Usuario') ?></div>
            <div class="sb-user-username"><?= e($usuarioSesion['username'] ?? '') ?></div>


            <?php if (!empty($rolesSesion)): ?>
                <div class="sb-role-badge"><?= e(implode(', ', $rolesSesion)) ?></div>
            <?php else: ?>
                <div class="sb-role-badge">Sin rol</div>
            <?php endif; ?>
        </div>
    </div>


    <nav class="sb-nav">


        <div class="sb-section">Principal</div>


        <a href="<?= e(url('/dashboard')) ?>"
           class="sb-link <?= $rutaActual === '/dashboard' ? 'active' : '' ?>">
            <span class="sb-icon">📘</span> Dashboard
        </a>


        <?php if ($esAdmin): ?>
            <div class="sb-section">Seguridad</div>


            <a href="<?= e(url('/usuarios')) ?>"
               class="sb-link <?= str_starts_with($rutaActual, '/usuarios') ? 'active' : '' ?>">
                <span class="sb-icon">👤</span> Usuarios
            </a>


            <a href="<?= e(url('/roles')) ?>"
               class="sb-link <?= str_starts_with($rutaActual, '/roles') ? 'active' : '' ?>">
                <span class="sb-icon">🔐</span> Roles
            </a>
        <?php endif; ?>


        <?php if ($esAdmision): ?>
            <div class="sb-section">Admisión</div>


            <a href="<?= e(url('/carreras')) ?>"
               class="sb-link <?= str_starts_with($rutaActual, '/carreras') && $rutaActual !== '/carreras/cupos' ? 'active' : '' ?>">
                <span class="sb-icon">🎓</span> Carreras
            </a>


            <a href="<?= e(url('/carreras/cupos')) ?>"
               class="sb-link <?= $rutaActual === '/carreras/cupos' ? 'active' : '' ?>">
                <span class="sb-icon">📌</span> Cupos por carrera
            </a>


            <a href="<?= e(url('/postulantes')) ?>"
               class="sb-link <?= str_starts_with($rutaActual, '/postulantes') ? 'active' : '' ?>">
                <span class="sb-icon">🧾</span> Postulantes
            </a>


            <a href="<?= e(url('/documentos')) ?>"
               class="sb-link <?= str_starts_with($rutaActual, '/documentos') ? 'active' : '' ?>">
                <span class="sb-icon">📄</span> Requisitos
            </a>


            <a href="<?= e(url('/grupos-academicos')) ?>"
               class="sb-link <?= str_starts_with($rutaActual, '/grupos-academicos') ? 'active' : '' ?>">
                <span class="sb-icon">🏫</span> Grupos académicos
            </a>


            <a href="<?= e(url('/inscripciones')) ?>"
               class="sb-link <?= str_starts_with($rutaActual, '/inscripciones') ? 'active' : '' ?>">
                <span class="sb-icon">✅</span> Inscripciones
            </a>
        <?php endif; ?>


        <?php if ($esFinanciero): ?>
            <div class="sb-section">Finanzas</div>


            <a href="<?= e(url('/pagos')) ?>"
               class="sb-link <?= str_starts_with($rutaActual, '/pagos') ? 'active' : '' ?>">
                <span class="sb-icon">💰</span> Pagos
            </a>
        <?php endif; ?>


        <?php if ($esAdmin || $esAdmision || $esDocente): ?>
            <div class="sb-section">Académico</div>


            <?php if ($esAdmin || $esAdmision): ?>
                <a href="<?= e(url('/docentes')) ?>"
                   class="sb-link <?= str_starts_with($rutaActual, '/docentes') ? 'active' : '' ?>">
                    <span class="sb-icon">👨‍🏫</span> Docentes
                </a>


                <a href="<?= e(url('/planificacion-horaria')) ?>"
                   class="sb-link <?= str_starts_with($rutaActual, '/planificacion-horaria') ? 'active' : '' ?>">
                    <span class="sb-icon">📅</span> Planificación horaria
                </a>


                <a href="<?= e(url('/conflictos-horarios')) ?>"
                   class="sb-link <?= str_starts_with($rutaActual, '/conflictos-horarios') ? 'active' : '' ?>">
                    <span class="sb-icon">⚠️</span> Conflictos de horario
                </a>
            <?php endif; ?>


            <!-- ========================================== -->
            <!-- BOTONES AGREGADOS: NOTAS, EXÁMENES Y ASISTENCIA -->
            <!-- ========================================== -->


            <!-- NOTAS (Asentar calificaciones) -->
            <?php if ($esDocente): ?>
                <a href="<?= e(url('/notas')) ?>"
                   class="sb-link <?= str_starts_with($rutaActual, '/notas') ? 'active' : '' ?>">
                    <span class="sb-icon">📝</span> Notas
                </a>
            <?php endif; ?>


            <!-- EXÁMENES (Rendir examen / Asentar notas manuales) -->
            <?php if ($esAdmin || $esAdmision || $esDocente): ?>
                <a href="<?= e(url('/examenes')) ?>"
                   class="sb-link <?= str_starts_with($rutaActual, '/examenes') ? 'active' : '' ?>">
                    <span class="sb-icon">📋</span> Exámenes
                </a>
            <?php endif; ?>


            <!-- ASISTENCIA (Registro de asistencia) -->
            <?php if ($esDocente): ?>
                <a href="<?= e(url('/asistencia')) ?>"
                   class="sb-link <?= str_starts_with($rutaActual, '/asistencia') ? 'active' : '' ?>">
                    <span class="sb-icon">📊</span> Asistencia
                </a>
            <?php endif; ?>


            <a href="<?= e(url('/resultados')) ?>"
               class="sb-link <?= str_starts_with($rutaActual, '/resultados') ? 'active' : '' ?>">
                <span class="sb-icon">🏆</span> Resultados finales
            </a>
        <?php endif; ?>


    </nav>


    <div class="sb-logout">
        <a href="<?= e(url('/logout')) ?>" onclick="return confirm('¿Deseas cerrar sesión?')">
            <span class="sb-icon">🚪</span> Cerrar sesión
        </a>
    </div>


</aside>


<button class="sb-collapse-btn" id="collapseBtn" aria-label="Colapsar menú">›</button>


<script>
(function () {
    var sidebar  = document.getElementById('sigieSidebar');
    var overlay  = document.getElementById('sidebarOverlay');
    var burger   = document.getElementById('burgerBtn');
    var collapseBtn = document.getElementById('collapseBtn');
    var mainContent = document.querySelector('.main-content');
    var isMobile = function () { return window.innerWidth <= 900; };


    var nameEl = document.querySelector('.sb-user-name');
    var initEl = document.getElementById('sb-initials');


    if (nameEl && initEl) {
        var parts = nameEl.textContent.trim().split(' ').filter(Boolean);
        initEl.textContent = ((parts[0] ? parts[0][0] : '') + (parts[1] ? parts[1][0] : '')).toUpperCase() || 'U';
    }


    function openMobile() {
        sidebar.classList.add('mobile-open');
        overlay.classList.add('visible');
        burger.classList.add('open');
        document.body.style.overflow = 'hidden';
    }


    function closeMobile() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('visible');
        burger.classList.remove('open');
        document.body.style.overflow = '';
    }


    if (burger) {
        burger.addEventListener('click', function () {
            sidebar.classList.contains('mobile-open') ? closeMobile() : openMobile();
        });
    }


    if (overlay) {
        overlay.addEventListener('click', closeMobile);
    }


    var collapsed = false;


    if (collapseBtn) {
        collapseBtn.addEventListener('click', function () {
            collapsed = !collapsed;


            sidebar.classList.toggle('collapsed', collapsed);
            collapseBtn.classList.toggle('sidebar-collapsed', collapsed);
            collapseBtn.textContent = collapsed ? '‹' : '›';


            if (mainContent) {
                mainContent.classList.toggle('sidebar-collapsed', collapsed);
            }
        });
    }


    window.addEventListener('resize', function () {
        if (!isMobile()) {
            closeMobile();
            document.body.style.overflow = '';
        }
    });
})();
</script>