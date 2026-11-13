@extends('Layouts.main')

@section('container')

<style>

  /* SEARCH DASHBOARD */
  .search-container {
      width: 50%;
      margin: 15px 0 20px;
  }

  .search-form {
      display: flex;
      align-items: center;
      gap: 10px;
      width: 100%;
  }

  .search-input-wrapper {
      position: relative;
      flex: 1;
      display: flex;
      align-items: center;
  }

  .search-icon {
      position: absolute;
      left: 15px;
      font-size: 23px;
      color: #94a3b8;
      pointer-events: none;
  }

  .search-input {
      width: 99%;
      height: 44px;
      padding: 0 42px;
      border: 1px solid #dbe2ea;
      border-radius: 9px;
      outline: none;
      background: #fff;
      color: #334155;
      font-size: 13px;
      transition: .2s;
  }

  .search-input:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59,130,246,.12);
  }

  .search-input::placeholder {
      color: #94a3b8;
  }

  .search-clear {
      position: absolute;
      right: 13px;
      color: #94a3b8;
      font-size: 22px;
      text-decoration: none;
  }

  .search-clear:hover {
      color: #ef4444;
  }

  .search-button {
      height: 44px;
      padding: 0 22px;
      border: none;
      border-radius: 9px;
      background: #2563eb;
      color: #fff;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 7px;
      transition: .2s;
  }

  .search-button:hover {
      background: #1d4ed8;
      transform: translateY(-1px);
  }

  .search-button span {
      font-size: 19px;
  }

  @media (max-width: 600px) {
      .search-form {
          flex-direction: column;
          align-items: stretch;
      }

      .search-button {
          justify-content: center;
      }
  }

  .pagination-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 18px;
      padding: 12px 0;
  }

  .pagination-info {
      font-size: 13px;
      color: #64748b;
  }

  .pagination-links {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 5px;
  }

  .page-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 34px;
      height: 34px;
      padding: 0 10px;
      border: 1px solid #dbe2ea;
      border-radius: 6px;
      color: #334155;
      background: #fff;
      font-size: 13px;
      text-decoration: none;
      cursor: pointer;
  }

  .page-btn:hover {
      background: #eff6ff;
      color: #2563eb;
  }

  .page-btn.active {
      background: #2563eb;
      color: #fff;
      border-color: #2563eb;
  }

  .page-btn.disabled {
      color: #94a3b8;
      background: #f1f5f9;
      cursor: not-allowed;
  }

</style>
<section class="section-head">
      <div><h2>Ringkasan Inventaris</h2><p>Pantau kondisi persediaan barang secara real-time di halaman ini.</p></div>
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
    
    <section class="panel table-panel">
      <div class="panel-head">
        <div><h3>Daftar Barang</h3><span>Kelola seluruh persediaan alat tulis kantor</span></div>
        @if(request('search'))
    <div style="font-size:13px; color:#64748b; margin-bottom:12px;">
        Hasil pencarian untuk:
        <strong>{{ request('search') }}</strong>
        — ditemukan {{ $barang->total() }} barang.
    </div>
    @endif
    <div class="search-container">
        <form action="{{ url('/dashboard') }}" method="GET" class="search-form">

            <div class="search-input-wrapper">
                <span class="search-icon">⌕</span>

                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? request('search') }}"
                    placeholder="Cari kode barang, nama barang, atau kategori..."
                    class="search-input"
                >

                @if(request('search'))
                    <a href="{{ url('/dashboard') }}"
                      class="search-clear"
                      title="Hapus pencarian">
                        &times;
                    </a>
                @endif
            </div>

            <button type="submit" class="search-button">
                Search
            </button>

        </form>
    </div>

      </div>
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>KODE</th>
              <th>NAMA BARANG</th>
              <th>KATEGORI</th>
              <th>STOK</th>
              <th>LOKASI</th>
              <th>STATUS</th>
              @hasrole('Super Admin')
              <th>AKSI</th>
              @endhasrole
            </tr>
        </thead>                  
        
      <tbody id="productRows">
          @forelse ($barang as $item)
              <tr>
              <td>{{ $item->kode_barang }}</td>
        <td>{{ $item->nama_barang }}</td>
        <td>{{ $item->kategori }}</td>
        <td>{{ $item->stok }} {{ $item->satuan }}</td>
        <td>
            {{ $item->nama_cabang ?? '-' }}
            {{ $item->lokasi ? ' - ' . $item->lokasi : '' }}
        </td>
                  <td>
                      @if ($item->stok > 10)
                          <span class="badge bg-success">Tersedia</span>
                      @elseif ($item->stok > 0)
                          <span class="badge bg-warning">Stok Menipis</span>
                      @else
                          <span class="badge bg-danger">Habis</span>
                      @endif
                  </td>
                  @hasrole('Super Admin')
                  <td>
                      <a href="{{ url('/databarang/daftar-barang') }}"
                        class="btn btn-sm btn-primary">
                          Detail
                      </a>
                  </td>
                  @endhasrole
              </tr>
          @empty
              <tr>
                  <td colspan="7" class="text-center">
                      Belum ada data barang.
                  </td>
              </tr>
          @endforelse
      </tbody>
    </table>

    @if ($barang->hasPages())
        <div class="pagination-container">
            <div class="pagination-info">
                Menampilkan {{ $barang->firstItem() ?? 0 }}
                sampai {{ $barang->lastItem() ?? 0 }}
                dari {{ $barang->total() }} barang
            </div>

            <div class="pagination-links">
                {{-- Previous --}}
                @if ($barang->onFirstPage())
                    <span class="page-btn disabled">‹ Previous</span>
                @else
                    <a href="{{ $barang->previousPageUrl() }}" class="page-btn">
                        ‹ Previous
                    </a>
                @endif

                {{-- Nomor halaman --}}
                @foreach ($barang->getUrlRange(1, $barang->lastPage()) as $page => $url)
                    @if ($page == $barang->currentPage())
                        <span class="page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($barang->hasMorePages())
                    <a href="{{ $barang->nextPageUrl() }}" class="page-btn">
                        Next ›
                    </a>
                @else
                    <span class="page-btn disabled">Next ›</span>
                @endif
            </div>
        </div>
    @endif
      </div>
    </section>
    <div class="footer">© 2025 ATK Store Dashboard · Sistem Informasi Inventaris</div>
  </main>
</div>


@endsection