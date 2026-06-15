<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#16a34a">
    <link rel="apple-touch-icon" href="/logo.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/logo-192.png">
    <title>@yield('title', 'Une erreur est survenue') — Green Express</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'figtree', ui-sans-serif, system-ui, -apple-system, sans-serif;
            background: linear-gradient(160deg, #f0fdf4 0%, #f8fafc 60%, #ecfdf5 100%);
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            padding-top: max(1.5rem, env(safe-area-inset-top));
            padding-bottom: max(1.5rem, env(safe-area-inset-bottom));
        }
        @media (prefers-color-scheme: dark) {
            body { background: linear-gradient(160deg, #052e16 0%, #020617 60%, #022c22 100%); color: #e2e8f0; }
            .card { background: #0f172a !important; border-color: #1e293b !important; }
            .subtitle { color: #94a3b8 !important; }
            .code-badge { background: #022c22 !important; color: #6ee7b7 !important; }
            .btn-secondary { background: #1e293b !important; color: #e2e8f0 !important; border-color: #334155 !important; }
        }
        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1.5rem;
            box-shadow: 0 20px 50px -20px rgba(0,0,0,0.25);
            max-width: 30rem;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
            animation: pop 0.4s ease-out;
        }
        @keyframes pop {
            from { opacity: 0; transform: translateY(16px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .icon-wrap {
            width: 4.5rem; height: 4.5rem;
            margin: 0 auto 1.25rem;
            border-radius: 1.25rem;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #10b981, #047857);
            box-shadow: 0 12px 24px -8px rgba(16,185,129,0.6);
        }
        .icon-wrap svg { width: 2.25rem; height: 2.25rem; color: #fff; }
        .code-badge {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            background: #ecfdf5;
            color: #047857;
            padding: 0.3rem 0.75rem;
            border-radius: 0.6rem;
            margin-bottom: 1rem;
        }
        h1 { font-size: 1.5rem; font-weight: 700; margin: 0 0 0.5rem; line-height: 1.25; }
        .subtitle { font-size: 0.95rem; color: #64748b; line-height: 1.6; margin: 0 0 1.75rem; }
        .actions { display: flex; flex-direction: column; gap: 0.75rem; }
        @media (min-width: 480px) { .actions { flex-direction: row; justify-content: center; } }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            font-size: 0.9rem; font-weight: 600;
            padding: 0.7rem 1.4rem;
            border-radius: 0.8rem;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: transform 0.15s ease, opacity 0.15s ease;
        }
        .btn:active { transform: scale(0.97); }
        .btn-primary { background: linear-gradient(135deg, #10b981, #047857); color: #fff; }
        .btn-primary:hover { opacity: 0.92; }
        .btn-secondary { background: #f1f5f9; color: #0f172a; border-color: #e2e8f0; }
        .btn-secondary:hover { opacity: 0.85; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            @yield('icon')
        </div>
        <span class="code-badge">@yield('code', 'Erreur')</span>
        <h1>@yield('heading', 'Une erreur est survenue')</h1>
        <p class="subtitle">@yield('message', 'Quelque chose s\'est mal passé. Veuillez réessayer.')</p>
        <div class="actions">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.1rem;height:1.1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Accueil
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.1rem;height:1.1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour
            </a>
        </div>
    </div>
</body>
</html>
