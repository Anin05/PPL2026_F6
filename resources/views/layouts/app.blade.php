<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'ANAHEIM CRM') - PT Anaheim Nimbus Universal</title>
    <meta name="description" content="ANAHEIM ERP F-6 – Modul CRM Klien & Kontak">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <style>
        /* Additional styling for clean blade integration */
        .pagination-bar { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-top: 1px solid var(--border); font-size: 13px; color: var(--muted); }
        .pagination-bar nav { display: flex; gap: 4px; align-items: center; }
        .pagination-bar .page-link { display: inline-flex; align-items: center; justify-content: center; min-width: 28px; height: 28px; padding: 0 8px; border: 1px solid var(--border); border-radius: 6px; font-weight: 500; color: var(--navy); text-decoration: none; background: #fff; }
        .pagination-bar .page-link.active { background: var(--primary); color: #fff; border-color: var(--primary); }
        .pagination-bar .page-link.disabled { opacity: 0.5; pointer-events: none; }
        .dropdown-menu { position: absolute; right: 0; top: 100%; margin-top: 4px; background: #fff; border: 1px solid var(--border); border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); min-width: 160px; z-index: 50; display: none; }
        .dropdown-menu.show { display: block; }
        .dropdown-item { display: flex; align-items: center; gap: 8px; padding: 8px 12px; font-size: 13px; color: var(--navy); text-decoration: none; width: 100%; background: none; border: none; text-align: left; cursor: pointer; }
        .dropdown-item:hover { background: var(--bg-hover, #f8fafc); }
        .dropdown-item.danger { color: #dc2626; }
        .rel { position: relative; }
    </style>
</head>
<body>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="brand">
                <div class="brand-row"><span class="logo-mark">A</span>ANAHEIM</div>
                <small>PT Anaheim Nimbus Universal</small>
            </div>
            <div class="ws">
                <small>WORKSPACE</small>
                <b>F-6 · CRM Klien &amp; Kontak</b>
            </div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'cur' : '' }}">
                    <x-icon name="grid" size="17" /> Dashboard
                </a>
                <a href="{{ route('klien.index') }}" class="{{ request()->routeIs('klien.*') ? 'cur' : '' }}">
                    <x-icon name="building" size="17" /> Master Klien
                </a>
                <a href="{{ route('kontak.index') }}" class="{{ request()->routeIs('kontak.*') ? 'cur' : '' }}">
                    <x-icon name="contact" size="17" /> Kontak Klien
                </a>
                <a href="{{ route('riwayat.index') }}" class="{{ request()->routeIs('riwayat.*') ? 'cur' : '' }}">
                    <x-icon name="message" size="17" /> Riwayat Interaksi
                </a>
                <a href="{{ route('arsip.index') }}" class="{{ request()->routeIs('arsip.*') ? 'cur' : '' }}">
                    <x-icon name="archive" size="17" /> Arsip
                </a>
            </nav>
            <div class="side-foot">
                <a class="help" href="#" onclick="alert('Pusat bantuan ANAHEIM ERP: hubungi support@anaheim.co.id'); return false;">
                    <x-icon name="help" size="17" /> Pusat bantuan
                </a>
                <div class="side-status"><i></i>Sistem beroperasi normal</div>
                <div class="side-ver">ANAHEIM ERP · Laravel v{{ Illuminate\Foundation\Application::VERSION }}</div>
            </div>
        </aside>

        <div class="scrim" id="scrim" onclick="document.body.classList.remove('side-open')"></div>

        <div class="main">
            <!-- Topbar -->
            <header class="topbar">
                <button class="icon-btn menu-btn" id="menuBtn" aria-label="Menu" onclick="document.body.classList.toggle('side-open')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                        <line x1="3" y1="12" x2="21" y2="12" />
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <line x1="3" y1="18" x2="21" y2="18" />
                    </svg>
                </button>
                <h2 class="top-title" id="pageTitle">@yield('page_title', 'CRM Anaheim')</h2>
                <div class="top-right">
                    <form action="{{ route('klien.index') }}" method="GET" class="top-search">
                        <x-icon name="search" size="16" />
                        <input name="q" placeholder="Cari di workspace..." value="{{ request('q') }}" autocomplete="off">
                        <kbd>⌘ K</kbd>
                    </form>
                    <button class="icon-btn bell" id="bellBtn" aria-label="Notifikasi" onclick="alert('Tidak ada notifikasi baru.')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                        <i class="dot"></i>
                    </button>
                    <span class="top-sep"></span>
                    <button class="user" id="userBtn" type="button">
                        <span class="user-av">AC</span>
                        <span class="user-t"><b>Admin CRM</b><small>Administrator</small></span>
                    </button>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="content">
                @if (session('success'))
                    <div class="alert ok" id="flash">
                        <span class="al-ic"><x-icon name="check" size="14" /></span>
                        <div><b>Berhasil!</b><br>{{ session('success') }}</div>
                        <button class="al-x" onclick="this.closest('.alert').remove()" aria-label="Tutup"><x-icon name="x" size="14" /></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; display:flex; align-items:center; gap:12px; padding:12px 16px; border-radius:8px; margin-bottom:16px;">
                        <span class="al-ic"><x-icon name="alert" size="14" /></span>
                        <div><b>Perhatian:</b><br>{{ session('error') }}</div>
                        <button class="al-x" onclick="this.closest('.alert').remove()" aria-label="Tutup" style="margin-left:auto; background:none; border:none; cursor:pointer;"><x-icon name="x" size="14" /></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="foot">
                <span>© 2026 PT Anaheim Nimbus Universal</span>
                <span>F-6 · CRM Klien &amp; Kontak (Laravel Blade)</span>
            </footer>
        </div>
    </div>

    <script>
        // Dropdown menu toggle helper
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-dropdown-trigger]');
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (!btn || m !== btn.nextElementSibling) {
                    m.classList.remove('show');
                }
            });
            if (btn) {
                e.preventDefault();
                e.stopPropagation();
                const menu = btn.nextElementSibling;
                if (menu) menu.classList.toggle('show');
            }
        });
        <script src="{{ asset('js/api.js') }}"></script>
    <script>
        // tampilkan pesan sukses yang disimpan sebelum pindah halaman
        (function () {
            const msg = sessionStorage.getItem('flash');
            if (!msg) return;
            sessionStorage.removeItem('flash');
            const el = document.createElement('div');
            el.className = 'alert ok';
            el.innerHTML = '<div><b>Berhasil!</b><br></div>';
            el.firstChild.append(msg);
            document.querySelector('main.content').prepend(el);
        })();
        </script>                                   <!-- dropdown, baris 145 -->
    <script src="{{ asset('js/api.js') }}"></script>
    <script> ... flash ... </script>
    @stack('scripts')
</body>
</html>

