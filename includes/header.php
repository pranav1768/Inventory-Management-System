<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'IMS – Inventory Management System') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ── DESIGN TOKENS ─────────────────────────────────────── */
        :root {
            --color-navy:        #0F2044;
            --color-navy-mid:    #1C3461;
            --color-navy-light:  #2E4D8A;
            --color-accent:      #2563EB;
            --color-accent-light:#3B82F6;
            --color-accent-pale: #EFF6FF;
            --color-bg:          #F1F5F9;
            --color-surface:     #FFFFFF;
            --color-border:      #E2E8F0;
            --color-border-mid:  #CBD5E1;
            --color-text-primary:#0F172A;
            --color-text-body:   #334155;
            --color-text-muted:  #64748B;
            --color-text-subtle: #94A3B8;
            --color-success:     #16A34A;
            --color-success-bg:  #F0FDF4;
            --color-success-border:#BBF7D0;
            --color-warning:     #D97706;
            --color-warning-bg:  #FFFBEB;
            --color-warning-border:#FDE68A;
            --color-danger:      #DC2626;
            --color-danger-bg:   #FEF2F2;
            --color-danger-border:#FECACA;
            --color-info:        #0284C7;
            --color-info-bg:     #F0F9FF;
            --color-info-border: #BAE6FD;
            --sidebar-width:     260px;
            --topbar-height:     64px;
            --border-radius-sm:  6px;
            --border-radius-md:  10px;
            --border-radius-lg:  14px;
            --shadow-xs:   0 1px 2px rgba(0,0,0,0.05);
            --shadow-sm:   0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md:   0 4px 6px rgba(0,0,0,0.05), 0 2px 4px rgba(0,0,0,0.04);
            --shadow-lg:   0 10px 15px rgba(0,0,0,0.06), 0 4px 6px rgba(0,0,0,0.04);
            --shadow-xl:   0 20px 25px rgba(0,0,0,0.08), 0 10px 10px rgba(0,0,0,0.04);
            --font-body:   'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-mono:   'DM Mono', 'Fira Code', monospace;
            --transition-fast: 150ms ease;
            --transition-base: 220ms ease;
            --transition-slow: 350ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ── RESET ─────────────────────────────────────────────── */
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body {
            font-family: var(--font-body);
            background: var(--color-bg);
            color: var(--color-text-body);
            font-size: 14px;
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ── TOPBAR ─────────────────────────────────────────────── */
        .topbar {
            position: fixed; top:0; left:0; right:0;
            height: var(--topbar-height);
            background: var(--color-navy);
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex; align-items: center; padding: 0 20px;
            z-index: 1000;
            box-shadow: 0 2px 16px rgba(0,0,0,0.25);
        }

        .topbar-brand {
            display: flex; align-items: center; gap: 12px;
            width: var(--sidebar-width); flex-shrink: 0;
            text-decoration: none;
        }

        /* Tiger emblem container */
        .brand-emblem {
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; filter: drop-shadow(0 1px 4px rgba(0,0,0,0.4));
        }

        .brand-text { display: flex; flex-direction: column; }
        .brand-name {
            font-size: 14px; font-weight: 700; color: #FFFFFF;
            letter-spacing: -0.01em; line-height: 1;
        }
        .brand-sub {
            font-size: 10px; color: rgba(255,255,255,0.4);
            letter-spacing: 0.04em; text-transform: uppercase; margin-top: 2px;
        }

        .topbar-center { flex:1; display:flex; align-items:center; gap:10px; }

        .sidebar-toggle {
            display: none; background: none; border: none;
            width: 36px; height: 36px; border-radius: var(--border-radius-sm);
            align-items: center; justify-content: center;
            cursor: pointer; color: rgba(255,255,255,0.7);
            transition: background var(--transition-fast);
        }
        .sidebar-toggle:hover { background: rgba(255,255,255,0.1); color:#fff; }

        .page-breadcrumb {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; color: rgba(255,255,255,0.38);
        }
        .page-breadcrumb .crumb-current { color:rgba(255,255,255,0.8); font-weight:500; }
        .page-breadcrumb i { font-size:9px; color:rgba(255,255,255,0.2); }

        .topbar-right { display:flex; align-items:center; gap:8px; }

        .topbar-clock {
            font-size: 12px; color: rgba(255,255,255,0.38);
            font-variant-numeric: tabular-nums;
            display:flex; align-items:center; gap:6px;
            padding-right:12px; border-right:1px solid rgba(255,255,255,0.08);
            margin-right:4px;
        }
        .topbar-clock i { font-size:11px; color:rgba(255,255,255,0.22); }

        /* ── TOPBAR ICON BUTTONS ─────────────────────────────────
           FIX: use opacity trick so icon never disappears on hover  */
        .topbar-btn {
            width: 34px; height: 34px;
            display: flex; align-items: center; justify-content: center;
            border-radius: var(--border-radius-sm);
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            cursor: pointer; color: rgba(255,255,255,0.55);
            font-size: 14px;
            transition: background var(--transition-fast),
                        color var(--transition-fast),
                        box-shadow var(--transition-fast);
            text-decoration: none;
        }
        .topbar-btn:hover {
            background: rgba(255,255,255,0.14);
            color: #fff;
            box-shadow: 0 0 8px rgba(255,255,255,0.12);
            border-color: rgba(255,255,255,0.18);
        }

        .topbar-user {
            display:flex; align-items:center; gap:10px;
            padding:6px 10px; border-radius:var(--border-radius-sm);
            cursor:pointer; transition:background var(--transition-fast);
            text-decoration:none;
        }
        .topbar-user:hover { background:rgba(255,255,255,0.07); }

        .user-avatar {
            width:34px; height:34px;
            background: var(--color-accent);
            border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:13px; font-weight:700; color:white; flex-shrink:0;
        }

        /* operator avatar gets section-specific colour */
        .user-avatar.op-556  { background:#D97706; }
        .user-avatar.op-762  { background:#7C3AED; }
        .user-avatar.op-prm  { background:#0891B2; }
        .user-avatar.op-cal  { background:#059669; }
        .user-avatar.op-pkg  { background:#DB2777; }

        .user-info { display:flex; flex-direction:column; line-height:1; }
        .user-name  { font-size:13px; font-weight:600; color:rgba(255,255,255,0.9); }
        .user-role  { font-size:11px; color:rgba(255,255,255,0.38); margin-top:2px; text-transform:capitalize; }

        /* ── SIDEBAR ─────────────────────────────────────────────── */
        .sidebar-overlay {
            position:fixed; inset:0; background:rgba(0,0,0,0.48);
            z-index:990; opacity:0; pointer-events:none;
            transition:opacity var(--transition-slow); backdrop-filter:blur(2px);
        }
        .sidebar-overlay.active { opacity:1; pointer-events:auto; }

        .sidebar {
            position:fixed; top:var(--topbar-height); left:0; bottom:0;
            width:var(--sidebar-width);
            background:var(--color-surface);
            border-right:1px solid var(--color-border);
            display:flex; flex-direction:column;
            overflow-y:auto; overflow-x:hidden;
            z-index:800; transition:transform var(--transition-slow);
        }

        .sidebar-section { padding:20px 12px 6px; }
        .sidebar-section:first-child { padding-top:16px; }

        .section-label {
            font-size:10px; font-weight:700; color:var(--color-text-subtle);
            text-transform:uppercase; letter-spacing:0.08em;
            padding:0 8px; margin-bottom:4px; display:block;
        }

        .nav-link {
            display:flex; align-items:center; gap:10px;
            padding:9px 10px; border-radius:var(--border-radius-sm);
            text-decoration:none; color:var(--color-text-muted);
            font-size:13.5px; font-weight:500;
            transition:all var(--transition-fast);
            margin-bottom:1px; position:relative;
            cursor:pointer;
        }
        .nav-link:hover { background:var(--color-bg); color:var(--color-text-primary); }
        .nav-link.active {
            background:var(--color-accent-pale);
            color:var(--color-accent); font-weight:700;
        }
        .nav-link.active::before {
            content:''; position:absolute; left:0; top:25%; bottom:25%;
            width:3px; background:var(--color-accent); border-radius:0 2px 2px 0;
        }
        .nav-link .nav-icon { width:18px; text-align:center; font-size:14px; flex-shrink:0; }
        .nav-link .nav-badge {
            margin-left:auto; background:var(--color-danger); color:white;
            font-size:10px; font-weight:700; padding:2px 6px;
            border-radius:20px; min-width:18px; text-align:center;
        }

        .sidebar-footer {
            padding:16px 12px; margin-top:auto;
            border-top:1px solid var(--color-border);
        }
        .sidebar-footer-info {
            font-size:11px; color:var(--color-text-subtle);
            text-align:center; line-height:1.6;
        }

        .sidebar-divider { height:1px; background:var(--color-border); margin:6px 12px; }

        /* ── LAYOUT ──────────────────────────────────────────────── */
        .app-body { padding-top:var(--topbar-height); padding-left:var(--sidebar-width); min-height:100vh; display:flex; flex-direction:column; }
        .main-content { flex:1; padding:28px 28px 40px; max-width:1440px; }
        .app-footer {
            padding:16px 28px; border-top:1px solid var(--color-border);
            background:var(--color-surface);
            display:flex; align-items:center; justify-content:space-between;
            font-size:12px; color:var(--color-text-subtle);
        }

        /* ── PAGE HEADER ─────────────────────────────────────────── */
        .page-header {
            display:flex; align-items:flex-start; justify-content:space-between;
            gap:16px; margin-bottom:24px; flex-wrap:wrap;
        }
        .page-title { font-size:22px; font-weight:700; color:var(--color-text-primary); letter-spacing:-0.02em; line-height:1.2; }
        .page-subtitle { font-size:13px; color:var(--color-text-muted); margin-top:4px; }
        .page-actions { display:flex; gap:10px; flex-wrap:wrap; align-items:flex-start; }

        /* ── BUTTONS ─────────────────────────────────────────────────
           FIX: explicit colour on ALL states so nothing disappears   */
        .btn {
            display:inline-flex; align-items:center; gap:7px;
            padding:9px 16px; border-radius:var(--border-radius-sm);
            font-size:13.5px; font-weight:600; font-family:var(--font-body);
            cursor:pointer; border:1px solid transparent;
            text-decoration:none;
            transition: background var(--transition-fast),
                        color var(--transition-fast),
                        border-color var(--transition-fast),
                        box-shadow var(--transition-fast),
                        transform var(--transition-fast);
            line-height:1; white-space:nowrap;
        }
        .btn:disabled { opacity:0.5; cursor:not-allowed; pointer-events:none; }

        /* Primary */
        .btn-primary {
            background:var(--color-accent); color:#fff;
            border-color:var(--color-accent);
            box-shadow:0 1px 2px rgba(37,99,235,0.2);
        }
        .btn-primary:hover {
            background:#1D4ED8; color:#fff; border-color:#1D4ED8;
            box-shadow:0 0 10px rgba(37,99,235,0.35), 0 2px 6px rgba(37,99,235,0.3);
            transform:translateY(-1px);
        }
        .btn-primary:active { transform:translateY(0); box-shadow:0 1px 2px rgba(37,99,235,0.2); }

        /* Secondary — FIX: explicit colour on all states */
        .btn-secondary {
            background:var(--color-surface); color:var(--color-text-body);
            border-color:var(--color-border-mid); box-shadow:var(--shadow-xs);
        }
        .btn-secondary:hover {
            background:var(--color-bg); color:var(--color-text-primary);
            border-color:var(--color-text-muted);
            box-shadow:0 0 8px rgba(100,116,139,0.15);
        }
        .btn-secondary:active { background:var(--color-border); transform:translateY(0); }

        /* Danger */
        .btn-danger {
            background:var(--color-danger); color:#fff;
            border-color:var(--color-danger);
            box-shadow:0 1px 2px rgba(220,38,38,0.2);
        }
        .btn-danger:hover {
            background:#B91C1C; color:#fff; border-color:#B91C1C;
            box-shadow:0 0 10px rgba(220,38,38,0.35), 0 2px 6px rgba(220,38,38,0.3);
            transform:translateY(-1px);
        }
        .btn-danger:active { transform:translateY(0); }

        /* Success */
        .btn-success {
            background:var(--color-success); color:#fff;
            border-color:var(--color-success);
        }
        .btn-success:hover {
            background:#15803D; color:#fff; border-color:#15803D;
            box-shadow:0 0 10px rgba(22,163,74,0.3);
            transform:translateY(-1px);
        }

        /* Ghost */
        .btn-ghost {
            background:transparent; color:var(--color-text-muted); border-color:transparent;
        }
        .btn-ghost:hover { background:var(--color-bg); color:var(--color-text-body); border-color:var(--color-border); }

        .btn-sm { padding:6px 12px; font-size:12.5px; }
        .btn-icon { padding:7px; width:32px; height:32px; justify-content:center; }

        /* ── STAT CARDS ──────────────────────────────────────────── */
        .stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
        @media(max-width:1100px){.stats-row{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:640px){.stats-row{grid-template-columns:1fr;}}

        .stat-card {
            background:var(--color-surface); border:1px solid var(--color-border);
            border-radius:var(--border-radius-md); padding:20px 22px;
            display:flex; align-items:center; gap:16px;
            box-shadow:var(--shadow-xs);
            transition:box-shadow var(--transition-fast), transform var(--transition-fast);
            position:relative; overflow:hidden;
        }
        .stat-card::after { content:''; position:absolute; top:0; left:0; width:3px; height:100%; }
        .stat-card:hover { box-shadow:var(--shadow-md); transform:translateY(-1px); }
        .stat-card.accent::after  { background:var(--color-accent); }
        .stat-card.success::after { background:var(--color-success); }
        .stat-card.warning::after { background:var(--color-warning); }
        .stat-card.danger::after  { background:var(--color-danger); }

        .stat-icon-wrap {
            width:46px; height:46px; border-radius:var(--border-radius-sm);
            display:flex; align-items:center; justify-content:center;
            font-size:18px; flex-shrink:0;
        }
        .stat-icon-wrap.accent  { background:var(--color-accent-pale); color:var(--color-accent); }
        .stat-icon-wrap.success { background:var(--color-success-bg);  color:var(--color-success); }
        .stat-icon-wrap.warning { background:var(--color-warning-bg);  color:var(--color-warning); }
        .stat-icon-wrap.danger  { background:var(--color-danger-bg);   color:var(--color-danger); }

        .stat-body { flex:1; min-width:0; }
        .stat-value { font-size:26px; font-weight:700; color:var(--color-text-primary); line-height:1; letter-spacing:-0.03em; font-variant-numeric:tabular-nums; }
        .stat-label { font-size:12.5px; color:var(--color-text-muted); margin-top:4px; font-weight:500; }

        /* ── CARDS ───────────────────────────────────────────────── */
        .card { background:var(--color-surface); border:1px solid var(--color-border); border-radius:var(--border-radius-md); box-shadow:var(--shadow-xs); overflow:hidden; }
        .card-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--color-border); gap:12px; }
        .card-title { font-size:14px; font-weight:700; color:var(--color-text-primary); display:flex; align-items:center; gap:8px; }
        .card-title i { color:var(--color-text-muted); font-size:13px; }
        .card-body  { padding:20px; }
        .card-footer { padding:12px 20px; border-top:1px solid var(--color-border); background:var(--color-bg); display:flex; align-items:center; justify-content:space-between; font-size:12px; color:var(--color-text-muted); }

        /* ── TABLE ───────────────────────────────────────────────── */
        .table-toolbar { padding:14px 20px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; border-bottom:1px solid var(--color-border); }
        .table-wrapper { overflow-x:auto; -webkit-overflow-scrolling:touch; }

        table.data-table { width:100%; border-collapse:collapse; font-size:13px; }
        table.data-table thead { background:var(--color-bg); border-bottom:2px solid var(--color-border); }
        table.data-table thead th { padding:11px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--color-text-muted); white-space:nowrap; }
        table.data-table tbody tr { border-bottom:1px solid var(--color-border); transition:background var(--transition-fast); }
        table.data-table tbody tr:last-child { border-bottom:none; }
        table.data-table tbody tr:hover { background:var(--color-bg); }
        table.data-table td { padding:13px 16px; color:var(--color-text-body); vertical-align:middle; }
        table.data-table td.cell-muted  { color:var(--color-text-muted); font-size:12px; }
        table.data-table td.cell-mono   { font-family:var(--font-mono); font-size:12px; color:var(--color-navy-light); background:var(--color-accent-pale); }
        table.data-table td strong { color:var(--color-text-primary); font-weight:600; }

        .table-empty { text-align:center; padding:56px 20px; }
        .table-empty i { font-size:36px; color:var(--color-border-mid); display:block; margin-bottom:14px; }
        .table-empty p { color:var(--color-text-muted); font-size:14px; }
        .table-empty a { color:var(--color-accent); text-decoration:none; font-weight:600; }

        /* ── BADGES ──────────────────────────────────────────────── */
        .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:11.5px; font-weight:600; line-height:1; }
        .badge-success { background:var(--color-success-bg); color:var(--color-success); border:1px solid var(--color-success-border); }
        .badge-warning { background:var(--color-warning-bg); color:var(--color-warning); border:1px solid var(--color-warning-border); }
        .badge-danger  { background:var(--color-danger-bg);  color:var(--color-danger);  border:1px solid var(--color-danger-border); }
        .badge-info    { background:var(--color-info-bg);    color:var(--color-info);    border:1px solid var(--color-info-border); }
        .badge-neutral { background:var(--color-bg);         color:var(--color-text-muted); border:1px solid var(--color-border); }
        .badge-accent  { background:var(--color-accent-pale);color:var(--color-accent);  border:1px solid #BFDBFE; }

        /* ── FORMS ───────────────────────────────────────────────── */
        .form-group { margin-bottom:20px; }
        .form-label { display:block; font-size:12.5px; font-weight:600; color:var(--color-text-body); margin-bottom:6px; letter-spacing:0.01em; }
        .form-label .required { color:var(--color-danger); margin-left:2px; }

        .form-control {
            width:100%; padding:9px 13px;
            border:1px solid var(--color-border-mid);
            border-radius:var(--border-radius-sm);
            font-size:13.5px; font-family:var(--font-body);
            color:var(--color-text-primary); background:var(--color-surface);
            transition:border-color var(--transition-fast), box-shadow var(--transition-fast);
            outline:none; appearance:none;
        }
        .form-control:focus { border-color:var(--color-accent); box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
        .form-control:read-only { background:var(--color-bg); color:var(--color-text-muted); }
        .form-control::placeholder { color:var(--color-text-subtle); }

        select.form-control {
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2394A3B8' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat:no-repeat; background-position:right 12px center;
            padding-right:32px; cursor:pointer;
        }
        .form-hint  { font-size:11.5px; color:var(--color-text-muted); margin-top:5px; }
        .form-error { font-size:11.5px; color:var(--color-danger); margin-top:5px; display:flex; align-items:center; gap:4px; }
        .form-row   { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        @media(max-width:700px){ .form-row{grid-template-columns:1fr;} }

        .search-field { position:relative; }
        .search-field i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--color-text-subtle); font-size:13px; pointer-events:none; }
        .search-field .form-control { padding-left:36px; }

        /* ── ALERTS ──────────────────────────────────────────────── */
        .alert { display:flex; align-items:flex-start; gap:12px; padding:14px 16px; border-radius:var(--border-radius-sm); border:1px solid transparent; margin-bottom:20px; font-size:13.5px; }
        .alert i { margin-top:1px; flex-shrink:0; }
        .alert-content { flex:1; }
        .alert-content strong { display:block; margin-bottom:2px; }
        .alert-success { background:var(--color-success-bg); border-color:var(--color-success-border); color:var(--color-success); }
        .alert-danger, .alert-error { background:var(--color-danger-bg); border-color:var(--color-danger-border); color:var(--color-danger); }
        .alert-warning { background:var(--color-warning-bg); border-color:var(--color-warning-border); color:var(--color-warning); }
        .alert-info    { background:var(--color-info-bg);    border-color:var(--color-info-border);    color:var(--color-info); }

        /* ── MODALS ──────────────────────────────────────────────── */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:2000; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(3px); }
        .modal-overlay.show { display:flex; }
        .modal-dialog { background:var(--color-surface); border-radius:14px; box-shadow:var(--shadow-xl); width:100%; max-width:440px; overflow:hidden; animation:modalIn 0.2s cubic-bezier(0.4,0,0.2,1); }
        @keyframes modalIn { from{opacity:0;transform:scale(0.96) translateY(6px)} to{opacity:1;transform:scale(1) translateY(0)} }
        .modal-header { padding:20px 22px 0; display:flex; align-items:flex-start; gap:14px; }
        .modal-icon-wrap { width:44px; height:44px; border-radius:var(--border-radius-sm); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
        .modal-icon-wrap.danger  { background:var(--color-danger-bg);  color:var(--color-danger); }
        .modal-icon-wrap.warning { background:var(--color-warning-bg); color:var(--color-warning); }
        .modal-icon-wrap.info    { background:var(--color-info-bg);    color:var(--color-info); }
        .modal-title  { font-size:16px; font-weight:700; color:var(--color-text-primary); }
        .modal-desc   { font-size:13px; color:var(--color-text-muted); margin-top:6px; line-height:1.6; }
        .modal-desc strong { color:var(--color-text-body); }
        .modal-footer { padding:16px 22px 20px; display:flex; gap:10px; justify-content:flex-end; margin-top:4px; }

        /* ── PRODUCT TILES ───────────────────────────────────────── */
        .product-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:12px; }
        .product-tile {
            border:1.5px solid var(--color-border); border-radius:var(--border-radius-sm);
            padding:16px 12px; text-align:center; cursor:pointer;
            transition:all var(--transition-fast); background:var(--color-bg);
            position:relative; user-select:none;
        }
        .product-tile:hover { border-color:var(--color-accent-light); background:var(--color-accent-pale); }
        .product-tile.selected { border-color:var(--color-accent); background:var(--color-accent-pale); box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
        .product-tile.selected::after { content:'\f00c'; font-family:'Font Awesome 6 Free'; font-weight:900; position:absolute; top:8px; right:10px; color:var(--color-accent); font-size:11px; }
        .product-tile-icon  { font-size:26px; margin-bottom:8px; display:block; line-height:1; }
        .product-tile-name  { font-size:12.5px; font-weight:700; color:var(--color-text-primary); line-height:1.3; }
        .product-tile-unit  { font-size:10px; color:var(--color-text-subtle); margin-top:4px; text-transform:uppercase; letter-spacing:0.05em; }

        /* ── OPERATOR SECTION BANNER ─────────────────────────────── */
        .section-banner {
            display:flex; align-items:center; gap:14px;
            padding:14px 20px; border-radius:var(--border-radius-md);
            margin-bottom:20px; border:1px solid;
        }
        .section-banner.s-556  { background:#FFFBEB; border-color:#FDE68A; color:#92400E; }
        .section-banner.s-762  { background:#F5F3FF; border-color:#DDD6FE; color:#4C1D95; }
        .section-banner.s-prm  { background:#F0F9FF; border-color:#BAE6FD; color:#0C4A6E; }
        .section-banner.s-cal  { background:#F0FDF4; border-color:#BBF7D0; color:#14532D; }
        .section-banner.s-pkg  { background:#FDF2F8; border-color:#FBCFE8; color:#831843; }
        .section-banner i { font-size:22px; }
        .section-banner-title { font-size:15px; font-weight:700; }
        .section-banner-sub   { font-size:12px; margin-top:2px; opacity:0.75; }

        /* ── TOAST ───────────────────────────────────────────────── */
        .toast-container { position:fixed; bottom:24px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:10px; }
        .toast { display:flex; align-items:center; gap:12px; padding:14px 18px; background:var(--color-text-primary); color:white; border-radius:var(--border-radius-md); box-shadow:var(--shadow-xl); min-width:280px; max-width:380px; font-size:13.5px; font-weight:500; transform:translateX(120%); transition:transform var(--transition-slow); }
        .toast.show { transform:translateX(0); }
        .toast-icon { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:12px; }
        .toast.toast-success { border-left:3px solid var(--color-success); }
        .toast.toast-success .toast-icon { background:rgba(22,163,74,0.15); color:var(--color-success); }
        .toast.toast-danger  { border-left:3px solid var(--color-danger); }
        .toast.toast-danger  .toast-icon { background:rgba(220,38,38,0.15); color:var(--color-danger); }
        .toast.toast-info    { border-left:3px solid var(--color-info); }
        .toast.toast-info    .toast-icon { background:rgba(2,132,199,0.15); color:var(--color-info); }

        /* ── UTILITIES ───────────────────────────────────────────── */
        .mb-0{margin-bottom:0} .mb-2{margin-bottom:12px} .mb-3{margin-bottom:20px} .mb-4{margin-bottom:28px}
        .mt-2{margin-top:12px} .mt-3{margin-top:20px}
        .flex{display:flex} .flex-center{display:flex;align-items:center} .gap-2{gap:8px} .gap-3{gap:12px}
        .text-muted{color:var(--color-text-muted)} .text-sm{font-size:12px} .text-right{text-align:right}
        .fw-600{font-weight:600} .fw-700{font-weight:700}

        ::-webkit-scrollbar{width:5px;height:5px}
        ::-webkit-scrollbar-track{background:transparent}
        ::-webkit-scrollbar-thumb{background:var(--color-border-mid);border-radius:10px}

        /* ── RESPONSIVE ──────────────────────────────────────────── */
        @media(max-width:900px){
            :root{--sidebar-width:0px}
            .sidebar{transform:translateX(-260px);width:260px}
            .sidebar.open{transform:translateX(0)}
            .sidebar-toggle{display:flex}
            .topbar-brand{width:auto}
            .page-breadcrumb{display:none}
            .app-body{padding-left:0}
            .main-content{padding:20px 16px 32px}
        }
    </style>
</head>
<body>

<!-- ── TOP BAR ─────────────────────────────────────────────────── -->
<header class="topbar" role="banner">
    <a class="topbar-brand" href="<?= isAdmin() ? 'home.php' : 'operator_dashboard.php' ?>" aria-label="IMS Home">
        <!-- Indian Government Tiger Emblem (Satyamev Jayate) SVG -->
        <div class="brand-emblem" aria-hidden="true" title="Government of India">
            <img src="/ims-enterprise/ministry_of_defence.png" alt="Government of India Emblem"
                 style="width:40px;height:40px;object-fit:contain;">
        </div>
        <div class="brand-text">
            <span class="brand-name">OFV &ndash; IMS</span>
            <span class="brand-sub">Ordnance Factory Varangaon</span>
        </div>
    </a>

    <div class="topbar-center">
        <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle navigation" id="sidebarToggleBtn">
            <i class="fas fa-bars"></i>
        </button>
        <nav class="page-breadcrumb" aria-label="Breadcrumb">
            <span>Home</span>
            <?php if (!in_array(basename($_SERVER['PHP_SELF']), ['home.php','operator_dashboard.php'])): ?>
            <i class="fas fa-chevron-right"></i>
            <span class="crumb-current">
                <?php
                $pagenames = [
                    'add_product.php'        => 'Add Product',
                    'edit_product.php'       => 'Edit Product',
                    'history.php'            => 'Audit History',
                    'low_stock.php'          => 'Low Stock Alert',
                    'operator_update.php'    => 'Update Stock',
                ];
                echo $pagenames[basename($_SERVER['PHP_SELF'])] ?? ucfirst(str_replace(['.php','_'],['',' '],basename($_SERVER['PHP_SELF'])));
                ?>
            </span>
            <?php endif; ?>
        </nav>
    </div>

    <div class="topbar-right">
        <div class="topbar-clock" id="live-clock" aria-live="polite">
            <i class="fas fa-clock"></i>
            <span id="clock-text">Loading...</span>
        </div>

        <?php if (isAdmin()): ?>
        <a href="low_stock.php" class="topbar-btn" title="Low Stock Alerts">
            <i class="fas fa-bell"></i>
        </a>
        <?php endif; ?>

        <div class="topbar-user" onclick="confirmLogout()" role="button" tabindex="0"
             title="Sign out" aria-label="Sign out <?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?>">
            <?php
            $role = $_SESSION['role'] ?? 'operator';
            $code = $_SESSION['product_code'] ?? '';
            $avatarClass = '';
            if ($role !== 'admin') {
                $avatarMap = ['OFV-556'=>'op-556','OFV-762'=>'op-762','OFV-PRM'=>'op-prm','OFV-CAL'=>'op-cal','OFV-PKG'=>'op-pkg'];
                $avatarClass = $avatarMap[$code] ?? '';
            }
            ?>
            <div class="user-avatar <?= $avatarClass ?>" aria-hidden="true">
                <?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?></span>
                <span class="user-role"><?= $role === 'admin' ? 'Administrator' : htmlspecialchars($_SESSION['section'] ?? 'Operator') ?></span>
            </div>
            <i class="fas fa-chevron-down" style="font-size:10px;color:rgba(255,255,255,0.25);margin-left:2px;"></i>
        </div>
    </div>
</header>

<!-- Sidebar overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ── SIDEBAR ───────────────────────────────────────────────────── -->
<nav class="sidebar" id="sidebar" aria-label="Main navigation">
    <?php if (isAdmin()): ?>
    <!-- ADMIN SIDEBAR -->
    <div class="sidebar-section">
        <span class="section-label">Main Menu</span>
        <a href="home.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])==='home.php'?'active':''?>">
            <span class="nav-icon"><i class="fas fa-th-large"></i></span>Dashboard
        </a>
        <a href="add_product.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])==='add_product.php'?'active':''?>">
            <span class="nav-icon"><i class="fas fa-plus-circle"></i></span>Add Product
        </a>
    </div>
    <div class="sidebar-divider"></div>
    <div class="sidebar-section">
        <span class="section-label">Inventory</span>
        <a href="low_stock.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])==='low_stock.php'?'active':''?>">
            <span class="nav-icon"><i class="fas fa-exclamation-triangle"></i></span>Low Stock Alert
        </a>
    </div>
    <div class="sidebar-divider"></div>
    <div class="sidebar-section">
        <span class="section-label">Reports</span>
        <a href="history.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])==='history.php'?'active':''?>">
            <span class="nav-icon"><i class="fas fa-history"></i></span>Audit History
        </a>
    </div>
    <?php else: ?>
    <!-- OPERATOR SIDEBAR -->
    <div class="sidebar-section">
        <span class="section-label">My Section</span>
        <a href="operator_dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])==='operator_dashboard.php'?'active':''?>">
            <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>My Dashboard
        </a>
        <a href="operator_update.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])==='operator_update.php'?'active':''?>">
            <span class="nav-icon"><i class="fas fa-edit"></i></span>Update Stock
        </a>
    </div>
    <?php endif; ?>
    <div class="sidebar-divider"></div>
    <div class="sidebar-section">
        <span class="section-label">Account</span>
        <a href="javascript:void(0)" class="nav-link" onclick="confirmLogout()">
            <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>Sign Out
        </a>
    </div>
    <div class="sidebar-footer">
        <div class="sidebar-footer-info">IMS v3.0 &nbsp;&bull;&nbsp; <?= date('Y') ?><br>Ministry of Defence</div>
    </div>
</nav>

<!-- ── APP BODY ───────────────────────────────────────────────────── -->
<div class="app-body">
<main class="main-content" id="main-content">

<!-- LOGOUT MODAL -->
<div class="modal-overlay" id="logoutModal" role="dialog" aria-modal="true" aria-labelledby="logoutModalTitle">
    <div class="modal-dialog">
        <div class="modal-header">
            <div class="modal-icon-wrap warning"><i class="fas fa-sign-out-alt"></i></div>
            <div>
                <div class="modal-title" id="logoutModalTitle">Sign Out</div>
                <div class="modal-desc">Are you sure you want to sign out of the <strong>OFV Inventory Management System</strong>? Any unsaved work will be lost.</div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeLogoutModal()">Cancel</button>
            <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i>Yes, Sign Out</a>
        </div>
    </div>
</div>

<!-- TOAST CONTAINER -->
<div class="toast-container" aria-live="polite"></div>

<script>
    // Live clock
    (function tick(){
        const el = document.getElementById('clock-text');
        if (el) {
            const n = new Date();
            el.textContent = n.toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'})
                           + ', ' + n.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit',hour12:true});
        }
        setTimeout(tick, 1000);
    })();

    // Sidebar
    function toggleSidebar(){
        const s=document.getElementById('sidebar'),o=document.getElementById('sidebarOverlay'),b=document.getElementById('sidebarToggleBtn');
        const open=s.classList.toggle('open');
        o.classList.toggle('active',open);
        b?.setAttribute('aria-expanded',open);
    }
    function closeSidebar(){
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('active');
        document.getElementById('sidebarToggleBtn')?.setAttribute('aria-expanded','false');
    }

    // Logout modal
    function confirmLogout(){ closeSidebar(); document.getElementById('logoutModal').classList.add('show'); }
    function closeLogoutModal(){ document.getElementById('logoutModal').classList.remove('show'); }

    // Toast
    function showToast(msg, type='success'){
        const icons={success:'fa-check',danger:'fa-times',info:'fa-info',warning:'fa-exclamation'};
        const t=document.createElement('div');
        t.className=`toast toast-${type}`;
        t.innerHTML=`<div class="toast-icon"><i class="fas ${icons[type]||icons.info}"></i></div><span>${msg}</span>`;
        document.querySelector('.toast-container').appendChild(t);
        requestAnimationFrame(()=>requestAnimationFrame(()=>t.classList.add('show')));
        setTimeout(()=>{t.classList.remove('show');setTimeout(()=>t.remove(),400);},4000);
    }

    // Auto-toast from URL
    (function(){
        const p=new URLSearchParams(window.location.search);
        const m={'1':['Product added to inventory.','success'],'2':['Product updated.','success'],
                 '3':['Product removed.','info'],'4':['Inventory updated.','success'],
                 '5':['Stock updated successfully.','success']};
        const v=p.get('success'); if(v&&m[v])showToast(...m[v]);
    })();

    // Keyboard ESC
    document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeLogoutModal();closeSidebar();}});
</script>
