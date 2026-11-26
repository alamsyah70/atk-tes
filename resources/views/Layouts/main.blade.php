<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'ATK Dashboard - Inventory Management')</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}?v={{ file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : time() }}"
    >

    <style>
        /* GLOBAL LOADING - FIXED DI TENGAH LAYAR */
        #pageLoading {
            position: fixed !important;

            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;

            width: 100vw !important;
            height: 100vh !important;

            z-index: 2147483647 !important;

            display: none;
            align-items: center !important;
            justify-content: center !important;

            background: transparent !important;
            margin: 0 !important;
            padding: 0 !important;

            transform: none !important;
        }

        #pageLoading.active {
            display: flex !important;
        }

        #pageLoading .page-loading-content {
            position: relative !important;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            width: auto !important;
            height: auto !important;

            margin: 0 !important;
            padding: 20px !important;

            background: transparent !important;
            border: none !important;
            box-shadow: none !important;

            transform: none !important;
        }

        #pageLoading .page-loading-spinner {
            width: 45px;
            height: 45px;

            margin: 0 auto 14px;

            border: 4px solid rgba(71, 120, 185, .20);
            border-top-color: #4778b9;
            border-radius: 50%;

            animation: pageLoadingSpin .8s linear infinite;
        }

        #pageLoading .page-loading-title {
            text-align: center;
            color: #31577f;
            font-size: 14px;
            font-weight: 700;
        }

        #pageLoading .page-loading-description {
            text-align: center;
            color: #718096;
            font-size: 11px;
        }

        @keyframes pageLoadingSpin {
            to {
                transform: rotate(360deg);
            }
        }
        /* =========================================
           GLOBAL SIDEBAR
        ========================================= */

        .sidebar {
            width: 248px;
            min-width: 248px;
            min-height: 100vh;
            background: linear-gradient(180deg, #102a56, #153b75);
            color: #fff;
            padding: 24px 15px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 1000;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 10px 25px;
            border-bottom: 1px solid rgba(255,255,255,.10);
            margin-bottom: 15px;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 12px;
            background: #fff;
            color: #1769d2;
            display: grid;
            place-items: center;
            font-size: 20px;
            font-weight: 900;
        }

        .brand-info {
            min-width: 0;
        }

        .brand-info strong {
            display: block;
            font-size: 13px;
            letter-spacing: .3px;
            color: #fff;
        }

        .brand-info small {
            display: block;
            margin-top: 4px;
            font-size: 10px;
            color: #b9cdec;
            overflow-wrap: anywhere;
        }

        .brand-info .user-role {
            display: inline-block;
            margin-top: 6px;
            padding: 4px 8px;
            border-radius: 20px;
            background: rgba(255,255,255,.13);
            color: #dbeafe;
            font-size: 9px;
            font-weight: 700;
        }

        .nav-label {
            color: #89a8d4;
            font-size: 10px;
            letter-spacing: 1.3px;
            font-weight: 800;
            padding: 12px 12px 9px;
        }

        /* =========================================
           NAVIGATION
        ========================================= */

        .nav {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .nav > a,
        .nav-parent {
            width: 100%;
            min-height: 43px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 13px;

            border: 0;
            border-radius: 9px;
            background: transparent;

            color: #c7d7ef;
            text-decoration: none;
            text-align: left;
            font-size: 12px;
            font-weight: 500;

            cursor: pointer;

            transition:
                background-color .22s ease,
                color .22s ease;
        }

        .nav > a:hover,
        .nav-parent:hover {
            color: #fff;
            background: rgba(255,255,255,.10);
        }

        .nav > a.active,
        .nav-group.open > .nav-parent {
            color: #fff;
            background: rgba(255,255,255,.15);
            font-weight: 700;
        }

        .nav .ico {
            width: 20px;
            min-width: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .nav-group {
            width: 100%;
        }

        /* =========================================
           DROPDOWN
        ========================================= */

        .nav-parent .arrow {
            margin-left: auto;
            font-size: 13px;
            opacity: .8;
            transition: transform .3s ease;
        }

        .nav-group.open > .nav-parent .arrow {
            transform: rotate(180deg);
        }

        .submenu {
            display: block;
            max-height: 0;
            overflow: hidden;
            padding-left: 32px;
            opacity: 0;
            visibility: hidden;

            transition:
                max-height .35s ease,
                opacity .25s ease,
                padding .25s ease,
                visibility .25s ease;
        }

        .nav-group.open > .submenu {
            max-height: 600px;
            opacity: 1;
            visibility: visible;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .submenu a {
            display: block;
            position: relative;
            padding: 9px 11px;
            margin: 2px 0;

            color: #a9c1e3;
            text-decoration: none;
            font-size: 11px;
            font-weight: 500;

            border-radius: 7px;
            border-left: 2px solid transparent;

            transition:
                color .2s ease,
                background-color .2s ease,
                padding-left .2s ease,
                border-color .2s ease;
        }

        .submenu a:hover {
            color: #fff;
            background: rgba(255,255,255,.10);
            padding-left: 15px;
        }

        .submenu a.active {
            color: #fff;
            background: rgba(96,165,250,.16);
            border-left-color: #60a5fa;
            font-weight: 700;
            padding-left: 15px;
        }

        /* =========================================
           SIDEBAR BOTTOM
        ========================================= */

        .sidebar-bottom {
            margin-top: auto;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.10);
            border-radius: 12px;
            padding: 13px;
        }

        .sidebar-bottom strong {
            font-size: 12px;
        }

        .sidebar-bottom p {
            color: #c1d2eb;
            font-size: 10px;
            line-height: 1.6;
            margin: 7px 0 0;
        }

        /* =========================================
           LOGOUT
        ========================================= */

        .logout-menu {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 13px;

            border: 0;
            border-radius: 9px;
            background: transparent;
            color: #c7d7ef;

            font-size: 12px;
            text-align: left;
            cursor: pointer;

            transition: .2s ease;
        }

        .logout-menu:hover {
            color: #fff;
            background: rgba(239,68,68,.18);
        }

        /* =========================================
           MAIN CONTENT
        ========================================= */

        .app {
            min-height: 100vh;
            display: flex;
            width: 100%;
        }

        .main {
            flex: 1;
            min-width: 0;
            padding: 27px 30px 40px;
            background: #f4f7fc;
        }

        /* =========================================
           MOBILE MENU
        ========================================= */

        .mobile-menu-toggle {
            display: none;
            width: 40px;
            height: 40px;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            background: #fff;
            color: #102a56;
            font-size: 19px;
            cursor: pointer;
            margin-bottom: 15px;
        }

        .sidebar-overlay {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                width: 260px;
                min-width: 260px;
                height: 100vh;
                overflow-y: auto;

                transform: translateX(-100%);
                transition: transform .28s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay {
                display: block;
                position: fixed;
                inset: 0;
                z-index: 999;
                background: rgba(15,23,42,.45);
                opacity: 0;
                visibility: hidden;
                transition: .25s ease;
            }

            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
            }

            .main {
                width: 100%;
                padding: 18px 14px 30px;
            }

            .mobile-menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .submenu,
            .submenu a,
            .nav-parent,
            .nav-parent .arrow,
            .sidebar,
            .sidebar-overlay {
                transition: none !important;
            }
        }
    </style>
</head>

<body>

<div class="app">

    {{-- =========================================
         SIDEBAR
    ========================================= --}}

    <aside class="sidebar" id="sidebar">

        {{-- BRAND --}}
        <div class="brand">

            <div class="brand-info">

                <strong>APLIKASI ATK MDI</strong>

                <small>
                    Login As {{ auth()->user()->user ?? 'Guest' }}
                </small>

                <small class="user-role">
                    {{ auth()->user()->getRoleNames()->first() ?? 'Tanpa Role' }} - {{ auth()->user()->branch?->nama_cabang ?? 'Semua Cabang / Pusat' }}
                </small>

            </div>

        </div>


        <div class="nav-label">MENU UTAMA</div>

        <nav class="nav">

            {{-- PANDUAN --}}
            <a href="{{ url('/panduan') }}"
               class="{{ request()->is('panduan') ? 'active' : '' }}">

                <span class="ico">ⓘ</span>
                <span>Panduan & FAQ</span>

            </a>


            {{-- DASHBOARD --}}
            @can('dashboard.view')

            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

                <span class="ico">▦</span>
                <span>Dashboard</span>

            </a>

            @endcan


            {{-- =====================================
                 DATA BARANG
            ====================================== --}}

            @canany([
                'barang.view',
                'penerimaan.view',
                'pengeluaran.view',
                'stock-card.view',
                'stok-opname.view'
            ])

            @php
                $dataBarangAktif =
                    request()->is('databarang/*') ||
                    request()->is('pengeluaran*') ||
                    request()->is('kartu-stok*') ||
                    request()->is('stok-opname*');
            @endphp

            <div class="nav-group {{ $dataBarangAktif ? 'open' : '' }}">

                <button
                    class="nav-parent {{ $dataBarangAktif ? 'active' : '' }}"
                    type="button"
                    aria-expanded="{{ $dataBarangAktif ? 'true' : 'false' }}"
                >

                    <span class="ico">▤</span>
                    <span>Data Barang</span>
                    <span class="arrow">⌄</span>

                </button>

                <div class="submenu">

                    {{-- KELOLA BARANG KHUSUS SUPER ADMIN / IT --}}
                    @hasanyrole('Super Admin|IT Cabang')

                        @can('barang.view')

                        <a href="{{ url('/databarang/kelola-barang') }}"
                           class="{{ request()->is('databarang/kelola-barang') ? 'active' : '' }}">

                            Kelola Barang

                        </a>

                        @endcan

                    @endhasanyrole


                    {{-- MASTER BARANG --}}
                    @can('barang.view')

                    <a href="{{ route('barangs.index') }}"
                       class="{{ request()->routeIs('barangs.*') ? 'active' : '' }}">

                        Master Barang

                    </a>

                    @endcan


                    {{-- PENERIMAAN --}}
                    @can('penerimaan.view')

                    <a href="{{ route('databarang.penerimaan') }}"
                       class="{{ request()->is('databarang/penerimaan-barang*') ? 'active' : '' }}">

                        Penerimaan Barang

                    </a>

                    @endcan


                    {{-- PENGELUARAN --}}
                    @can('pengeluaran.view')

                    <a href="{{ route('pengeluaran.index') }}"
                       class="{{ request()->is('pengeluaran*') ? 'active' : '' }}">

                        Pengeluaran Barang

                    </a>

                    @endcan


                    {{-- STOCK CARD --}}
                    @can('stock-card.view')

                    <a href="{{ route('stock-card.index') }}"
                       class="{{ request()->is('kartu-stok*') ? 'active' : '' }}">

                        Stock Card

                    </a>

                    @endcan


                    {{-- STOCK OPNAME --}}
                    @can('stok-opname.view')

                    <a href="{{ route('stok-opname.index') }}"
                       class="{{ request()->is('stok-opname*') ? 'active' : '' }}">

                        Stock Opname

                    </a>

                    @endcan

                </div>

            </div>

            @endcanany


            {{-- =====================================
                 SUPPLIER
            ====================================== --}}

            @can('supplier.view')

            @php
                $supplierAktif = request()->routeIs('supplier.*');
            @endphp

            <div class="nav-group {{ $supplierAktif ? 'open' : '' }}">

                <button
                    class="nav-parent {{ $supplierAktif ? 'active' : '' }}"
                    type="button"
                    aria-expanded="{{ $supplierAktif ? 'true' : 'false' }}"
                >

                    <span class="ico">♧</span>
                    <span>Supplier</span>
                    <span class="arrow">⌄</span>

                </button>

                <div class="submenu">

                    <a href="{{ route('supplier.index') }}"
                       class="{{ $supplierAktif ? 'active' : '' }}">

                        Manage Supplier

                    </a>

                </div>

            </div>

            @endcan


            {{-- =====================================
                 LAPORAN
            ====================================== --}}

            @can('laporan.view')

            @php
                $laporanAktif = request()->routeIs('laporan.*');
            @endphp

            <div class="nav-group {{ $laporanAktif ? 'open' : '' }}">

                <button
                    class="nav-parent {{ $laporanAktif ? 'active' : '' }}"
                    type="button"
                    aria-expanded="{{ $laporanAktif ? 'true' : 'false' }}"
                >

                    <span class="ico">▧</span>
                    <span>Laporan</span>
                    <span class="arrow">⌄</span>

                </button>

                <div class="submenu">

                    <a href="{{ route('laporan.index') }}"
                       class="{{ request()->routeIs('laporan.index') ? 'active' : '' }}">

                        Laporan Rekapitulasi

                    </a>

                    <a href="{{ route('laporan.penerimaan') }}"
                       class="{{ request()->routeIs('laporan.penerimaan') ? 'active' : '' }}">

                        Histori Penerimaan

                    </a>

                    <a href="{{ route('laporan.pengeluaran') }}"
                       class="{{ request()->routeIs('laporan.pengeluaran') ? 'active' : '' }}">

                        Histori Pengeluaran

                    </a>

                    <a href="{{ route('laporan.penyesuaian') }}"
                       class="{{ request()->routeIs('laporan.penyesuaian') ? 'active' : '' }}">

                        Histori Penyesuaian Stok

                    </a>

                </div>

            </div>

            @endcan


            {{-- =====================================
                 MANAJEMEN USER
            ====================================== --}}

            @hasanyrole('Super Admin|IT Cabang')

                @can('user.view')

                @php
                    $userAktif = request()->is('create-user*');
                @endphp

                <div class="nav-group {{ $userAktif ? 'open' : '' }}">

                    <button
                        class="nav-parent {{ $userAktif ? 'active' : '' }}"
                        type="button"
                        aria-expanded="{{ $userAktif ? 'true' : 'false' }}"
                    >

                        <span class="ico">♙</span>
                        <span>Manajemen User</span>
                        <span class="arrow">⌄</span>

                    </button>

                    <div class="submenu">

                        <a href="{{ route('create-user') }}"
                           class="{{ $userAktif ? 'active' : '' }}">

                            Manage User

                        </a>

                    </div>

                </div>

                @endcan

            @endhasanyrole

        </nav>


        {{-- =========================================
             PENGATURAN DAN LOGOUT
        ========================================= --}}

        <div class="nav-label">PENGATURAN</div>

        <form action="{{ route('logout') }}" method="POST">

            @csrf

            <nav class="nav">

                <button type="submit" class="logout-menu">

                    <span class="ico">↪</span>
                    <span>Sign Out</span>

                </button>

            </nav>

        </form>


        {{-- TIPS --}}
        <div class="sidebar-bottom">

            <strong>💡 Tips Persediaan</strong>

            <p>
                Periksa stok secara berkala agar kebutuhan alat tulis
                kantor selalu tersedia.
            </p>

        </div>

    </aside>


    {{-- OVERLAY MOBILE --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>


    {{-- =========================================
         MAIN CONTENT
    ========================================= --}}

    <main class="main">

        {{-- TOMBOL MENU MOBILE --}}
        <button
            type="button"
            class="mobile-menu-toggle"
            id="mobileMenuToggle"
            aria-label="Buka menu navigasi"
        >
            ☰
        </button>


        {{-- HEADER --}}
        <div class="page-header">
            @yield('header')
        </div>


        {{-- CONTENT --}}
        <div class="page-content">
            @yield('container')
        </div>


    </main>
<!-- GLOBAL PAGE LOADING -->
<div class="page-loading" id="pageLoading">
    <div class="page-loading-content">

        <div class="page-loading-spinner"></div>

        <div class="page-loading-title">
            Memproses...
        </div>

    </div>
</div>
</div>


{{-- =========================================
     JAVASCRIPT
========================================= --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mobileToggle = document.getElementById('mobileMenuToggle');

    const navGroups = document.querySelectorAll('.nav-group');


    /* =====================================
       DROPDOWN MENU
    ===================================== */

    navGroups.forEach(function (group) {

        const button = group.querySelector('.nav-parent');

        if (!button) return;

        button.addEventListener('click', function () {

            const isOpen = group.classList.contains('open');

            // Tutup dropdown lainnya
            navGroups.forEach(function (otherGroup) {

                otherGroup.classList.remove('open');

                const otherButton =
                    otherGroup.querySelector('.nav-parent');

                if (otherButton) {
                    otherButton.setAttribute(
                        'aria-expanded',
                        'false'
                    );
                }

            });

            // Buka dropdown yang dipilih
            if (!isOpen) {

                group.classList.add('open');

                button.setAttribute(
                    'aria-expanded',
                    'true'
                );

            }

        });

    });


    /* =====================================
       MOBILE SIDEBAR
    ===================================== */

    function openSidebar() {

        sidebar.classList.add('open');
        overlay.classList.add('show');

        document.body.style.overflow = 'hidden';

    }

    function closeSidebar() {

        sidebar.classList.remove('open');
        overlay.classList.remove('show');

        document.body.style.overflow = '';

    }


    if (mobileToggle) {

        mobileToggle.addEventListener('click', function () {

            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }

        });

    }


    if (overlay) {

        overlay.addEventListener('click', closeSidebar);

    }


    // Tutup sidebar ketika link diklik di perangkat mobile
    sidebar.querySelectorAll('.submenu a, .nav > a').forEach(function (link) {

        link.addEventListener('click', function () {

            if (window.innerWidth <= 768) {
                closeSidebar();
            }

        });

    });


    // Tutup sidebar dengan tombol Escape
    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {
            closeSidebar();
        }

    });


    // Reset sidebar ketika layar kembali ke desktop
    window.addEventListener('resize', function () {

        if (window.innerWidth > 768) {
            closeSidebar();
        }

    });

});
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const loading = document.getElementById('pageLoading');

    if (!loading) return;

    let isNavigating = false;

    function showLoading(message = 'Memproses...') {
        if (isNavigating) return;

        isNavigating = true;

        const title = loading.querySelector('.page-loading-title');

        if (title) {
            title.textContent = message;
        }

        loading.classList.add('active');
    }

    function hideLoading() {
        isNavigating = false;
        loading.classList.remove('active');
    }

    // =================================
    // 1. KLIK LINK / NAVIGASI HALAMAN
    // =================================

    document.addEventListener('click', function (event) {

        const link = event.target.closest('a');

        if (!link) return;

        // Abaikan link yang bukan navigasi halaman
        if (
            event.defaultPrevented ||
            event.button !== 0 ||
            event.ctrlKey ||
            event.shiftKey ||
            event.altKey ||
            event.metaKey ||
            link.target === '_blank' ||
            link.hasAttribute('download') ||
            link.dataset.noLoading !== undefined
        ) {
            return;
        }

        const href = link.getAttribute('href');

        if (
            !href ||
            href === '#' ||
            href.startsWith('#') ||
            href.startsWith('javascript:') ||
            href.startsWith('mailto:') ||
            href.startsWith('tel:')
        ) {
            return;
        }

        const tujuan = new URL(link.href, window.location.href);

        // Abaikan link ke domain lain
        if (tujuan.origin !== window.location.origin) {
            return;
        }

        // Abaikan link ke halaman yang sama
        if (
            tujuan.pathname === window.location.pathname &&
            tujuan.search === window.location.search &&
            tujuan.hash !== ''
        ) {
            return;
        }

        if (isNavigating) {
            event.preventDefault();
            return;
        }

        showLoading('Membuka halaman...');

    });


    // =================================
    // 2. SUBMIT FORM
    // =================================

    document.addEventListener('submit', function (event) {

        const form = event.target;

        if (!(form instanceof HTMLFormElement)) return;

        // Form yang dikecualikan
        if (
            form.dataset.noLoading !== undefined ||
            form.target === '_blank'
        ) {
            return;
        }

        // Form AJAX tidak perlu loading navigasi
        if (
            form.dataset.ajax !== undefined
        ) {
            return;
        }

        if (isNavigating) {
            event.preventDefault();
            return;
        }

        const submitButton = event.submitter;

        if (submitButton) {
            submitButton.disabled = true;

            if (submitButton.tagName === 'BUTTON') {
                submitButton.dataset.originalHtml =
                    submitButton.innerHTML;

                submitButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
            }
        }

        showLoading('Menyimpan data...');

    });


    // =================================
    // 3. BROWSER BACK / FORWARD
    // =================================

    window.addEventListener('pageshow', function () {
        hideLoading();
    });

    // =================================
    // 4. RESET JIKA NAVIGASI GAGAL
    // =================================

    window.addEventListener('pagehide', function () {
        // Browser sedang meninggalkan halaman.
        // Tidak perlu menyembunyikan overlay.
    });

});
</script>
</body>
</html>
