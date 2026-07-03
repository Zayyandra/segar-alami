<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Halaman Tidak Ditemukan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;min-height:100vh;background:#fafafa;display:flex;align-items:center;justify-content:center;padding:1.5rem;}
        @keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
        @keyframes scaleIn{from{opacity:0;transform:scale(.85)}to{opacity:1;transform:scale(1)}}
        @keyframes drift{0%,100%{transform:translateY(0) rotate(-2deg)}50%{transform:translateY(-10px) rotate(2deg)}}
        .a1{animation:fadeUp .5s ease both}
        .a2{animation:fadeUp .5s .1s ease both;opacity:0}
        .a3{animation:fadeUp .5s .2s ease both;opacity:0}
        .a4{animation:fadeUp .5s .3s ease both;opacity:0}
        .icon-anim{animation:scaleIn .4s .05s ease both;opacity:0}
        .drift{animation:drift 5s ease-in-out infinite}
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

        <div class="icon-anim drift" style="display:flex;justify-content:center;margin-bottom:1.75rem;">
            <div style="width:80px;height:80px;border-radius:24px;background:#f8fafc;border:1.5px solid #e2e8f0;display:flex;align-items:center;justify-content:center;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    <line x1="11" y1="8" x2="11" y2="14"/>
                    <line x1="8" y1="11" x2="14" y2="11"/>
                </svg>
            </div>
        </div>

        <p class="a2" style="font-size:11px;font-weight:600;letter-spacing:.1em;color:#94a3b8;text-transform:uppercase;margin-bottom:.5rem;">
            Error 404
        </p>
        <h1 class="a2" style="font-size:1.5rem;font-weight:700;color:#0f172a;margin-bottom:.65rem;letter-spacing:-.02em;line-height:1.25;">
            Halaman Tidak Ditemukan
        </h1>
        <p class="a3" style="font-size:.875rem;color:#64748b;line-height:1.65;margin-bottom:1.75rem;max-width:270px;margin-left:auto;margin-right:auto;">
            Halaman yang Anda cari tidak ada, sudah dipindahkan, atau URL-nya tidak valid.
        </p>

        <div class="a4" style="display:flex;gap:10px;justify-content:center;">
            <a href="javascript:history.back()" class="btn-ghost">← Kembali</a>
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
