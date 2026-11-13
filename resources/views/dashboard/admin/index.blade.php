@extends('Layouts.main')

@section('container')
<section class="section-head">
      <div><h2>Ringkasan Inventaris</h2><p>Pantau kondisi persediaan barang secara real-time di halaman ini.</p></div>
      <!-- <button class="btn" id="addBtn">＋ Tambah Barang</button> -->
    </section>

    
    <section class="stats">

    {{-- Total Jenis Barang --}}
    <div class="stat">
        <div class="stat-top">
            <span class="stat-label">Total Jenis Barang</span>
            <span class="stat-icon">▤</span>
        </div>
        <h3 id="totalItems">
            {{ number_format($totalItems, 0, ',', '.') }}
        </h3>
        <small>Seluruh item terdaftar</small>
    </div>

    {{-- Total Stok --}}
    <div class="stat">
        <div class="stat-top">
            <span class="stat-label">Total Stok Tersedia</span>
            <span class="stat-icon">▣</span>
        </div>
        <h3 id="totalStock">
            {{ number_format($totalStock, 0, ',', '.') }}
        </h3>
        <small>Akumulasi seluruh barang</small>
    </div>

    {{-- Stok Menipis --}}
    <div class="stat">
        <div class="stat-top">
            <span class="stat-label">Stok Menipis</span>
            <span class="stat-icon" style="background:#fff3df;color:#e39a24">⚠</span>
        </div>
        <h3 id="lowStock">
            {{ number_format($lowStock, 0, ',', '.') }}
        </h3>
        <small>Perlu segera dilakukan restock</small>
    </div>

    {{-- Total Kategori --}}
    <div class="stat">
        <div class="stat-top">
            <span class="stat-label">Total Kategori</span>
            <span class="stat-icon" style="background:#e8f8f1;color:#16a36a">▧</span>
        </div>
        <h3 id="totalCategories">
            {{ number_format($totalCategories, 0, ',', '.') }}
        </h3>
        <small>Kategori barang aktif</small>
    </div>

    </section>


    <section class="content-grid">
      <div class="panel">
        <div class="panel-head"><h3>Statistik Barang Masuk & Keluar</h3><span>7 bulan terakhir</span></div>
        <div class="chart-wrap"><canvas id="stockChart"></canvas></div>
        <div class="legend"><span><i class="dot" style="background:#1769d2"></i>Barang Masuk</span><span><i class="dot" style="background:#8fc3ff"></i>Barang Keluar</span></div>
      </div>
      <div class="panel">
        <div class="panel-head"><h3>Perlu Perhatian</h3><span>Stok ≤ 10 unit</span></div>
        <div class="stock-list" id="lowStockList"></div>
      </div>
    </section>

    <section class="panel table-panel">
      <div class="panel-head">
        <div><h3>Daftar Barang</h3><span>Kelola seluruh persediaan alat tulis kantor</span></div>
        <div class="table-tools"><input id="tableSearch" placeholder="Cari nama barang..." /><select id="categoryFilter"><option value="">Semua kategori</option><option>Alat Tulis</option><option>Kertas</option><option>Arsip</option><option>Perlengkapan</option><option>Elektronik</option></select></div>
      </div>
      <div class="table-scroll">
        <table>
          <thead><tr><th>KODE</th><th>NAMA BARANG</th><th>KATEGORI</th><th>STOK</th><th>LOKASI</th><th>STATUS</th><th>AKSI</th></tr></thead>
          <tbody id="productRows"></tbody>
        </table>
      </div>
    </section>
    <div class="footer">© 2025 ATK Store Dashboard · Sistem Informasi Inventaris</div>
  </main>
</div>

<div class="modal-backdrop" id="modalBackdrop">
  <form class="modal" id="productForm">
    <h3>Tambah Barang</h3>
    <div class="form-group"><label for="itemCode">Kode Barang</label><input id="itemCode" required placeholder="Contoh: ATK009" /></div>
    <div class="form-group"><label for="itemName">Nama Barang</label><input id="itemName" required placeholder="Nama barang" /></div>
    <div class="form-group"><label for="itemCategory">Kategori</label><select id="itemCategory"><option>Alat Tulis</option><option>Kertas</option><option>Arsip</option><option>Perlengkapan</option><option>Elektronik</option></select></div>
    <div class="form-group"><label for="itemStock">Jumlah Stok</label><input id="itemStock" type="number" min="0" required placeholder="0" /></div>
    <div class="form-group"><label for="itemLocation">Lokasi</label><input id="itemLocation" required placeholder="Contoh: Rak A1" /></div>
    <div class="modal-actions"><button type="button" class="btn btn-light" id="cancelBtn">Batal</button><button class="btn" type="submit">Simpan Barang</button></div>
  </form>
</div>
<div id="toast" style="position:fixed;right:20px;bottom:20px;background:#102a56;color:white;padding:12px 17px;border-radius:10px;font-size:12px;box-shadow:var(--shadow);opacity:0;transform:translateY(10px);transition:.25s;pointer-events:none;z-index:20"></div>
@endsection