@extends('Layouts.main')

@section('container')

<style>
    .panduan-page {
        --primary: #2563eb;
        --primary-dark: #1e40af;
        --text: #1e293b;
        --muted: #64748b;
        --border: #e2e8f0;
        --surface: #ffffff;
        color: var(--text);
        padding: 10px 0 35px;
    }

    .panduan-page * {
        box-sizing: border-box;
    }

    .panduan-hero {
        background: linear-gradient(135deg, #1e40af, #2563eb);
        color: white;
        padding: 32px;
        border-radius: 16px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
    }

    .panduan-hero::after {
        content: "📦";
        position: absolute;
        right: 35px;
        top: 20px;
        font-size: 90px;
        opacity: .15;
    }

    .panduan-hero h2 {
        font-size: 25px;
        font-weight: 800;
        margin: 0 0 10px;
    }

    .panduan-hero p {
        font-size: 14px;
        line-height: 1.7;
        max-width: 650px;
        margin: 0;
        color: #dbeafe;
    }

    .panduan-label {
        display: inline-block;
        background: rgba(255,255,255,.18);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .5px;
        margin-bottom: 12px;
    }

    .panduan-section-title {
        margin: 30px 0 15px;
    }

    .panduan-section-title h3 {
        font-size: 19px;
        font-weight: 800;
        margin: 0 0 5px;
    }

    .panduan-section-title p {
        font-size: 13px;
        color: var(--muted);
        margin: 0;
    }

    .panduan-role {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 15px;
        overflow: hidden;
        margin-bottom: 22px;
        box-shadow: 0 4px 15px rgba(15,23,42,.04);
    }

    .panduan-role-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
    }

    .panduan-role-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 23px;
        flex-shrink: 0;
    }

    .superadmin .panduan-role-icon {
        background: #dbeafe;
    }

    .admin-gerai .panduan-role-icon {
        background: #dcfce7;
    }

    .panduan-role-header h4 {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 800;
    }

    .panduan-role-header p {
        color: var(--muted);
        margin: 0;
        font-size: 12px;
    }

    .panduan-content {
        padding: 20px;
    }

    .panduan-flow {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 13px;
    }

    .panduan-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        border: 1px solid var(--border);
        border-radius: 11px;
        padding: 15px;
        transition: .2s;
        background: white;
    }

    .panduan-item:hover {
        border-color: #93c5fd;
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(37,99,235,.07);
    }

    .panduan-number {
        width: 30px;
        height: 30px;
        border-radius: 9px;
        background: #eff6ff;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        flex-shrink: 0;
    }

    .panduan-item h5 {
        font-size: 13px;
        font-weight: 800;
        margin: 1px 0 5px;
    }

    .panduan-item p {
        color: var(--muted);
        font-size: 12px;
        line-height: 1.6;
        margin: 0;
    }

    .panduan-note {
        margin-top: 16px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 13px 15px;
        color: #1e40af;
        font-size: 12px;
        line-height: 1.6;
    }

    .panduan-process {
        background: white;
        border: 1px solid var(--border);
        border-radius: 15px;
        padding: 22px;
        margin-top: 25px;
    }

    .panduan-process h4 {
        font-size: 16px;
        font-weight: 800;
        margin: 0 0 18px;
    }

    .panduan-process-flow {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .panduan-process-step {
        background: #f1f5f9;
        border: 1px solid var(--border);
        padding: 12px 16px;
        border-radius: 10px;
        text-align: center;
        min-width: 130px;
    }

    .panduan-process-step strong {
        display: block;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .panduan-process-step small {
        color: var(--muted);
        font-size: 11px;
    }

    .panduan-arrow {
        color: var(--primary);
        font-size: 20px;
        font-weight: 800;
    }

    .panduan-footer {
        text-align: center;
        color: var(--muted);
        font-size: 12px;
        padding: 25px 10px 0;
    }

    @media(max-width: 650px) {
        .panduan-hero {
            padding: 23px;
        }

        .panduan-hero h2 {
            font-size: 21px;
        }

        .panduan-hero::after {
            right: 10px;
            font-size: 65px;
        }

        .panduan-flow {
            grid-template-columns: 1fr;
        }

        .panduan-process-flow {
            flex-direction: column;
        }

        .panduan-arrow {
            transform: rotate(90deg);
        }

        .panduan-process-step {
            width: 100%;
        }
    }
</style>

<div class="panduan-page">
    {{-- PENGANTAR --}}
    <div class="panduan-section-title">
        <h3>Alur Penggunaan Aplikasi</h3>
        <p>
            Setiap pengguna memiliki tugas dan tanggung jawab sesuai
            dengan peran yang diberikan oleh administrator.
        </p>
    </div>

    {{-- SUPER ADMIN --}}
    <div class="panduan-role superadmin">
        <div class="panduan-role-header">
            <div>
                <h4>USER SUPER ADMIN</h4>
                <p>Pengelola utama sistem dan administrasi pengguna</p>
            </div>
        </div>

        <div class="panduan-content">

            <div class="panduan-flow">

                <div class="panduan-item">
                    <div class="panduan-number">01</div>
                    <div>
                        <h5>Manajemen Pengguna</h5>
                        <p>
                            Membuat akun pengguna baru, menentukan
                            hak akses, serta mengatur akun pengguna
                            sesuai kebutuhan operasional.
                        </p>
                    </div>
                </div>

                <div class="panduan-item">
                    <div class="panduan-number">02</div>
                    <div>
                        <h5>Penyesuaian Stok</h5>
                        <p>
                            Mengelola koreksi stok barang apabila
                            ditemukan perbedaan antara stok sistem
                            dengan kondisi fisik barang.
                        </p>
                    </div>
                </div>

            </div>

            <div class="panduan-note">
                <strong>Catatan:</strong>
                Super Admin bertanggung jawab terhadap pengelolaan
                akses pengguna dan pengawasan administrasi sistem.
            </div>

        </div>
    </div>

    {{-- ADMIN GERAI --}}
    <div class="panduan-role admin-gerai">

        <div class="panduan-role-header">
            <div>
                <h4>USER ADMIN GERAI</h4>
                <p>Pengelola operasional persediaan barang di gerai/cabang</p>
            </div>
        </div>

        <div class="panduan-content">

            <div class="panduan-flow">

                <div class="panduan-item">
                    <div class="panduan-number">01</div>
                    <div>
                        <h5>Input Data Supplier</h5>
                        <p>
                            Mendaftarkan dan mengelola informasi supplier
                            sebagai sumber pemasok barang.
                        </p>
                    </div>
                </div>

                <div class="panduan-item">
                    <div class="panduan-number">02</div>
                    <div>
                        <h5>Input Penerimaan Barang</h5>
                        <p>
                            Mencatat barang yang masuk, meliputi supplier,
                            tanggal penerimaan, nama barang, jumlah,
                            dan informasi pendukung lainnya.
                        </p>
                    </div>
                </div>

                <div class="panduan-item">
                    <div class="panduan-number">03</div>
                    <div>
                        <h5>Input Pengeluaran Barang</h5>
                        <p>
                            Mencatat barang yang dikeluarkan,
                            jumlah barang, divisi, nama pengambil,
                            dan keterangan pengeluaran.
                        </p>
                    </div>
                </div>

                <div class="panduan-item">
                    <div class="panduan-number">04</div>
                    <div>
                        <h5>Cetak Tanda Terima</h5>
                        <p>
                            Mencetak bukti atau tanda terima
                            pengeluaran barang sebagai dokumentasi
                            dan bukti penyerahan barang.
                        </p>
                    </div>
                </div>

                <div class="panduan-item">
                    <div class="panduan-number">05</div>
                    <div>
                        <h5>Cetak Laporan Penerimaan</h5>
                        <p>
                            Melihat dan mencetak laporan penerimaan
                            berdasarkan periode tanggal serta data
                            supplier dan barang.
                        </p>
                    </div>
                </div>

                <div class="panduan-item">
                    <div class="panduan-number">06</div>
                    <div>
                        <h5>Cetak Laporan Pengeluaran</h5>
                        <p>
                            Melihat dan mencetak laporan pengeluaran
                            berdasarkan periode tanggal, nama barang,
                            serta informasi pengambilan.
                        </p>
                    </div>
                </div>

            </div>

            <div class="panduan-note">
                <strong>Catatan:</strong>
                Admin Gerai mengelola transaksi dan data operasional
                sesuai dengan cakupan cabang yang menjadi tanggung jawabnya.
            </div>

        </div>
    </div>

    {{-- ALUR OPERASIONAL --}}
    <div class="panduan-process">

        <h4>Gambaran Alur Persediaan Barang</h4>

        <div class="panduan-process-flow">

            <div class="panduan-process-step">
                <strong>SUPPLIER</strong>
                <small>Sumber pemasok barang</small>
            </div>

            <span class="panduan-arrow">→</span>

            <div class="panduan-process-step">
                <strong>PENERIMAAN</strong>
                <small>Pencatatan barang masuk</small>
            </div>

            <span class="panduan-arrow">→</span>

            <div class="panduan-process-step">
                <strong>STOK BARANG</strong>
                <small>Pemantauan persediaan</small>
            </div>

            <span class="panduan-arrow">→</span>

            <div class="panduan-process-step">
                <strong>PENGELUARAN</strong>
                <small>Pencatatan barang keluar</small>
            </div>

        </div>
    </div>
</div>

@endsection