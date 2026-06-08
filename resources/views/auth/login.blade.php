<?php $titulo = 'Iniciar sesión - SIGIE'; ?>

<style>
    /* ===== SIGIE - SISTEMA DE ADMISIÓN ESTUDIANTIL - FICCT UAGRM ===== */
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap');

    *, *::before, *::after {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --navy:       #0d1f35;
        --navy-mid:   #162d47;
        --blue:       #1a5f9c;
        --blue-light: #2e86d4;
        --gold:       #c89b3c;
        --gold-light: #e8c06a;
        --cream:      #f8f5f0;
        --white:      #ffffff;
        --muted:      #6b82a0;
        --border:     #dce6f0;
        --danger:     #c0392b;
    }

    body {
        font-family: 'DM Sans', system-ui, sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: stretch;
        background-color: var(--navy);
        overflow: hidden;
    }

    /* ── Panel izquierdo: fondo campus ── */
    .side-bg {
        flex: 1;
        position: relative;
        display: none;
        background:
            linear-gradient(160deg, rgba(13,31,53,0.82) 0%, rgba(26,95,156,0.55) 60%, rgba(200,155,60,0.25) 100%),
            url('https://cup.ficct.uagrm.edu.bo/pluginfile.php/1/local_edwiserpagebuilder/media/554450943/Modulo236-FICCT-VF_1000.jpg')
            center/cover no-repeat;
        overflow: hidden;
    }

    @media (min-width: 900px) { .side-bg { display: flex; flex-direction: column; justify-content: space-between; padding: 3rem; } }

    .side-bg::after {
        content: '';
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(
            0deg,
            transparent,
            transparent 3px,
            rgba(255,255,255,0.012) 3px,
            rgba(255,255,255,0.012) 4px
        );
        pointer-events: none;
    }

    .side-brand {
        position: relative;
        z-index: 1;
        animation: fadeLeft 0.7s ease-out both;
    }

    .side-brand .institution-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(200,155,60,0.4);
        border-radius: 100px;
        padding: 0.35rem 0.85rem;
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--gold-light);
        letter-spacing: 0.08em;
        text-transform: uppercase;
        backdrop-filter: blur(8px);
        margin-bottom: 1.5rem;
    }

    .side-brand h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(2.4rem, 4vw, 3.2rem);
        font-weight: 800;
        color: var(--white);
        line-height: 1.1;
        margin-bottom: 1rem;
    }

    .side-brand h1 span {
        color: var(--gold-light);
    }

    .side-brand p {
        color: rgba(255,255,255,0.72);
        font-size: 0.95rem;
        font-weight: 300;
        max-width: 340px;
        line-height: 1.65;
    }

    .side-footer {
        position: relative;
        z-index: 1;
        animation: fadeLeft 0.7s 0.2s ease-out both;
    }

    .side-footer .stat-row {
        display: flex;
        gap: 2rem;
    }

    .side-footer .stat { }

    .side-footer .stat-num {
        font-family: 'Playfair Display', serif;
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--gold-light);
        line-height: 1;
    }

    .side-footer .stat-label {
        font-size: 0.72rem;
        color: rgba(255,255,255,0.55);
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-top: 0.2rem;
    }

    /* Línea decorativa dorada */
    .gold-rule {
        width: 48px;
        height: 3px;
        background: linear-gradient(90deg, var(--gold), var(--gold-light));
        border-radius: 2px;
        margin: 1.25rem 0;
    }

    /* ── Panel derecho: formulario ── */
    .login-panel {
        width: 100%;
        max-width: 480px;
        min-height: 100vh;
        background: var(--cream);
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 3rem 2.5rem;
        position: relative;
        overflow: hidden;
    }

    @media (min-width: 900px) { .login-panel { border-left: 1px solid rgba(200,155,60,0.2); } }

    /* Detalle geométrico de fondo */
    .login-panel::before {
        content: '';
        position: absolute;
        bottom: -80px;
        right: -80px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(26,95,156,0.06) 0%, transparent 70%);
        pointer-events: none;
    }

    .login-panel::after {
        content: '';
        position: absolute;
        top: -60px;
        left: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(200,155,60,0.07) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Header del panel */
    .panel-header {
        margin-bottom: 2.25rem;
        animation: fadeUp 0.6s 0.1s ease-out both;
        position: relative;
        z-index: 1;
    }

    .logo-wrap {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .logo-icon {
        width: 52px;
        height: 52px;
        border-radius: 10px;
        flex-shrink: 0;
        overflow: hidden;
        background: var(--white);
        box-shadow: 0 4px 12px rgba(13,31,53,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logo-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 4px;
    }

    .logo-text-wrap { }

    .logo-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--navy);
        line-height: 1.1;
    }

    .logo-sub {
        font-size: 0.7rem;
        font-weight: 500;
        color: var(--muted);
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-top: 1px;
    }

    .panel-header h2 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1.55rem, 3vw, 1.85rem);
        font-weight: 700;
        color: var(--navy);
        line-height: 1.2;
        margin-bottom: 0.4rem;
    }

    .panel-header p {
        font-size: 0.85rem;
        color: var(--muted);
        font-weight: 400;
    }

    /* Formulario */
    .login-form {
        animation: fadeUp 0.6s 0.2s ease-out both;
        position: relative;
        z-index: 1;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        font-size: 0.8rem;
        color: var(--navy-mid);
        margin-bottom: 0.45rem;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .form-label.required::after {
        content: ' *';
        color: var(--danger);
    }

    .input-wrap {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 0.9rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.95rem;
        pointer-events: none;
        opacity: 0.5;
    }

    .form-control {
        width: 100%;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        padding: 0.78rem 1rem 0.78rem 2.5rem;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        color: var(--navy);
        background: var(--white);
        transition: border-color 0.2s, box-shadow 0.2s;
        -webkit-appearance: none;
    }

    .form-control:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(26,95,156,0.12);
        outline: none;
    }

    .form-control::placeholder {
        color: #b2bfcc;
        font-size: 0.85rem;
        font-weight: 300;
    }

    /* Botón */
    .btn-submit {
        width: 100%;
        border: none;
        border-radius: 12px;
        padding: 0.88rem;
        font-family: 'DM Sans', sans-serif;
        font-weight: 600;
        font-size: 0.92rem;
        cursor: pointer;
        letter-spacing: 0.03em;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
        color: var(--white);
        margin-top: 0.75rem;
        transition: transform 0.18s, box-shadow 0.18s;
        box-shadow: 0 4px 16px rgba(13,31,53,0.25);
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(200,155,60,0.18) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.2s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(13,31,53,0.32);
    }

    .btn-submit:hover::before { opacity: 1; }

    .btn-submit:active {
        transform: translateY(0);
        box-shadow: 0 3px 10px rgba(13,31,53,0.2);
    }

    .btn-inner {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* Divider dorado */
    .gold-accent-line {
        height: 2px;
        background: linear-gradient(90deg, var(--gold), var(--gold-light), transparent);
        border-radius: 2px;
        margin: 1.75rem 0;
        opacity: 0.5;
    }

    /* Footer del panel */
    .panel-footer {
        margin-top: 1.5rem;
        text-align: center;
        animation: fadeUp 0.6s 0.3s ease-out both;
        position: relative;
        z-index: 1;
    }

    .panel-footer p {
        font-size: 0.75rem;
        color: var(--muted);
        line-height: 1.6;
    }

    .panel-footer strong {
        color: var(--navy-mid);
        font-weight: 600;
    }

    /* Animaciones */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeLeft {
        from { opacity: 0; transform: translateX(-16px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    /* Responsive */
    @media (max-width: 480px) {
        .login-panel { padding: 2rem 1.5rem; min-height: 100dvh; }
    }

    @media (hover: none) and (pointer: coarse) {
        .btn-submit:active { transform: scale(0.97); }
    }
</style>

<div style="display:flex; min-height:100vh;">

    <!-- Panel izquierdo (solo escritorio) -->
    <div class="side-bg">
        <div class="side-brand">
            <div class="institution-badge">
                <img src="https://www.ficct.uagrm.edu.bo:3000/uploads/faculty/Escudo_FICCT.png" alt="FICCT" style="width:16px;height:16px;object-fit:contain;"> UAGRM · FICCT
            </div>
            <h1>Sistema de Informacion para la <br>Gestion de Admision Universitaria al <span>Curso Preuniversitario</span></h1>
            <div class="gold-rule"></div>
            <p>Plataforma oficial de gestión e ingreso Académico y CUP.</p>
        </div>
        <div class="side-footer">
            <div class="stat-row">
                <div class="stat">
                    <div class="stat-num">UAGRM</div>
                    <div class="stat-label">Universidad</div>
                </div>
                <div class="stat">
                    <div class="stat-num">FICCT</div>
                    <div class="stat-label">Facultad</div>
                </div>
                <div class="stat">
                    <div class="stat-num">SIGAUCP</div>
                    <div class="stat-label">Sistema</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Panel derecho: Login -->
    <div class="login-panel">

        <div class="panel-header">
            <div class="logo-wrap">
                <div class="logo-icon">
                    <img src="https://www.ficct.uagrm.edu.bo:3000/uploads/faculty/Escudo_FICCT.png" alt="Escudo FICCT">
                </div>
                <div class="logo-text-wrap">
                    <div class="logo-title">SIGAUCP</div>
                    <div class="logo-sub">UAGRM · FICCT</div>
                </div>
            </div>
            <h2>Bienvenido</h2>
            <p>Ingrese sus credenciales para acceder al sistema.</p>
        </div>

        <form action="<?= e(url('/login')) ?>" method="POST" class="login-form">

            <div class="form-group">
                <label for="username" class="form-label required">Usuario</label>
                <div class="input-wrap">
                    <span class="input-icon">👤</span>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-control"
                        value="<?= e(old('username')) ?>"
                        placeholder="Ingrese su usuario"
                        required
                        autofocus
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label required">Contraseña</label>
                <div class="input-wrap">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Ingrese su contraseña"
                        required
                    >
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <span class="btn-inner">
                    <span>Iniciar sesión</span>
                    <span>→</span>
                </span>
            </button>

        </form>

        <div class="gold-accent-line"></div>

        <div class="panel-footer">
            <p>
                <strong> </strong><br>
                FICCT · UAGRM<br>
            </p>
        </div>

    </div><!-- /login-panel -->

</div><!-- /flex wrapper -->