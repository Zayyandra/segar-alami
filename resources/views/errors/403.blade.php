<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;min-height:100vh;background:#fafafa;display:flex;align-items:center;justify-content:center;padding:1.5rem;}
        @keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
        @keyframes scaleIn{from{opacity:0;transform:scale(.85)}to{opacity:1;transform:scale(1)}}
        @keyframes ripple{0%,100%{transform:scale(1);opacity:.5}50%{transform:scale(1.7);opacity:0}}
        .a1{animation:fadeUp .5s ease both}
        .a2{animation:fadeUp .5s .1s ease both;opacity:0}
        .a3{animation:fadeUp .5s .2s ease both;opacity:0}
        .a4{animation:fadeUp .5s .3s ease both;opacity:0}
        .icon-anim{animation:scaleIn .4s .05s ease both;opacity:0}
        .rp1{position:absolute;inset:-14px;border-radius:50%;border:1.5px solid #fda4af;animation:ripple 2.5s ease-in-out infinite}
        .rp2{position:absolute;inset:-14px;border-radius:50%;border:1.5px solid #fda4af;animation:ripple 2.5s 1.25s ease-in-out infinite}
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
            <div style="position:relative;width:72px;height:72px;">
                <div class="rp1"></div>
                <div class="rp2"></div>
                <div style="position:relative;width:72px;height:72px;border-radius:50%;background:#fff1f2;border:1.5px solid #fecdd3;display:flex;align-items:center;justify-content:center;">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                    </svg>
                </div>
            </div>
        </div>

        <p class="a2" style="font-size:11px;font-weight:600;letter-spacing:.1em;color:#f43f5e;text-transform:uppercase;margin-bottom:.5rem;">
            Error 403
        </p>
        <h1 class="a2" style="font-size:1.5rem;font-weight:700;color:#0f172a;margin-bottom:.65rem;letter-spacing:-.02em;line-height:1.25;">
            Akses Ditolak
        </h1>
        <p class="a3" style="font-size:.875rem;color:#64748b;line-height:1.65;margin-bottom:1.75rem;max-width:270px;margin-left:auto;margin-right:auto;">
            Anda tidak memiliki izin untuk mengakses halaman ini. Hubungi owner jika merasa ini kesalahan.
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
