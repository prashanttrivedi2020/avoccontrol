<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FireKontrol 365 – {{ __('No loss goes undocumented.') }}</title>

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a3a1a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FK365">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            color: #0f172a;
        }

        /* ── Language switcher (top-right) ────── */
        .lang-bar {
            position: fixed;
            top: 14px;
            right: 20px;
            display: flex;
            gap: 4px;
            z-index: 100;
        }
        .lang-bar a {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            color: #64748b;
            background: #ffffff;
            transition: all .15s;
        }
        .lang-bar a:hover { border-color: #0f766e; color: #0f766e; }
        .lang-bar a.active { background: #0f766e; border-color: #0f766e; color: #fff; }

        .page-wrap {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 40px;
            max-width: 960px;
            width: 100%;
        }

        /* ── Left: Marketing ─────────────────── */
        .marketing { padding: 20px 0; }
        .hero-headline {
            font-size: 32px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            color: #0f172a;
        }
        .hero-sub {
            font-size: 15px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 32px;
            max-width: 520px;
        }

        .feature-block { margin-bottom: 32px; }
        .feature-block h3 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 14px;
            color: #0f172a;
        }
        .feature-list { list-style: none; }
        .feature-list li {
            font-size: 13px;
            color: #475569;
            padding: 5px 0;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            line-height: 1.4;
        }
        .feature-list li .icon { flex-shrink: 0; font-size: 15px; }

        .pricing-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px 24px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 12px 30px rgba(15,23,42,0.06);
        }
        .pricing-bar .label {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }
        .pricing-bar .price { font-size: 32px; font-weight: 800; color: #0f172a; }
        .pricing-bar .price .cur { font-size: 22px; }
        .pricing-bar .price .period { font-size: 16px; font-weight: 500; color: #64748b; }
        .pricing-bar .vat { font-size: 12px; color: #64748b; margin-top: 4px; }

        .impressum a {
            font-size: 13px;
            color: #0369a1;
            text-decoration: none;
        }
        .impressum a::before { content: '▶ '; font-size: 10px; }
        .impressum a:hover { color: #0f766e; }

        /* ── Right: Auth card ─────────────────── */
        .auth-card {
            background: #fff;
            border-radius: 16px;
            padding: 32px 28px;
            color: #0f172a;
            box-shadow: 0 20px 60px rgba(15,23,42,0.12);
            border: 1px solid #e2e8f0;
        }
        .auth-logo { text-align: center; margin-bottom: 24px; }
        .logo-mark {
            display: inline-block;
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            border-radius: 12px;
            padding: 16px 24px;
            margin-bottom: 8px;
        }
        .logo-text {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -0.5px;
            line-height: 1;
        }
        .logo-fire { color: #e11d48; }
        .logo-kont { color: #fff; }
        .logo-365  { color: #e11d48; }
        .logo-sub  { font-size: 11px; color: #64748b; font-style: italic; margin-top: 4px; }

        .form-tabs { display: flex; border-bottom: 2px solid #e2e8f0; margin-bottom: 20px; }
        .tab-btn {
            flex: 1;
            padding: 8px;
            background: none;
            border: none;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            color: #64748b;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.15s;
        }
        .tab-btn.active { color: #e11d48; border-bottom-color: #e11d48; }

        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 5px; }
        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            color: #0f172a;
            background: #f8fafc;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus { outline: none; border-color: #0f766e; background: #fff; box-shadow: 0 0 0 3px rgba(20,184,166,0.15); }
        .form-error { font-size: 12px; color: #e11d48; margin-top: 3px; }

        .btn-submit {
            display: block;
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s;
            margin-bottom: 10px;
        }
        .btn-submit:hover { background: linear-gradient(135deg, #0d9488 0%, #2dd4bf 100%); }
        .btn-demo {
            display: block;
            width: 100%;
            padding: 11px;
            background: white;
            color: #334155;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            margin-bottom: 10px;
        }
        .btn-demo:hover { border-color: #0f766e; color: #0f766e; }

        .proto-notice {
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
            line-height: 1.5;
        }

        .alert-box {
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 14px;
        }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .alert-success { background: #f0fdfa; border: 1px solid #99f6e4; color: #0f766e; }

        @media (max-width: 768px) {
            .page-wrap { grid-template-columns: 1fr; }
            .hero-headline { font-size: 24px; }
        }
    </style>
</head>
<body>

{{-- Language switcher --}}
@php $locale = app()->getLocale(); @endphp
<div class="lang-bar">
    <a href="{{ route('lang.switch', 'de') }}" class="{{ $locale === 'de' ? 'active' : '' }}">🇩🇪 DE</a>
    <a href="{{ route('lang.switch', 'en') }}" class="{{ $locale === 'en' ? 'active' : '' }}">🇬🇧 EN</a>
    <a href="{{ route('lang.switch', 'tr') }}" class="{{ $locale === 'tr' ? 'active' : '' }}">🇹🇷 TR</a>
</div>

<div class="page-wrap">
     <!-- Right: Auth card -->
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-mark">
                <div class="logo-text">
                    <span class="logo-fire">Fire</span><span class="logo-kont">Kontrol</span>
                    <span class="logo-365"> 365</span>
                </div>
            </div>
            <div class="logo-sub">{{ __('No money down the drain!') }} 🔥</div>
        </div>

        @if ($errors->any())
            <div class="alert-box alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert-box alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="form-tabs">
            <button class="tab-btn {{ !old('_tab') || old('_tab') === 'register' ? 'active' : '' }}"
                    onclick="switchTab('register', event)">{{ __('Create account') }}</button>
            <button class="tab-btn {{ old('_tab') === 'login' ? 'active' : '' }}"
                    onclick="switchTab('login', event)">{{ __('Log in') }}</button>
        </div>

        <!-- Register -->
        <div id="tab-register" class="tab-panel {{ !old('_tab') || old('_tab') === 'register' ? 'active' : '' }}">
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="_tab" value="register">
                <div class="form-group">
                    <label class="form-label">{{ __('Username') }}</label>
                    <input type="text" name="username" class="form-input"
                           value="{{ old('username') }}" autocomplete="username" autofocus>
                    @error('username') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Password') }}</label>
                    <input type="password" name="password" class="form-input" autocomplete="new-password">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Confirm password') }}</label>
                    <input type="password" name="password_confirmation" class="form-input" autocomplete="new-password">
                </div>
                <button type="submit" class="btn-submit">{{ __('Create account') }}</button>
            </form>
        </div>

        <!-- Login -->
        <div id="tab-login" class="tab-panel {{ old('_tab') === 'login' ? 'active' : '' }}">
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <input type="hidden" name="_tab" value="login">
                <div class="form-group">
                    <label class="form-label">{{ __('Username') }}</label>
                    <input type="text" name="username" class="form-input"
                           value="{{ old('username') }}" autocomplete="username">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Password') }}</label>
                    <input type="password" name="password" class="form-input" autocomplete="current-password">
                </div>
                <button type="submit" class="btn-submit">{{ __('Log in') }}</button>
            </form>
        </div>

        

        
    </div>
    <!-- Left: Marketing -->
    <div class="marketing">
        <h1 class="hero-headline">{{ __('No loss goes undocumented.') }}</h1>
        <p class="hero-sub">{{ __('Document spoilage, expiry and theft in seconds with a photo — clean, tamper-proof records for the tax office.') }}</p>

        <div class="feature-block">
            <h3>{{ __('What is FireKontrol 365?') }}</h3>
            <ul class="feature-list">
                <li><span class="icon">📸</span> {{ __('Record losses by photo or barcode scan') }}</li>
                <li><span class="icon">🚨</span> {{ __('Document theft (police report number, incident details)') }}</li>
                <li><span class="icon">📱</span> {{ __('Add products via barcode scan') }}</li>
                <li><span class="icon">📄</span> {{ __('Annual CSV export + photo summary report') }}</li>
                <li><span class="icon">📊</span> {{ __('Statistics: identify your most discarded products') }}</li>
                <li><span class="icon">🔒</span> {{ __('Immutable, GoBD-compliant recordkeeping') }}</li>
            </ul>
        </div>

        <div class="pricing-bar">
            <div class="label">{{ __('Complete package') }}</div>
            <div class="price">
                <span class="cur">99 €</span>
                <span class="period"> / {{ __('month') }}</span>
            </div>
            <div class="vat">{{ __('excl. VAT — 117.81 € incl. 19% VAT') }}</div>
        </div>

        <div class="impressum">
            <a href="#">{{ __('Imprint & Legal') }}</a>
        </div>
    </div>

   
</div>
<script src="{{ asset('js/pwa-install.js') }}"></script>
<script>
function switchTab(tab, event) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    if (event && event.target) event.target.classList.add('active');
}
</script>
<script>
// if ('serviceWorker' in navigator) {
//     window.addEventListener('load', () => {
//         navigator.serviceWorker.register('/sw.js')
//             .then(reg => console.log('[FK365] SW registered:', reg.scope))
//             .catch(err => console.warn('[FK365] SW registration failed:', err));
//     });
// }
</script>
</body>
</html>
