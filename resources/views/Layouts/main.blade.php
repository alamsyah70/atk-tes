<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ATK Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
  <style>

    .nav > a {
        border: 0;
        color: #c7d7ef;
        background: transparent;
        padding: 12px 13px;
        text-align: left;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        text-decoration: none;
        transition: .2s;
    }

    .nav > a:hover,
    .nav > a.active {
        color: #fff;
        background: rgba(255,255,255,.14);
    }


    /* Menu aktif */
    .submenu a.active {
        color: #fff;
        background: rgba(255,255,255,.16);
        border-left: 3px solid #60a5fa;
        font-weight: 700;
        padding-left: 12px;
    }

    .nav-parent.active {
        color: #fff;
        background: rgba(255,255,255,.14);
    }

    .nav > a.active {
        color: #fff;
        background: rgba(255,255,255,.14);
    }

    :root {
      --navy: #102a56;
      --blue: #1769d2;
      --blue2: #2f8cff;
      --pale: #eaf3ff;
      --bg: #f4f7fc;
      --text: #1d2b43;
      --muted: #7c8ba3;
      --line: #e7edf5;
      --white: #fff;
      --green: #16a36a;
      --orange: #f0a12b;
      --red: #e05252;
      --shadow: 0 8px 24px rgba(23, 62, 116, .07);
    }
    
    /* Tombol induk submenu */
    .nav-parent {
        width: 100%;
        border: none;
        color: #c7d7ef;
        background: transparent;
        padding: 12px 13px;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        text-align: left;
        font-size: 13px;
    }

    .nav-parent:hover,
    .nav-group.open .nav-parent {
        color: white;
        background: rgba(255,255,255,.14);
    }

    /* Panah dropdown */
    .arrow {
        margin-left: auto;
        transition: transform .2s;
    }

    .nav-group.open .arrow {
        transform: rotate(180deg);
    }

    /* Isi submenu */
    .submenu {
        display: none;
        padding: 5px 0 5px 43px;
    }

    .nav-group.open .submenu {
        display: grid;
        gap: 4px;
    }

    .submenu a {
        display: block;
        text-decoration: none;
        color: #a9c1e3;
        padding: 9px 10px;
        font-size: 11px;
        border-radius: 7px;
        transition: .2s;
    }

    .submenu a:hover {
        color: white;
        background: rgba(255,255,255,.12);
    }

    * { box-sizing: border-box; }
    body { margin: 0; font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--bg); color: var(--text); }
    button, input, select { font: inherit; }
    .app { min-height: 100vh; display: flex; }
    .sidebar { width: 248px; background: linear-gradient(180deg, #102a56, #153b75); color: #fff; padding: 26px 16px; display: flex; flex-direction: column; flex-shrink: 0; }
    .brand { display: flex; align-items: center; gap: 12px; padding: 0 10px 30px; }
    .brand-mark { width: 42px; height: 42px; border-radius: 13px; background: #fff; color: var(--blue); display: grid; place-items: center; font-size: 21px; font-weight: 900; }
    .brand strong { display: block; font-size: 17px; letter-spacing: .4px; }
    .brand small { color: #b9cdec; font-size: 11px; }
    .nav-label { color: #89a8d4; font-size: 10px; letter-spacing: 1.5px; font-weight: 800; padding: 12px 12px 9px; }
    .nav { display: grid; gap: 6px; }
    .nav button { border: 0; color: #c7d7ef; background: transparent; padding: 12px 13px; text-align: left; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 12px; font-size: 13px; transition: .2s; }
    .nav button:hover, .nav button.active { color: #fff; background: rgba(255,255,255,.14); }
    .nav .ico { width: 19px; text-align: center; font-size: 16px; }
    .sidebar-bottom { margin-top: auto; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.1); border-radius: 13px; padding: 14px; }
    .sidebar-bottom strong { font-size: 12px; }
    .sidebar-bottom p { color: #c1d2eb; font-size: 11px; line-height: 1.6; margin: 7px 0 0; }
    .main { flex: 1; min-width: 0; padding: 27px 32px 40px; }
    .topbar { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin-bottom: 28px; }
    .welcome h1 { margin: 0; font-size: 24px; color: var(--navy); }
    .welcome p { margin: 6px 0 0; color: var(--muted); font-size: 12px; }
    .top-actions { display: flex; align-items: center; gap: 12px; }
    .search { position: relative; }
    .search input { width: 235px; border: 1px solid var(--line); background: #fff; border-radius: 10px; padding: 11px 14px 11px 36px; outline: none; font-size: 12px; }
    .search span { position: absolute; left: 13px; top: 9px; color: #8b9ab0; }
    .icon-btn { border: 1px solid var(--line); background: #fff; width: 39px; height: 39px; border-radius: 10px; cursor: pointer; color: var(--navy); }
    .profile { display: flex; align-items: center; gap: 9px; padding-left: 5px; }
    .avatar { width: 37px; height: 37px; display: grid; place-items: center; border-radius: 50%; background: #dceaff; color: var(--blue); font-weight: 800; font-size: 12px; }
    .profile strong { display: block; font-size: 12px; }
    .profile small { color: var(--muted); font-size: 10px; }
    .section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .section-head h2 { margin: 0; font-size: 15px; color: var(--navy); }
    .section-head p { margin: 4px 0 0; color: var(--muted); font-size: 11px; }
    .btn { border: 0; background: var(--blue); color: #fff; padding: 10px 14px; border-radius: 9px; cursor: pointer; font-size: 11px; font-weight: 700; box-shadow: 0 5px 12px rgba(23,105,210,.18); }
    .btn:hover { background: #1059b7; }
    .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 24px; }
    .stat { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 17px; box-shadow: var(--shadow); }
    .stat-top { display: flex; justify-content: space-between; align-items: center; }
    .stat-label { color: var(--muted); font-size: 11px; }
    .stat-icon { width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; background: var(--pale); color: var(--blue); }
    .stat h3 { margin: 13px 0 5px; font-size: 25px; color: var(--navy); }
    .stat small { color: var(--muted); font-size: 10px; }
    .stat small b { color: var(--green); }
    .content-grid { display: grid; grid-template-columns: 1.55fr 1fr; gap: 17px; margin-bottom: 20px; }
    .panel { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 19px; box-shadow: var(--shadow); min-width: 0; }
    .panel-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .panel-head h3 { margin: 0; color: var(--navy); font-size: 13px; }
    .panel-head span { color: var(--muted); font-size: 10px; }
    .chart-wrap { height: 205px; position: relative; }
    canvas { width: 100%; height: 100%; }
    .legend { display: flex; gap: 16px; color: var(--muted); font-size: 10px; margin-top: 8px; }
    .dot { width: 7px; height: 7px; display: inline-block; border-radius: 50%; margin-right: 5px; }
    .stock-list { display: grid; gap: 16px; }
    .stock-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
    .stock-info { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .product-icon { width: 35px; height: 35px; border-radius: 10px; display: grid; place-items: center; background: #edf4ff; font-size: 16px; }
    .stock-info strong { display: block; font-size: 11px; }
    .stock-info small { display: block; color: var(--muted); font-size: 9px; margin-top: 3px; }
    .stock-count { font-size: 11px; font-weight: 800; white-space: nowrap; }
    .low { color: var(--red); }
    .table-panel { padding: 0; overflow: hidden; }
    .table-panel .panel-head { padding: 18px 19px 10px; margin: 0; }
    .table-tools { display: flex; gap: 8px; align-items: center; }
    .table-tools input, .table-tools select { border: 1px solid var(--line); border-radius: 8px; padding: 8px 10px; color: var(--text); font-size: 10px; outline: none; background: #fff; }
    .table-scroll { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; white-space: nowrap; }
    th { background: #f8faff; color: #8291a8; font-size: 9px; letter-spacing: .5px; text-align: left; padding: 12px 19px; }
    td { padding: 13px 19px; border-top: 1px solid #f0f3f8; font-size: 10px; }
    td strong { font-size: 10px; }
    .badge { display: inline-block; padding: 5px 8px; border-radius: 20px; font-size: 9px; font-weight: 700; }
    .badge.ok { color: #11865a; background: #e6f7ef; }
    .badge.warn { color: #b97812; background: #fff4dc; }
    .badge.out { color: #c64040; background: #ffeded; }
    .action { border: 0; background: transparent; color: var(--blue); cursor: pointer; font-size: 10px; font-weight: 700; }
    .empty { text-align: center; color: var(--muted); padding: 28px; }
    .footer { text-align: center; color: #9aa7b9; font-size: 10px; margin-top: 22px; }
    .mobile-menu { display: none; }
    .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(9,27,54,.45); z-index: 10; align-items: center; justify-content: center; padding: 15px; }
    .modal-backdrop.show { display: flex; }
    .modal { background: #fff; border-radius: 16px; padding: 22px; width: min(420px, 100%); box-shadow: 0 20px 60px rgba(0,0,0,.2); }
    .modal h3 { margin: 0 0 16px; color: var(--navy); }
    .form-group { margin-bottom: 12px; }
    .form-group label { display: block; font-size: 11px; font-weight: 700; margin-bottom: 6px; }
    .form-group input, .form-group select { width: 100%; border: 1px solid var(--line); border-radius: 8px; padding: 10px; outline: none; font-size: 12px; }
    .modal-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 18px; }
    .btn-light { background: #edf3fb; color: var(--navy); box-shadow: none; }
    @media (max-width: 1050px) { .stats { grid-template-columns: repeat(2,1fr); } .content-grid { grid-template-columns: 1fr; } }
    @media (max-width: 720px) {
      .sidebar { position: fixed; z-index: 5; left: -260px; top: 0; bottom: 0; transition: .25s; }
      .sidebar.open { left: 0; }
      .main { padding: 20px 15px 30px; width: 100%; }
      .mobile-menu { display: inline-grid; place-items: center; }
      .topbar { align-items: flex-start; }
      .welcome h1 { font-size: 19px; }
      .top-actions { gap: 7px; }
      .search { display: none; }
      .profile div:last-child { display: none; }
      .stats { gap: 9px; }
      .stat { padding: 13px; }
      .stat h3 { font-size: 21px; }
      .section-head { align-items: flex-start; gap: 10px; }
      .section-head .btn { white-space: nowrap; }
      .table-panel .panel-head { flex-wrap: wrap; gap: 12px; }
    }
  </style>

</head>
<body>
<div class="app">
  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <div><strong>APLIKASI ATK MDI</strong>
      <small>Login As {{ auth()->user()->user ?? 'Guest' }}</small>
    </div>
    </div>
    <div class="nav-label">MENU UTAMA</div>
    

    <nav class="nav">
    {{-- DASHBOARD --}}
<a href="{{ url('/panduan') }}"
   class="{{ request()->is('panduan') ? '' : '' }}">
    <span class="ico">▦</span>
    Panduan
</a>

{{-- DASHBOARD --}}
<a href="{{ url('/dashboard') }}"
   class="{{ request()->is('dashboard') ? 'active' : '' }}">
    <span class="ico">▦</span>
    Dashboard
</a>

{{-- DATA BARANG --}}
<div class="nav-group {{ request()->is('databarang/*') || request()->is('pengeluaran*') ? 'open' : '' }}">
    <button class="nav-parent {{ request()->is('databarang/*') || request()->is('pengeluaran*') ? 'active' : '' }}"
            type="button">
        <span class="ico">▤</span>
        Data Barang
        <span class="arrow">⌄</span>
    </button>

    <div class="submenu">
    @hasrole('Super Admin')
        <a href="/databarang/kelola-barang"
           class="{{ request()->is('databarang/kelola-barang') ? 'active' : '' }}">
            Kelola Barang
        </a>
    @endhasrole
        <a href="/databarang/penerimaan-barang"
           class="{{ request()->is('databarang/penerimaan-barang') ? 'active' : '' }}">
            Penerimaan Barang
        </a>

        <a href="/pengeluaran"
           class="{{ request()->is('pengeluaran*') ? 'active' : '' }}">
            Pengeluaran Barang
        </a>
    </div>
</div>

{{-- SUPPLIER --}}
@can('supplier.view')
<div class="nav-group {{ request()->routeIs('supplier.*') ? 'open' : '' }}">

    <button class="nav-parent {{ request()->routeIs('supplier.*') ? 'active' : '' }}"
            type="button">
        <span class="ico">▤</span>
        Supplier
        <span class="arrow">⌄</span>
    </button>

    <div class="submenu">
        <a href="{{ route('supplier.index') }}"
           class="{{ request()->routeIs('supplier.index') ? 'active' : '' }}">
            Manage Supplier
        </a>
    </div>
</div>
@endcan

@can('laporan.view')
<div class="nav-group">
    <button class="nav-parent" type="button">
        <span class="ico">▤</span>
        Laporan
        <span class="arrow">⌄</span>
    </button>

    <div class="submenu">
        <a href="{{ route('laporan.penerimaan') }}"
           class="{{ request()->routeIs('laporan.penerimaan*') ? 'active' : '' }}">
            Laporan Penerimaan Barang
        </a>

        <a href="{{ route('laporan.pengeluaran') }}"
           class="{{ request()->routeIs('laporan.pengeluaran*') ? 'active' : '' }}">
            Laporan Pengeluaran Barang
        </a>
    </div>
</div>
@endcan

{{-- MANAGE USER --}}
@hasrole('Super Admin')
<div class="nav-group {{ request()->is('create-user*') ? 'open' : '' }}">

    <button class="nav-parent {{ request()->is('create-user*') ? 'active' : '' }}"
            type="button">
        <span class="ico">♙</span>
        User
        <span class="arrow">⌄</span>
    </button>

    <div class="submenu">
        <a href="{{ route('create-user') }}"
           class="{{ request()->is('create-user*') ? 'active' : '' }}">
            Manage User
        </a>
    </div>
</div>
@endhasrole

</nav>


    <div class="nav-label">PENGATURAN</div>
    <form action="/logout" method="POST">
            @csrf
            <nav class="nav"><button type="submit" data-page="Pengaturan"><span class="ico">⚙</span>Sign Out</button></nav>
        </form>
    <div class="sidebar-bottom"><strong>💡 Tips Persediaan</strong><p>Periksa stok secara berkala agar kebutuhan alat tulis kantor selalu tersedia.</p></div>
  </aside>

  <main class="main">
    <div>
        @yield('header')
    </div>
    <div>
        @yield('container')
    </div>
<script>
    
    document.querySelectorAll('.nav-parent').forEach(button => {
        button.addEventListener('click', function () {

            const parent = this.closest('.nav-group');

            // Tutup submenu lainnya
            document.querySelectorAll('.nav-group').forEach(group => {
                if (group !== parent) {
                    group.classList.remove('open');
                }
            });

            // Buka/tutup submenu yang diklik
            parent.classList.toggle('open');
        });
    });

  const products = [
    {code:"ATK001",name:"Pulpen Standard",category:"Alat Tulis",stock:120,location:"Rak A1"},
    {code:"ATK002",name:"Pensil 2B",category:"Alat Tulis",stock:85,location:"Rak A1"},
    {code:"ATK003",name:"Kertas A4 80 GSM",category:"Kertas",stock:240,location:"Rak B1"},
    {code:"ATK004",name:"Buku Tulis",category:"Kertas",stock:8,location:"Rak B2"},
    {code:"ATK005",name:"Map Folder",category:"Arsip",stock:6,location:"Rak C1"},
    {code:"ATK006",name:"Stapler",category:"Perlengkapan",stock:35,location:"Rak C2"},
    {code:"ATK007",name:"Tinta Printer",category:"Elektronik",stock:18,location:"Rak D1"},
    {code:"ATK008",name:"Penghapus",category:"Alat Tulis",stock:772,location:"Rak A2"}
  ];
  const $ = id => document.getElementById(id);
  function format(n){ return new Intl.NumberFormat("id-ID").format(n); }
  function status(stock){ return stock === 0 ? '<span class="badge out">Habis</span>' : stock <= 10 ? '<span class="badge warn">Menipis</span>' : '<span class="badge ok">Tersedia</span>'; }
  function renderStats(){
    $("totalItems").textContent = format(products.length);
    $("totalStock").textContent = format(products.reduce((s,p)=>s+p.stock,0));
    $("lowStock").textContent = products.filter(p=>p.stock<=10).length;
    $("totalCategories").textContent = new Set(products.map(p=>p.category)).size;
  }
  function renderTable(){
    const q = ($("tableSearch").value || $("globalSearch").value).toLowerCase();
    const cat = $("categoryFilter").value;
    const filtered = products.filter(p=>(p.name.toLowerCase().includes(q)||p.code.toLowerCase().includes(q))&&(!cat||p.category===cat));
    $("productRows").innerHTML = filtered.length ? filtered.map(p=>`<tr>
      <td>${p.code}</td><td><strong>${p.name}</strong></td><td>${p.category}</td><td><strong>${format(p.stock)}</strong> unit</td><td>${p.location}</td><td>${status(p.stock)}</td>
      <td><button class="action" onclick="editStock('${p.code}')">Ubah stok</button></td></tr>`).join("") : '<tr><td colspan="7" class="empty">Barang tidak ditemukan.</td></tr>';
  }
  function renderLowStock(){
    const low = products.filter(p=>p.stock<=10).sort((a,b)=>a.stock-b.stock);
    $("lowStockList").innerHTML = low.length ? low.map(p=>`<div class="stock-row"><div class="stock-info"><div class="product-icon">📦</div><div><strong>${p.name}</strong><small>${p.code} · ${p.location}</small></div></div><span class="stock-count low">${p.stock} unit</span></div>`).join("") : '<p style="font-size:11px;color:#16a36a">Semua stok dalam kondisi aman 🎉</p>';
  }
  function drawChart(){
    const canvas = $("stockChart"), rect = canvas.getBoundingClientRect(), dpr = window.devicePixelRatio || 1;
    canvas.width = rect.width*dpr; canvas.height = rect.height*dpr;
    const ctx = canvas.getContext("2d"); ctx.scale(dpr,dpr);
    const w=rect.width,h=rect.height, pad={l:28,r:10,t:12,b:28};
    const labels=["Mei","Jun","Jul","Agu","Sep","Okt","Nov"], masuk=[55,78,62,95,72,110,88], keluar=[35,48,42,61,55,74,59], max=120;
    ctx.font="10px Arial"; ctx.textAlign="right"; ctx.fillStyle="#8a99ad"; ctx.strokeStyle="#edf1f6"; ctx.lineWidth=1;
    for(let i=0;i<=4;i++){const y=pad.t+(h-pad.t-pad.b)*i/4;ctx.beginPath();ctx.moveTo(pad.l,y);ctx.lineTo(w-pad.r,y);ctx.stroke();ctx.fillText(String(max-i*30),pad.l-8,y+3);}
    const slot=(w-pad.l-pad.r)/labels.length, barW=Math.min(13,slot*.25);
    labels.forEach((lab,i)=>{const cx=pad.l+slot*i+slot/2;[[masuk[i],"#1769d2",-barW/2],[keluar[i],"#8fc3ff",barW/2]].forEach(([v,color,off])=>{const bh=(v/max)*(h-pad.t-pad.b);ctx.fillStyle=color;ctx.beginPath();ctx.roundRect(cx+off,pad.t+(h-pad.t-pad.b)-bh,barW,bh,4);ctx.fill();});ctx.textAlign="center";ctx.fillStyle="#8a99ad";ctx.fillText(lab,cx,h-8);});
  }
  function showToast(msg){const t=$("toast");t.textContent=msg;t.style.opacity=1;t.style.transform="translateY(0)";clearTimeout(window.toastTimer);window.toastTimer=setTimeout(()=>{t.style.opacity=0;t.style.transform="translateY(10px)"},2300);}
  function editStock(code){const p=products.find(x=>x.code===code);const val=prompt(`Masukkan stok baru untuk ${p.name}:`,p.stock);if(val!==null&&val.trim()!==""&&Number.isInteger(Number(val))&&Number(val)>=0){p.stock=Number(val);refresh();showToast("Stok berhasil diperbarui");}}
  function refresh(){renderStats();renderTable();renderLowStock();}
  $("addBtn").addEventListener("click",()=>{$("modalBackdrop").classList.add("show");$("itemCode").focus();});
  $("cancelBtn").addEventListener("click",()=>{$("modalBackdrop").classList.remove("show");});
  $("modalBackdrop").addEventListener("click",e=>{if(e.target===$("modalBackdrop"))$("modalBackdrop").classList.remove("show");});
  $("productForm").addEventListener("submit",e=>{
    e.preventDefault();
    const code=$("itemCode").value.trim().toUpperCase();
    if(products.some(p=>p.code===code)){showToast("Kode barang sudah digunakan");return;}
    products.push({code,name:$("itemName").value.trim(),category:$("itemCategory").value,stock:Number($("itemStock").value),location:$("itemLocation").value.trim()});
    refresh();e.target.reset();$("modalBackdrop").classList.remove("show");showToast("Barang berhasil ditambahkan");
  });
  $("tableSearch").addEventListener("input",renderTable);$("globalSearch").addEventListener("input",renderTable);$("categoryFilter").addEventListener("change",renderTable);
  $("menuToggle").addEventListener("click",()=>$("sidebar").classList.toggle("open"));
  document.querySelectorAll(".nav button").forEach(btn=>btn.addEventListener("click",()=>{
    document.querySelectorAll(".nav button").forEach(b=>b.classList.remove("active"));btn.classList.add("active");
    $("pageTitle").textContent=btn.dataset.page;
    if(btn.dataset.page!=="Dashboard")showToast(`Menu ${btn.dataset.page} dipilih — contoh navigasi dashboard`);
    $("sidebar").classList.remove("open");
  }));
  window.addEventListener("resize",drawChart);
  refresh();drawChart();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>