<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Terjadi Kesalahan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;min-height:100vh;background:#fafafa;display:flex;align-items:center;justify-content:center;padding:1.5rem;}
        @keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
        @keyframes scaleIn{from{opacity:0;transform:scale(.85)}to{opacity:1;transform:scale(1)}}
        @keyframes shake{0%,100%{transform:rotate(0)}20%{transform:rotate(-6deg)}40%{transform:rotate(6deg)}60%{transform:rotate(-3deg)}80%{transform:rotate(3deg)}}
        @keyframes blink{0%,100%{opacity:1}50%{opacity:.4}}
        .a1{animation:fadeUp .5s ease both}
        .a2{animation:fadeUp .5s .1s ease both;opacity:0}
        .a3{animation:fadeUp .5s .2s ease both;opacity:0}
        .a4{animation:fadeUp .5s .3s ease both;opacity:0}
        .icon-anim{animation:scaleIn .4s .05s ease both;opacity:0}
        .icon-anim:hover .icon-inner{animation:shake .4s ease}
        .dot{display:inline-block;width:6px;height:6px;border-radius:50%;background:#f59e0b;animation:blink 1.2s ease-in-out infinite}
        .dot:nth-child(2){animation-delay:.2s}
        .dot:nth-child(3){animation-delay:.4s}
        a.btn-ghost{display:inline-block;padding:.65rem 1.2rem;border-radius:10px;border:1px solid #e2e8f0;background:#fff;color:#475569;font-size:.875rem;font-weight:500;text-decoration:none}
        a.btn-ghost:hover{background:#f8fafc;border-color:#cbd5e1}
        a.btn-primary{display:inline-block;padding:.65rem 1.2rem;border-radius:10px;background:#10b981;color:#fff;font-size:.875rem;font-weight:500;text-decoration:none;box-shadow:0 4px 12px rgba(16,185,129,.28)}
        a.btn-primary:hover{background:#059669}
    </style>
</head>
<body>
    <div style="width:100%;max-width:380px;text-align:center;">

        <div class="a1" style="display:flex;justify-content:center;margin-bottom:2.5rem;">
            <div style="width:46px;height:46px;border-radius:13px;background:#fff;border:1px solid #f1f5f9;box-shadow:0 2px 10px rgba(0,0,0,.07);overflow:hidden;padding:4px;">
                <img src="{{ asset('images/logo-segar-alami.jpeg') }}" alt="Logo" style="width:100%;height:100%;object-fit:contain;display:block;">
            </div>
        </div>

        <div class="icon-anim" style="display:flex;justify-content:center;margin-bottom:1.75rem;">
            <div class="icon-inner" style="width:80px;height:80px;border-radius:24px;background:#fffbeb;border:1.5px solid #fde68a;display:flex;align-items:center;justify-content:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
        </div>

        <p class="a2" style="font-size:11px;font-weight:600;letter-spacing:.1em;color:#f59e0b;text-transform:uppercase;margin-bottom:.5rem;">
            Error 500
        </p>
        <h1 class="a2" style="font-size:1.5rem;font-weight:700;color:#0f172a;margin-bottom:.65rem;letter-spacing:-.02em;line-height:1.25;">
            Terjadi Kesalahan Server
        </h1>
        <p class="a3" style="font-size:.875rem;color:#64748b;line-height:1.65;margin-bottom:1.5rem;max-width:270px;margin-left:auto;margin-right:auto;">
            Server mengalami masalah saat memproses permintaan Anda. Coba lagi beberapa saat.
        </p>

        <div class="a3" style="display:flex;align-items:center;justify-content:center;gap:5px;margin-bottom:1.75rem;">
            <span style="font-size:.75rem;color:#94a3b8;">Mencoba menghubungkan</span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>

        <div class="a4" style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:.875rem 1rem;margin-bottom:1.75rem;text-align:left;">
            <p style="font-size:.75rem;font-weight:600;color:#92400e;margin-bottom:.4rem;">Yang bisa dilakukan:</p>
            <ul style="font-size:.75rem;color:#a16207;line-height:1.8;padding-left:1rem;">
                <li>Refresh halaman beberapa saat lagi</li>
                <li>Periksa koneksi internet Anda</li>
                <li>Hubungi admin jika masalah berlanjut</li>
            </ul>
        </div>

        <div class="a4" style="display:flex;gap:10px;justify-content:center;">
            <a href="javascript:location.reload()" class="btn-ghost">↺ Refresh</a>
            <a href="{{ auth()->check() ? route('app.dashboard') : route('login') }}" class="btn-primary">
                {{ auth()->check() ? 'Ke Dashboard' : 'Ke Login' }}
            </a>
        </div>

        <p class="a4" style="margin-top:2.5rem;font-size:.75rem;color:#cbd5e1;">
            Segar Alami · Sistem Informasi UMKM
        </p>
    </div>
</body>
</html>
