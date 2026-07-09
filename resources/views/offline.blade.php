<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Offline') }} – FireKontrol 365</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0e1f0e;
            color: #e8f0e8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .box {
            text-align: center;
            max-width: 420px;
        }
        .icon { font-size: 72px; margin-bottom: 24px; }
        h1 { font-size: 28px; font-weight: 800; margin-bottom: 12px; }
        p { color: #8aab8a; font-size: 16px; line-height: 1.6; margin-bottom: 24px; }
        a {
            display: inline-block;
            padding: 12px 24px;
            background: #e8223a;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            transition: background .15s;
        }
        a:hover { background: #ff3d56; }
    </style>
</head>
<body>
    <div class="box">
        <div class="icon">📡</div>
        <h1>{{ __('No internet connection') }}</h1>
        <p>{{ __('FireKontrol 365 requires an internet connection. Please check your network and try again.') }}</p>
        <a href="javascript:window.location.reload()">{{ __('Try again') }}</a>
    </div>
</body>
</html>
