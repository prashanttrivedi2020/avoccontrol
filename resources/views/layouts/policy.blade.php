<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'FireKontrol 365')</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f8fafc">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FK365">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png">
    

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #f4f7fb;
            --bg2:       #ffffff;
            --sidebar:   #f8fafc;
            --accent:    #e11d48;
            --accent2:   #f43f5e;
            --green:     #0f766e;
            --green-l:   #14b8a6;
            --text:      #0f172a;
            --text-muted: #64748b;
            --border:    #e2e8f0;
            --card:      #ffffff;
            --card2:     #f8fafc;
            --white:     #ffffff;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--bg) 0%, #eef2f7 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ── Sidebar ───────────────────────────────── */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
        }
        .sidebar-logo {
            /* padding: 4px 20px 20px; */
            border-bottom: 1px solid var(--border);
        }
        .sidebar-logo .brand {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .sidebar-logo .brand .fire { color: var(--accent); }
        .sidebar-logo .brand .kont { color: var(--text); }
        .sidebar-logo .brand .num  { color: var(--accent); }
        .sidebar-logo .sub {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }
        .sidebar nav { padding: 16px 0; }
        .nav-section { padding: 8px 20px 4px; font-size: 10px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.15s;
            border-left: 3px solid transparent;
        }
        .nav-link:hover    { color: var(--text); background: rgba(225,29,72,0.10); }
        .nav-link.active   { color: var(--white); border-left-color: var(--accent); background: linear-gradient(90deg, var(--accent) 0%, var(--accent2) 100%); }
        .nav-link .icon { font-size: 16px; width: 20px; text-align: center; }
        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--border);
        }
        .sidebar-user {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sidebar-user .avatar {
            width: 28px; height: 28px;
            background: var(--green);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: white;
            flex-shrink: 0;
        }
        .demo-badge {
            display: inline-block;
            background: rgba(255,165,0,0.2);
            color: #ffaa00;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 4px;
            border: 1px solid rgba(255,165,0,0.3);
        }
        .btn-logout {
            display: block;
            width: 100%;
            padding: 8px 12px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-muted);
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.15s;
        }
        .btn-logout:hover { border-color: var(--accent); color: var(--accent); }

        /* ── Language switcher ─────────────────────── */
        .lang-switcher {
            display: flex;
            gap: 4px;
            margin-bottom: 10px;
        }
        .lang-btn {
            flex: 1;
            padding: 5px 4px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-muted);
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.15s;
        }
        .lang-btn:hover { border-color: var(--green); color: var(--text); }
        .lang-btn.active { background: var(--green); border-color: var(--green); color: white; }

        /* ── Main content ──────────────────────────── */
        .main {
            margin-left: 240px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            padding: 16px 32px;
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 80;
        }
        .topbar h1 { font-size: 20px; font-weight: 700; }
        .topbar-actions { display: flex; gap: 10px; align-items: center; }
        .content { flex: 1; padding: 24px 24px 32px; }
        .mobile-menu-toggle {
            display: none;
            width: 42px;
            height: 42px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: rgba(255,255,255,0.06);
            color: var(--text);
            font-size: 20px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 90;
        }

        /* ── Components ────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.15s;
        }
        .btn-primary  { background: var(--accent); color: var(--white); box-shadow: 0 8px 18px rgba(225,29,72,0.16); }
        .btn-primary:hover { background: var(--accent2); }
        .btn-secondary { background: var(--bg2); color: var(--text); border: 1px solid var(--border); }
        .btn-secondary:hover { background: var(--card2); border-color: var(--green-l); color: var(--text); }
        .btn-danger   { background: rgba(255,45,85,0.16); color: var(--accent); border: 1px solid rgba(255,45,85,0.28); }
        .btn-danger:hover { background: rgba(255,45,85,0.24); }
        .btn-sm { padding: 5px 10px; font-size: 12px; }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(15,23,42,0.05);
            padding: 24px;
        }
        .card-sm { padding: 16px; }

        .stat-card {
            background: var(--card2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
        }
        .stat-label { font-size: 8px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .stat-value { font-size: 22px; font-weight: 800; }
        .stat-value.accent { color: var(--accent2); }
        .stat-sub   { font-size: 8px; color: var(--text-muted); margin-top: 4px; }

        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 14px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); }
        td { padding: 12px 14px; font-size: 14px; border-bottom: 1px solid rgba(15,23,42,0.06); vertical-align: middle; color: var(--text); }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(225,29,72,0.06); }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-red    { background: rgba(225,29,72,0.12); color: #be123c; }
        .badge-orange { background: rgba(245,158,11,0.14); color: #b45309; border: 1px solid rgba(245,158,11,0.20); }
        .badge-blue   { background: rgba(59,130,246,0.12); color: #1d4ed8; border: 1px solid rgba(59,130,246,0.20); }
        .badge-green  { background: rgba(15,118,110,0.12); color: #0f766e; border: 1px solid rgba(15,118,110,0.20); }
        .badge-gray   { background: rgba(15,23,42,0.06); color: #475569; }

        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 14px;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-control:focus { outline: none; border-color: var(--green-l); box-shadow: 0 0 0 3px rgba(20,184,166,0.15); }
        .form-control::placeholder { color: var(--text-muted); }
        select.form-control option { background: var(--bg2); }
        .form-hint { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
        .form-error { font-size: 12px; color: var(--accent); margin-top: 4px; }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: rgba(20,184,166,0.10);  border: 1px solid rgba(20,184,166,0.20);  color: #0f766e; }
        .alert-error   { background: rgba(225,29,72,0.10);  border: 1px solid rgba(225,29,72,0.20);  color: #be123c; }
        .alert-info    { background: rgba(59,130,246,0.10); border: 1px solid rgba(59,130,246,0.20); color: #1d4ed8; }

        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
        .empty-state .icon { font-size: 48px; margin-bottom: 16px; }
        .empty-state h3 { font-size: 18px; font-weight: 600; color: var(--text); margin-bottom: 8px; }

        .pagination { display: flex; gap: 4px; align-items: center; justify-content: center; margin-top: 24px; }
        .pagination a, .pagination span {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            border: 1px solid var(--border);
        }
        .pagination a:hover { background: var(--card2); color: var(--text); }
        .pagination .current { background: var(--accent); border-color: var(--accent); color: white; }

        @media (max-width: 900px) {
            .grid-4, .grid-3 { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            body { display: block; }
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.2s ease;
                width: 86%;
                max-width: 300px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            }
            .sidebar.is-open { transform: translateX(0); }
            .sidebar-overlay.is-open { display: block; }
            .main { margin-left: 0; }
            .topbar { padding: 14px 16px; }
            .mobile-menu-toggle { display: inline-flex; }
            .content { padding: 16px; }
            .grid-2, .grid-3, .grid-4 { grid-template-columns: repeat(1, 1fr); }
            .card, .stat-card { padding: 3px; }
            table { min-width: 620px; }
        }

        @media (max-width: 480px) {
            .topbar { align-items: center; }
            .topbar h1 { font-size: 16px; }
            .topbar-actions .btn { width: 100%; justify-content: center; }
            .topbar-actions { width: 100%; justify-content: flex-end; }
        }
        /* Hide third-party carousel/slider nav controls on pages that don't use them (e.g. products).
           This is intentionally broad and defensive — it hides known slider classes and
           common arrow controls so they don't overlay the product listing. */
        .products-page .owl-nav,
        .products-page .owl-dots,
        .products-page .owl-prev,
        .products-page .owl-next,
        .products-page .carousel-control-prev,
        .products-page .carousel-control-next,
        .products-page .carousel-control-prev-icon,
        .products-page .carousel-control-next-icon,
        .products-page [class*="slick-"],
        .products-page .slick-arrow,
        .products-page .slick-prev,
        .products-page .slick-next,
        .products-page .flickity-button,
        .products-page .flickity-prev-next-button,
        .products-page .flickity-button.previous,
        .products-page .flickity-button.next,
        .products-page .splide__arrow,
        .products-page .splide__arrow--prev,
        .products-page .splide__arrow--next,
        .products-page .glide__arrow,
        .products-page .glide__arrow--left,
        .products-page .glide__arrow--right {
            display: none !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        /* Defensive: hide any absolutely- or fixed-positioned large left/right controls
           that may still appear from third-party CSS. This only applies on the products page. */
        .products-page [style*="position:fixed" i][style*="left" i],
        .products-page [style*="position:absolute" i][style*="left" i] {
            display: none !important;
        }
    </style>

<style>
/* ===========================
   Bootstrap display utilities
   (Required for Laravel pagination)
   =========================== */

/* Base */
.d-none {
    display: none !important;
}

.d-flex {
    display: flex !important;
}

.flex-fill {
    flex: 1 1 auto !important;
}

.justify-content-between {
    justify-content: space-between !important;
}

.align-items-center,
.align-items-sm-center {
    align-items: center !important;
}

/* >=576px */
@media (min-width: 576px) {

    .d-sm-none {
        display: none !important;
    }

    .d-sm-flex {
        display: flex !important;
    }

    .flex-sm-fill {
        flex: 1 1 auto !important;
    }

    .justify-content-sm-between {
        justify-content: space-between !important;
    }

    .align-items-sm-center {
        align-items: center !important;
    }
}

/* <576px */
@media (max-width: 575.98px) {

    .d-sm-flex {
        display: none !important;
    }

    .d-sm-none {
        display: flex !important;
    }
}

/* Improve pagination appearance */
.pagination nav {
    width: 100%;
}

.pagination ul {
    display: flex;
    gap: 4px;
    list-style: none;
    margin: 0;
    padding: 0;
}

.pagination .page-item {
    margin: 0;
}

.pagination .page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    height: 34px;
    padding: 0 12px;
    border: 1px solid var(--border);
    border-radius: 6px;
    background: transparent;
    color: var(--text);
    text-decoration: none;
}

.pagination .page-item.active .page-link {
    background: var(--accent);
    border-color: var(--accent);
    color: #fff;
}

.pagination .page-item.disabled .page-link {
    opacity: .5;
    pointer-events: none;
}

</style>
@stack('styles')
</head>
<body>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="main">
    
    <div class="content">
       
        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</body>
</html>
