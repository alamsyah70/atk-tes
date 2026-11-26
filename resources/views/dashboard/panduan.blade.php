@extends('Layouts.main')

@section('container')

<style>
    .faq-page {
        --primary: #2563eb;
        --primary-dark: #1e40af;
        --text: #1e293b;
        --muted: #64748b;
        --border: #e2e8f0;
        --surface: #ffffff;
        --soft: #f8fafc;

        color: var(--text);
        padding: 10px 0 35px;
    }

    .faq-page * {
        box-sizing: border-box;
    }

    /* HEADER */

    .faq-header {
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        color: #fff;
        padding: 28px 30px;
        border-radius: 14px;
        margin-bottom: 22px;
        position: relative;
        overflow: hidden;
    }

    .faq-header::after {
        content: "?";
        position: absolute;
        right: 35px;
        top: -20px;
        font-size: 150px;
        font-weight: 800;
        color: rgba(255,255,255,.10);
        line-height: 1;
    }

    .faq-header-content {
        position: relative;
        z-index: 1;
    }

    .faq-label {
        display: inline-block;
        padding: 5px 11px;
        border-radius: 20px;
        background: rgba(255,255,255,.16);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .7px;
        margin-bottom: 10px;
    }

    .faq-header h2 {
        margin: 0 0 8px;
        font-size: 23px;
        font-weight: 800;
    }

    .faq-header p {
        margin: 0;
        max-width: 680px;
        font-size: 13px;
        line-height: 1.7;
        color: #dbeafe;
    }

    /* INTRO */

    .faq-intro {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px 17px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: 11px;
        margin-bottom: 25px;
    }

    .faq-intro-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #dbeafe;
        color: #1d4ed8;
        border-radius: 10px;
        font-size: 18px;
        flex-shrink: 0;
    }

    .faq-intro p {
        margin: 0;
        font-size: 12px;
        line-height: 1.6;
        color: #1e40af;
    }

    /* SECTION */

    .faq-section {
        margin-bottom: 25px;
    }

    .faq-section-heading {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 13px;
    }

    .faq-section-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 17px;
        flex-shrink: 0;
    }

    .faq-section-heading h3 {
        margin: 0 0 3px;
        font-size: 15px;
        font-weight: 800;
        color: #1e293b;
    }

    .faq-section-heading p {
        margin: 0;
        font-size: 11px;
        color: var(--muted);
    }

    /* ACCORDION */

    .faq-list {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .faq-item {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 11px;
        overflow: hidden;
        transition: border-color .2s, box-shadow .2s;
    }

    .faq-item:hover {
        border-color: #bfdbfe;
        box-shadow: 0 3px 10px rgba(37,99,235,.05);
    }

    .faq-item[open] {
        border-color: #93c5fd;
        box-shadow: 0 4px 12px rgba(37,99,235,.06);
    }

    .faq-question {
        list-style: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 17px;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        user-select: none;
    }

    .faq-question::-webkit-details-marker {
        display: none;
    }

    .faq-question-left {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .faq-number {
        width: 27px;
        height: 27px;
        min-width: 27px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 11px;
        font-weight: 800;
    }

    .faq-chevron {
        width: 27px;
        height: 27px;
        min-width: 27px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 17px;
        transition: transform .2s, background .2s;
    }

    .faq-item[open] .faq-chevron {
        transform: rotate(180deg);
        background: #dbeafe;
        color: #1d4ed8;
    }

    .faq-item[open] .faq-question {
        color: #1d4ed8;
        background: #f8fbff;
    }

    .faq-answer {
        padding: 14px 18px 17px 55px;
        border-top: 1px solid #eff3f8;
        color: #64748b;
        font-size: 12px;
        line-height: 1.8;
    }

    .faq-answer p {
        margin: 0 0 9px;
    }

    .faq-answer p:last-child {
        margin-bottom: 0;
    }

    .faq-answer ul,
    .faq-answer ol {
        padding-left: 18px;
        margin: 7px 0;
    }

    .faq-answer li {
        margin-bottom: 5px;
    }

    .faq-answer strong {
        color: #334155;
    }

    .faq-tip {
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        background: #f1f5f9;
        border-left: 3px solid #60a5fa;
        color: #475569;
        font-size: 11px;
        line-height: 1.7;
    }

    /* FOOTER */

    .faq-footer {
        margin-top: 28px;
        padding: 17px;
        border: 1px solid var(--border);
        background: #f8fafc;
        border-radius: 11px;
        text-align: center;
    }

    .faq-footer strong {
        display: block;
        color: #334155;
        font-size: 13px;
        margin-bottom: 5px;
    }

    .faq-footer p {
        margin: 0;
        color: var(--muted);
        font-size: 11px;
        line-height: 1.7;
    }

    /* RESPONSIVE */

    @media(max-width: 650px) {
        .faq-header {
            padding: 23px 20px;
        }

        .faq-header h2 {
            font-size: 20px;
        }

        .faq-header::after {
            right: 8px;
            font-size: 100px;
        }

        .faq-question {
            padding: 13px;
            font-size: 12px;
        }

        .faq-answer {
            padding: 13px 14px;
        }

        .faq-intro {
            align-items: flex-start;
        }
    }
</style>

<div class="faq-page">

    {{-- INFORMASI --}}

    <div class="faq-intro">
        <div class="faq-intro-icon">ℹ</div>

        <p>
            <strong>Petunjuk penggunaan:</strong>
            Klik salah satu pertanyaan untuk membuka penjelasan.
            Setiap jawaban berisi informasi mengenai fungsi menu
            dan cara penggunaannya.
        </p>
    </div>

    {{-- ==========================================
         BAGIAN 1: DASHBOARD DAN MASTER DATA
    =========================================== --}}

    <div class="faq-section">

        <div class="faq-section-heading">
            <div class="faq-section-icon">▦</div>

            <div>
                <h3>Dashboard dan Master Data</h3>
                <p>Informasi utama dan pengelolaan data persediaan.</p>
            </div>
        </div>

        <div class="faq-list">

            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">01</span>
                        Apa Fungsi Menu Dashboard?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Menu <strong>Dashboard</strong> merupakan halaman utama
                        yang menampilkan ringkasan kondisi persediaan ATK
                        secara keseluruhan.
                    </p>

                    <p>Informasi yang dapat ditampilkan antara lain:</p>

                    <ul>
                        <li>Total jenis barang yang tersedia.</li>
                        <li>Total jumlah stok barang.</li>
                        <li>Informasi barang dengan stok rendah.</li>
                        <li>Ringkasan transaksi penerimaan dan pengeluaran.</li>
                        <li>Grafik aktivitas persediaan.</li>
                        <li>Informasi transaksi terbaru.</li>
                    </ul>

                    <div class="faq-tip">
                        Dashboard membantu pengguna memantau kondisi
                        persediaan tanpa harus membuka setiap menu transaksi.
                    </div>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">02</span>
                        Apa Fungsi Menu Master Barang?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Menu <strong>Master Barang</strong> digunakan untuk
                        menyimpan dan mengelola data induk barang ATK
                        yang digunakan dalam transaksi penerimaan dan pengeluaran.
                    </p>

                    <p>Informasi barang meliputi:</p>

                    <ul>
                        <li>Kode barang.</li>
                        <li>Nama barang.</li>
                        <li>Kategori barang.</li>
                        <li>Satuan barang.</li>
                        <li>Lokasi penyimpanan.</li>
                    </ul>

                    <p>
                        Data master barang menjadi acuan ketika pengguna
                        memilih barang pada transaksi sehingga pencatatan
                        barang lebih konsisten.
                    </p>

                    <div class="faq-tip">
                        Pastikan data barang sudah benar sebelum digunakan
                        dalam transaksi penerimaan maupun pengeluaran.
                    </div>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">03</span>
                        Apa Fungsi Menu Manage Supplier?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Menu <strong>Manage Supplier</strong> digunakan untuk
                        mengelola data pemasok atau perusahaan yang menyediakan
                        barang ATK.
                    </p>

                    <p>Informasi supplier dapat mencakup:</p>

                    <ul>
                        <li>Kode supplier.</li>
                        <li>Nama supplier.</li>
                        <li>Alamat supplier.</li>
                        <li>Nomor telepon.</li>
                        <li>Email supplier.</li>
                        <li>Cabang terkait apabila diterapkan.</li>
                    </ul>

                    <p>
                        Data supplier digunakan sebagai referensi ketika
                        mencatat penerimaan barang.
                    </p>
                </div>
            </details>

        </div>
    </div>


    {{-- ==========================================
         BAGIAN 2: TRANSAKSI
    =========================================== --}}

    <div class="faq-section">

        <div class="faq-section-heading">
            <div class="faq-section-icon">⇄</div>

            <div>
                <h3>Transaksi Barang</h3>
                <p>Pencatatan barang masuk dan barang keluar.</p>
            </div>
        </div>

        <div class="faq-list">

            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">04</span>
                        Apa Fungsi Menu Penerimaan Barang?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Menu <strong>Penerimaan Barang</strong> digunakan
                        untuk mencatat barang ATK yang masuk ke persediaan
                        gerai dari supplier.
                    </p>

                    <p>Pengguna dapat mengisi informasi seperti:</p>

                    <ul>
                        <li>Supplier pemasok barang.</li>
                        <li>Tanggal penerimaan.</li>
                        <li>Daftar barang yang diterima.</li>
                        <li>Jumlah barang dan harga perolehan.</li>
                        <li>Keterangan transaksi.</li>
                        <li>Bukti PO atau dokumen pendukung.</li>
                    </ul>

                    <p>
                        Setiap detail penerimaan yang tersimpan akan
                        diperhitungkan dalam penambahan stok barang.
                    </p>

                    <div class="faq-tip">
                        Periksa kembali kode barang, jumlah, harga,
                        dan supplier sebelum menyimpan transaksi.
                    </div>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">05</span>
                        Apa Fungsi Menu Pengeluaran Barang?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Menu <strong>Pengeluaran Barang</strong> digunakan
                        untuk mencatat barang ATK yang dikeluarkan dari
                        persediaan untuk kebutuhan operasional atau divisi.
                    </p>

                    <p>Data yang dicatat meliputi:</p>

                    <ul>
                        <li>Tanggal pengeluaran.</li>
                        <li>Divisi penerima barang.</li>
                        <li>Nama pengambil barang.</li>
                        <li>Daftar barang yang dikeluarkan.</li>
                        <li>Jumlah barang yang dikeluarkan.</li>
                        <li>Keterangan pengeluaran.</li>
                    </ul>

                    <p>
                        Transaksi pengeluaran akan mengurangi jumlah
                        persediaan barang gerai.
                    </p>
                </div>
            </details>

        </div>
    </div>


    {{-- ==========================================
         BAGIAN 3: STOK DAN PENYESUAIAN
    =========================================== --}}

    <div class="faq-section">

        <div class="faq-section-heading">
            <div class="faq-section-icon">▤</div>

            <div>
                <h3>Persediaan dan Penyesuaian Stok</h3>
                <p>Pemantauan saldo barang dan koreksi persediaan.</p>
            </div>
        </div>

        <div class="faq-list">

            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">06</span>
                        Apa Fungsi Menu Stock Card / Kartu Stok?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Menu <strong>Stock Card</strong> atau Kartu Stok
                        digunakan untuk melihat riwayat pergerakan stok
                        setiap barang secara kronologis.
                    </p>

                    <p>Informasi yang dapat dilihat meliputi:</p>

                    <ul>
                        <li>Saldo awal sebelum periode yang dipilih.</li>
                        <li>Transaksi barang masuk.</li>
                        <li>Transaksi barang keluar.</li>
                        <li>Penyesuaian stok.</li>
                        <li>Saldo berjalan setelah setiap transaksi.</li>
                        <li>Saldo akhir pada periode laporan.</li>
                    </ul>

                    <div class="faq-tip">
                        Kartu stok sangat berguna untuk memeriksa asal
                        perubahan jumlah persediaan suatu barang.
                    </div>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">07</span>
                        Bagaimana Cara Melakukan Penyesuaian Stok?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Menu <strong>Penyesuaian Stok</strong> hanya tersedia pada user IT
                        apabila terdapat perbedaan antara stok sistem dan stok fisik, periksa
                        terlebih dahulu penyebab ketidak sesuaian dan hubungu IT untuk melakukan
                        penyesuaian stok.
                    </p>

                    <p>
                        Penyesuaian dapat berupa penambahan maupun pengurangan
                        stok dengan mencatat jumlah koreksi dan alasan
                        penyesuaian.
                    </p>

                    <div class="faq-tip">
                        Penyesuaian stok sebaiknya dilakukan berdasarkan
                        pemeriksaan fisik dan disertai alasan yang jelas.
                    </div>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">08</span>
                        Bagaimana Cara Sistem Menghitung Stok Barang?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Sistem menghitung stok berdasarkan akumulasi
                        transaksi barang masuk, barang keluar, dan
                        penyesuaian stok.
                    </p>

                    <div class="faq-tip">
                        <strong>Rumus perhitungan stok:</strong><br>
                        Stok Akhir = Total Penerimaan - Total Pengeluaran
                        + Total Penyesuaian Stok
                    </div>

                    <p>Contoh:</p>

                    <ul>
                        <li>Stok awal: 100 unit.</li>
                        <li>Penerimaan: 50 unit.</li>
                        <li>Pengeluaran: 30 unit.</li>
                        <li>Penyesuaian: -5 unit.</li>
                    </ul>

                    <p>
                        Maka stok akhir adalah
                        <strong>115 unit</strong>.
                    </p>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">09</span>
                        Apa yang Harus Dilakukan Jika Stok Sistem Berbeda dengan Stok Fisik?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Lakukan pemeriksaan terlebih dahulu terhadap
                        riwayat penerimaan, pengeluaran, dan penyesuaian
                        melalui menu Stock Card.
                    </p>

                    <p>Apabila perbedaan sudah dipastikan, lakukan:</p>

                    <ol>
                        <li>Hitung ulang stok fisik barang.</li>
                        <li>Periksa riwayat transaksi barang.</li>
                        <li>Identifikasi penyebab selisih.</li>
                        <li>Hubungi IT untuk dilakukan penyesuaian stok sesuai hasil pemeriksaan.</li>
                        <li>Berikan alasan penyesuaian dengan jelas.</li>
                    </ol>
                </div>
            </details>

        </div>
    </div>


    {{-- ==========================================
         BAGIAN 4: LAPORAN
    =========================================== --}}

    <div class="faq-section">

        <div class="faq-section-heading">
            <div class="faq-section-icon">▧</div>

            <div>
                <h3>Laporan dan Riwayat Transaksi</h3>
                <p>Melihat, memfilter, dan mencetak informasi transaksi.</p>
            </div>
        </div>

        <div class="faq-list">

            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">10</span>
                        Apa Fungsi Menu Laporan Rekapitulasi?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Menu <strong>Laporan Rekapitulasi</strong> digunakan
                        untuk menyajikan ringkasan aktivitas persediaan
                        dalam periode tertentu.
                    </p>

                    <p>
                        Laporan dapat membantu pengguna mengetahui
                        jumlah dan nilai transaksi berdasarkan
                        kategori laporan yang tersedia.
                    </p>

                    <p>
                        Pengguna dapat memanfaatkan filter periode,
                        cabang, supplier, barang, atau divisi apabila
                        tersedia pada jenis laporan yang dipilih.
                    </p>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">11</span>
                        Apa Fungsi Menu Laporan Histori Penerimaan?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Menu <strong>Laporan Histori Penerimaan</strong>
                        digunakan untuk menelusuri seluruh riwayat
                        transaksi barang masuk dan export pdf sesuai filter.
                    </p>

                    <p>Informasi yang dapat diperiksa antara lain:</p>

                    <ul>
                        <li>Tanggal penerimaan.</li>
                        <li>Nomor penerimaan.</li>
                        <li>Nama supplier.</li>
                        <li>Cabang penerima.</li>
                        <li>Kode dan nama barang.</li>
                        <li>Jumlah dan harga barang.</li>
                        <li>Total nilai penerimaan.</li>
                    </ul>

                    <p>
                        Laporan ini berguna untuk memeriksa transaksi
                        pembelian atau penerimaan barang berdasarkan
                        periode tertentu.
                    </p>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">12</span>
                        Apa Fungsi Menu Laporan Histori Pengeluaran?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Menu <strong>Laporan Histori Pengeluaran</strong>
                        digunakan untuk melihat riwayat barang yang
                        telah dikeluarkan dari persediaan dan export pdf sesuai filter.
                    </p>

                    <p>Data yang dapat ditelusuri antara lain:</p>

                    <ul>
                        <li>Tanggal dan nomor pengeluaran.</li>
                        <li>Nama divisi.</li>
                        <li>Nama pengambil barang.</li>
                        <li>Nama dan kode barang.</li>
                        <li>Jumlah barang yang dikeluarkan.</li>
                        <li>Nilai pengeluaran apabila tersedia.</li>
                    </ul>

                    <p>
                        Laporan ini membantu memantau pemakaian ATK
                        oleh masing-masing divisi.
                    </p>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">13</span>
                        Apa Fungsi Menu Laporan Penyesuaian Stok?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Menu <strong>Laporan Penyesuaian Stok</strong>
                        digunakan untuk melihat riwayat koreksi persediaan
                        yang pernah dilakukan.
                    </p>

                    <p>Informasi yang dapat diperiksa meliputi:</p>

                    <ul>
                        <li>Tanggal penyesuaian.</li>
                        <li>Nomor penyesuaian.</li>
                        <li>Cabang terkait.</li>
                        <li>Kode dan nama barang.</li>
                        <li>Jumlah koreksi stok.</li>
                        <li>Alasan penyesuaian.</li>
                        <li>Pengguna yang melakukan penyesuaian apabila dicatat.</li>
                    </ul>

                    <p>
                        Laporan ini membantu proses audit dan pemeriksaan
                        perubahan saldo persediaan.
                    </p>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">14</span>
                        Bagaimana Cara Memfilter Laporan?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Gunakan kolom filter yang tersedia pada halaman
                        laporan untuk mempersempit data yang ingin ditampilkan.
                    </p>

                    <p>Contoh filter yang dapat digunakan:</p>

                    <ul>
                        <li>Periode tanggal atau bulan.</li>
                        <li>Tahun transaksi.</li>
                        <li>Cabang.</li>
                        <li>Supplier.</li>
                        <li>Barang.</li>
                        <li>Divisi.</li>
                    </ul>

                    <p>
                        Setelah menentukan filter, jalankan pencarian
                        untuk menampilkan data sesuai kriteria.
                    </p>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">15</span>
                        Bagaimana Cara Mengunduh atau Mencetak Laporan PDF?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Pilih jenis laporan yang dibutuhkan, tentukan
                        periode dan filter lainnya, kemudian gunakan
                        tombol export PDF yang tersedia.
                    </p>

                    <div class="faq-tip">
                        Sebelum mencetak, pastikan periode, cabang,
                        dan data filter sudah sesuai dengan kebutuhan.
                    </div>
                </div>
            </details>

        </div>
    </div>


    {{-- ==========================================
         BAGIAN 6: PERTANYAAN UMUM
    =========================================== --}}

    <div class="faq-section">

        <div class="faq-section-heading">
            <div class="faq-section-icon">?</div>

            <div>
                <h3>Pertanyaan Umum dan Kendala</h3>
                <p>Jawaban untuk beberapa kendala yang sering ditemui.</p>
            </div>
        </div>

        <div class="faq-list">

            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">16</span>
                        Mengapa Barang Tidak Muncul Saat Melakukan Transaksi?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>Beberapa hal yang dapat diperiksa:</p>

                    <ul>
                        <li>Pastikan barang sudah terdaftar di Master Barang.</li>
                        <li>Periksa kode dan nama barang, sistem akan automatis mengenali barang jika sesuai master barang.</li>
                        <li>Periksa apakah data barang masih tersedia.</li>
                    </ul>

                    <p>
                        Jika barang tetap tidak muncul, hubungi
                        IT untuk pemeriksaan lebih lanjut.
                    </p>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">17</span>
                        Mengapa Data Transaksi Tidak Muncul pada Laporan?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>Periksa beberapa hal berikut:</p>

                    <ul>
                        <li>Pastikan periode laporan sudah sesuai dengan tanggal transaksi.</li>
                        <li>Pastikan Filter supplier, barang, atau divisi tidak membatasi data yang dicari.</li>
                    </ul>
                </div>
            </details>

            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">18</span>
                        Apa yang Harus Dilakukan Jika Salah Memasukkan Data Transaksi?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Segera periksa transaksi yang salah dan
                        identifikasi bagian data yang perlu diperbaiki.
                    </p>

                    <p>
                        Hubungi IT terlebih dahulu dan jelaskan alasannya.
                    </p>
                </div>
            </details>


            <details class="faq-item">
                <summary class="faq-question">
                    <span class="faq-question-left">
                        <span class="faq-number">19</span>
                        Apakah Aplikasi Dapat Digunakan oleh Beberapa Gerai?
                    </span>
                    <span class="faq-chevron">⌄</span>
                </summary>

                <div class="faq-answer">
                    <p>
                        Ya. Aplikasi ATK MDI dirancang untuk mendukung
                        pengelolaan persediaan dari beberapa gerai.
                    </p>

                    <p>
                        Setiap transaksi dikaitkan dengan gerai
                        masing-masing sehingga data persediaan dapat
                        dikelola sesuai ruang lingkup gerai tujuan.
                    </p>
                </div>
            </details>

            <div class="panduan-process">

        <h4>Gambaran Alur Persediaan Barang</h4>

        <div class="panduan-process-flow">
        <div class="panduan-process-step">
                <strong>MASTER BARANG</strong>
                <small>Informasi barang</small>
            </div>
            <span class="panduan-arrow">→</span>
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


    {{-- FOOTER --}}

    <div class="faq-footer">
        <strong>Masih Membutuhkan Bantuan?</strong>

        <p>
            Jika pertanyaan Anda belum terjawab atau mengalami kendala
            dalam penggunaan Aplikasi ATK MDI, silakan hubungi
            tim IT.
        </p>
    </div>

</div>

@endsection
