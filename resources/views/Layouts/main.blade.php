<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ATK Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
<!-- <style>
    /* HALAMAN PENERIMAAN BARANG */

    .receipt-page {
        width: 100%;
    }

    .receipt-badge {
        background: #eaf3ff;
        color: #1769d2;
        padding: 10px 15px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
    }

    .receipt-card {
        background: #fff;
        border: 1px solid #e7edf5;
        border-radius: 14px;
        padding: 22px;
        margin-bottom: 20px;
        box-shadow: 0 8px 24px rgba(23,62,116,.05);
    }

    .receipt-title {
        font-size: 15px;
        font-weight: 700;
        color: #102a56;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .receipt-title span {
        color: #1769d2;
        font-size: 19px;
    }

    .receipt-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .receipt-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .receipt-field label {
        font-size: 12px;
        font-weight: 700;
        color: #33445e;
    }

    .receipt-field label span {
        color: #e05252;
    }

    .receipt-field input,
    .receipt-field select,
    .receipt-field textarea {
        width: 100%;
        padding: 11px 13px;
        border: 1px solid #dce5f0;
        border-radius: 8px;
        outline: none;
        font-size: 12px;
        color: #24344d;
        background: #fff;
    }

    .receipt-field input:focus,
    .receipt-field select:focus,
    .receipt-field textarea:focus {
        border-color: #1769d2;
        box-shadow: 0 0 0 3px rgba(23,105,210,.08);
    }

    .receipt-field textarea {
        resize: vertical;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    .receipt-table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }

    .receipt-table-header .receipt-title {
        margin-bottom: 5px;
    }

    .receipt-table-header p {
        color: #7c8ba3;
        font-size: 11px;
        margin: 0;
    }

    .receipt-add-btn {
        background: #1769d2;
        color: white;
        border: none;
        padding: 11px 15px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }

    .receipt-add-btn:hover,
    .receipt-save:hover {
        background: #1059b7;
    }

    .receipt-table-scroll {
        overflow-x: auto;
    }

    .receipt-table {
        width: 100%;
        min-width: 850px;
        border-collapse: collapse;
    }

    .receipt-table th {
        background: #f2f7ff;
        color: #60728c;
        padding: 13px 10px;
        text-align: left;
        font-size: 10px;
    }

    .receipt-table td {
        padding: 9px 7px;
        border-bottom: 1px solid #edf1f6;
    }

    .receipt-table td input {
        width: 100%;
        min-width: 65px;
        padding: 9px 8px;
        border: 1px solid #dce5f0;
        border-radius: 6px;
        font-size: 11px;
        outline: none;
    }

    .receipt-table td input:focus {
        border-color: #1769d2;
    }

    .receipt-table td input[readonly] {
        background: #f3f7fc;
        color: #1769d2;
        font-weight: 700;
    }

    .row-delete {
        border: none;
        background: #ffeded;
        color: #dc2626;
        padding: 8px 10px;
        border-radius: 6px;
        cursor: pointer;
    }

    .row-delete:hover {
        background: #dc2626;
        color: white;
    }


    .receipt-total {
    margin-top: 15px;
    padding: 13px 18px;
    background: #f5f9ff;
    border-radius: 9px;

    display: grid;
    grid-template-columns: 1fr 1fr 1.2fr;
    align-items: center;
    gap: 15px;
    width: 100%;
    box-sizing: border-box;
    }

    .receipt-total > div {
        display: flex;
        flex-direction: column;
        gap: 5px;
        min-width: 0;
    }

    .receipt-total span {
        color: #7c8ba3;
        font-size: 10px;
        white-space: nowrap;
    }

    .receipt-total strong {
        color: #102a56;
        font-size: 13px;
        font-weight: 600;
    }

    .receipt-total .grand-total {
        border-left: 1px solid #1769d2;
        padding-left: 18px;
    }

    .receipt-total .grand-total h2 {
        margin: 0;
        color: #1769d2;
        font-size: 19px;
        font-weight: 700;
        white-space: nowrap;
    }

    /* Tampilan HP */
    @media (max-width: 600px) {
        .receipt-total {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 12px;
        }

        .receipt-total .grand-total {
            grid-column: 1 / -1;
            border-left: none;
            border-top: 1px solid #1769d2;
            padding: 10px 0 0;
        }
    }

    .grand-total h2 {
        color: #1769d2;
        font-size: 22px;
        margin: 0;
    }

    .receipt-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 25px;
    }

    .receipt-actions a,
    .receipt-actions button {
        padding: 11px 17px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
    }

    .receipt-cancel,
    .receipt-reset {
        border: 1px solid #dce5f0;
        color: #52647c;
        background: white;
    }

    .receipt-save {
        border: none;
        background: #1769d2;
        color: white;
    }

    @media(max-width: 700px) {
        .receipt-info-grid {
            grid-template-columns: 1fr;
        }

        .full-width {
            grid-column: auto;
        }

        .receipt-total {
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .receipt-total .grand-total {
            width: 100%;
            border-left: none;
            border-top: 2px solid #1769d2;
            padding: 15px 0 0;
        }

        .receipt-table-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .receipt-actions {
            flex-wrap: wrap;
        }
    }


        /* =====================================
        SEARCH HISTORI PENERIMAAN
        ===================================== */

        .receipt-page .search-container {
            width: 50%;
            max-width: 600px;
            margin: 10 0 20px auto;
        }

        .receipt-page .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        .receipt-page .search-input-wrapper {
            position: relative;
            flex: 1;
            min-width: 0;
            display: flex;
            align-items: center;
        }

        .receipt-page .search-icon {
            position: absolute;
            left: 15px;
            font-size: 23px;
            color: #94a3b8;
            pointer-events: none;
        }

        .receipt-page .search-input {
            box-sizing: border-box;
            width: 100%;
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

        .receipt-page .search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.12);
        }

        .receipt-page .search-input::placeholder {
            color: #94a3b8;
        }

        .receipt-page .search-clear {
            position: absolute;
            right: 13px;
            color: #94a3b8;
            font-size: 22px;
            text-decoration: none;
        }

        .receipt-page .search-clear:hover {
            color: #ef4444;
        }

        .receipt-page .search-button {
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
            justify-content: center;
            gap: 7px;
            transition: .2s;
            margin-left: 4px;
        }

        .receipt-page .search-button:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        /* =====================================
        TABEL HISTORI PENERIMAAN
        ===================================== */

        .receipt-page .table-responsive {
            border-radius: 8px;
        }

        .receipt-page .table {
            margin-bottom: 0;
            font-size: 12px;
            color: #34445d;
            vertical-align: middle;
        }

        .receipt-page .table thead th {
            padding: 12px 13px;
            background: #f4f7fc;
            color: #60738f;
            border-bottom: 1px solid #e4eaf2;
            border-top: none;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .35px;
            white-space: nowrap;
        }

        .receipt-page .table tbody td {
            padding: 11px 13px;
            border-color: #edf1f6;
        }

        .receipt-page .table tbody tr:hover {
            background: #f8fbff;
        }

        .receipt-page .table .btn {
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }

        /* =====================================
        PAGINATION
        ===================================== */

        .receipt-page .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 18px;
            padding: 12px 0;
        }

        .receipt-page .pagination-info {
            font-size: 13px;
            color: #64748b;
        }

        .receipt-page .pagination-links {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 5px;
        }

        .receipt-page .page-btn {
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

        .receipt-page .page-btn:hover {
            background: #eff6ff;
            color: #2563eb;
        }

        .receipt-page .page-btn.active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        .receipt-page .page-btn.disabled {
            color: #94a3b8;
            background: #f1f5f9;
            cursor: not-allowed;
        }

        /* =====================================
        RESPONSIVE
        ===================================== */

        @media (max-width: 768px) {
            .receipt-page .receipt-card {
                padding: 13px;
            }

            .receipt-page .receipt-table {
                min-width: 850px;
            }
        }

        @media (max-width: 600px) {
            .receipt-page .search-container {
                width: 100%;
                max-width: none;
            }

            .receipt-page .search-form {
                flex-direction: column;
                align-items: stretch;
            }

            .receipt-page .search-button {
                width: 100%;
            }

            .receipt-page .pagination-container {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        /* AUTOCOMPLETE MASTER BARANG */

    .barang-autocomplete {
        position: relative;
        width: 100%;
        min-width: 190px;
    }

    .barang-autocomplete input {
        width: 100%;
        min-width: 190px;
        padding: 9px 8px;
        border: 1px solid #dce5f0;
        border-radius: 6px;
        font-size: 11px;
        outline: none;
    }

    .barang-autocomplete input:focus {
        border-color: #1769d2;
    }

    .barang-suggestions {
        display: none;
        position: fixed;
        width: 320px;
        max-width: calc(100vw - 20px);
        max-height: 250px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #dce5f0;
        border-radius: 8px;
        box-shadow: 0 8px 25px rgba(0,0,0,.18);
        z-index: 999999;
    }

    .barang-suggestion-item {
        padding: 10px 12px;
        cursor: pointer;
        border-bottom: 1px solid #edf1f6;
        background: #fff;
    }

    .barang-suggestion-item:hover {
        background: #f0f6ff;
    }

    .barang-suggestion-item strong {
        display: block;
        color: #1769d2;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .barang-suggestion-item small {
        display: block;
        color: #64748b;
        font-size: 10px;
    }

    .barang-suggestion-empty {
        padding: 12px;
        color: #94a3b8;
        font-size: 11px;
    }

    .pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 20px;
    }

    .pagination-info {
        font-size: 13px;
        color: #6c757d;
    }

    .pagination-links {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
    }

    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: #fff;
        color: #0d6efd;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .page-btn:hover {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .page-btn.active {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        font-weight: 600;
    }

    .page-btn.disabled {
        color: #adb5bd;
        background: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
        pointer-events: none;
    }
    .modal {
    position: fixed !important;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1055;
    background-color: rgba(0, 0, 0, 0.05);
    backdrop-filter: blur(1px);
    }

    .modal-dialog {
        margin-left: auto;
        margin-right: auto;
        
    }

    thead th {
    vertical-align: middle;
    white-space: nowrap;
    }

    thead th select.form-select {
        min-width: 150px;
        font-size: 12px;
        cursor: pointer;
    }

    thead th form {
        margin: 0;
    }

    /* PAGINATION */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
        margin-bottom: 15px;
    }

    .pagination-info {
        font-size: 13px;
        color: #6c757d;
    }

    .pagination-links {
        display: flex;
        align-items: center;
        gap: 5px;
        flex-wrap: wrap;
    }

    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: #fff;
        color: #0d6efd;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .page-btn:hover {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .page-btn.active {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        font-weight: 600;
    }

    .page-btn.disabled {
        color: #adb5bd;
        background: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
        pointer-events: none;
    }

    @media (max-width: 576px) {
        .pagination-container {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    .search-container {
        width: 50%;
        margin: 15px 0 20px auto;
    }

    .search-form {
        display: flex;
        align-items: center;
        justify-content: flex-end;
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
    }

    .search-button:hover {
        background: #1d4ed8;
    }

    .modal {
        position: fixed !important;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1055;
        background-color: rgba(0, 0, 0, .05);
        backdrop-filter: blur(1px);
    }

    .modal-dialog {
        margin-left: auto;
        margin-right: auto;
    }

    @media (max-width: 768px) {
        .search-container {
            width: 100%;
            margin: 15px 0 20px;
        }
    }

    @media (max-width: 500px) {
        .search-form {
            flex-wrap: wrap;
        }

        .search-input-wrapper {
            flex-basis: 100%;
        }

        .search-button {
            flex: 1;
        }
    }
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
    /* =====================================
    DROPDOWN FILTER STATUS DASHBOARD
    ===================================== */

    .status-filter {
        position: relative;
        min-width: 135px;
    }

    .status-filter select {
        appearance: none;
        -webkit-appearance: none;
        width: 100%;
        min-width: 135px;
        height: 34px;
        padding: 6px 32px 6px 12px;

        font-size: 11px;
        font-weight: 600;
        letter-spacing: .2px;

        color: #344054;
        background-color: #f8fafc;

        border: 1px solid #dbe3ef;
        border-radius: 8px;

        cursor: pointer;
        outline: none;

        transition: all .25s ease;
    }

    /* Icon dropdown */
    .status-filter::after {
        content: "";
        position: absolute;
        right: 12px;
        top: 50%;

        width: 7px;
        height: 7px;

        border-right: 2px solid #64748b;
        border-bottom: 2px solid #64748b;

        transform: translateY(-70%) rotate(45deg);
        pointer-events: none;
    }

    /* Hover */
    .status-filter select:hover {
        background-color: #ffffff;
        border-color: #93b4e8;
    }

    /* Focus */
    .status-filter select:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .13);
    }

    /* Option */
    .status-filter select option {
        padding: 10px;
        font-size: 12px;
        font-weight: 500;
        background: #ffffff;
        color: #344054;
    }
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
    
</style> -->

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
    <a href="/databarang/barangs"
           class="{{ request()->is('databarang/barangs') ? 'active' : '' }}">
            Master Barang
        </a>
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
    <a href="{{ route('laporan.index') }}"
           class="{{ request()->routeIs('laporan.index*') ? 'active' : '' }}">
            Laporan Bulanan & Tahunan
        </a>
        
        <a href="{{ route('laporan.penerimaan') }}"
           class="{{ request()->routeIs('laporan.penerimaan*') ? 'active' : '' }}">
            Laporan Histori Penerimaan Barang
        </a>

        <a href="{{ route('laporan.pengeluaran') }}"
           class="{{ request()->routeIs('laporan.pengeluaran*') ? 'active' : '' }}">
            Laporan Histori Pengeluaran Barang
        </a>

        <a href="{{ route('laporan.penyesuaian') }}"
           class="{{ request()->routeIs('laporan.penyesuaian*') ? 'active' : '' }}">
            Laporan Histori Penyesuaian Stok
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