@extends('Layouts.main')

@section('container')

<style>
    .log-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }

    .log-title {
        font-size: 24px;
        font-weight: 700;
        color: #243247;
        margin: 0;
    }

    .log-card {
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 12px;
        box-shadow: 0 3px 12px rgba(0,0,0,.035);
        overflow: hidden;
    }

    .log-filter {
        padding: 18px;
        border-bottom: 1px solid #edf0f4;
        background: #fafbfd;
    }

    .log-table th {
        background: #f5f7fa;
        color: #526174;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .4px;
        white-space: nowrap;
        padding: 13px;
    }

    .log-table td {
        padding: 13px;
        vertical-align: middle;
        font-size: 13px;
        color: #344256;
    }

    .log-user {
        font-weight: 600;
        color: #243247;
    }

    .log-subtext {
        font-size: 11px;
        color: #8793a3;
        margin-top: 3px;
    }

    .log-description {
        max-width: 300px;
        white-space: normal;
        line-height: 1.5;
    }

    .log-badge {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-create {
        background: #e5f7ed;
        color: #16834b;
    }

    .badge-update {
        background: #e7f0ff;
        color: #2463c7;
    }

    .badge-delete {
        background: #ffe9e9;
        color: #c03939;
    }

    .badge-login {
        background: #e8f5ff;
        color: #0875b9;
    }

    .badge-other {
        background: #f0edff;
        color: #7252b5;
    }

    .log-detail-btn {
        border: 1px solid #dce3ec;
        background: #fff;
        color: #426080;
        border-radius: 7px;
        padding: 5px 9px;
        font-size: 12px;
    }

    .log-detail-btn:hover {
        background: #eef5ff;
        border-color: #b8d1f3;
    }

    .log-empty {
        padding: 55px 15px;
        text-align: center;
        color: #8793a3;
    }

    .log-empty i {
        font-size: 38px;
        display: block;
        margin-bottom: 12px;
    }

    @media(max-width: 768px) {
        .log-title {
            font-size: 20px;
        }

        .log-filter .col-md-3,
        .log-filter .col-md-2 {
            width: 100%;
        }
    }
</style>

{{-- HEADER --}}
<div class="log-header">

    <div>
        <h2 class="log-title">
            <i class="bi bi-clock-history me-2"></i>
            Log Aktivitas User
        </h2>

        <div class="text-muted small mt-1">
            Riwayat aktivitas dan perubahan data pengguna aplikasi
        </div>
    </div>

    <a href="{{ route('create-user') }}"
       class="btn btn-outline-secondary">

        <i class="bi bi-arrow-left me-1"></i>
        Kembali ke Daftar User

    </a>

</div>

{{-- FILTER --}}
<div class="log-card mb-4">

    <div class="log-filter">

        <form action="{{ route('audit-logs.index') }}"
              method="GET">

            <div class="row g-2 align-items-end">

                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Cari User / Aktivitas
                    </label>

                    <input type="text"
                           name="q"
                           class="form-control"
                           placeholder="Nama user, modul, keterangan..."
                           value="{{ request('q') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        Modul
                    </label>

                    <select name="module" class="form-select">
                        <option value="">Semua Modul</option>

                        @foreach([
                            'auth' => 'Autentikasi',
                            'user' => 'User',
                            'master_barang' => 'Master Barang',
                            'penerimaan' => 'Penerimaan',
                            'pengeluaran' => 'Pengeluaran',
                            'penyesuaian_stok' => 'Penyesuaian Stok',
                            'stok_opname' => 'Stok Opname'
                        ] as $key => $label)

                            <option value="{{ $key }}"
                                @selected(request('module') == $key)>
                                {{ $label }}
                            </option>

                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        Aktivitas
                    </label>

                    <select name="action" class="form-select">
                        <option value="">Semua Aktivitas</option>

                        @foreach([
                            'login' => 'Login',
                            'logout' => 'Logout',
                            'create' => 'Tambah',
                            'update' => 'Ubah',
                            'delete' => 'Hapus',
                            'adjust' => 'Penyesuaian',
                            'finalize' => 'Finalisasi',
                            'cancel' => 'Pembatalan'
                        ] as $key => $label)

                            <option value="{{ $key }}"
                                @selected(request('action') == $key)>
                                {{ $label }}
                            </option>

                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        Dari Tanggal
                    </label>

                    <input type="date"
                           name="tanggal_awal"
                           class="form-control"
                           value="{{ request('tanggal_awal') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        Sampai Tanggal
                    </label>

                    <input type="date"
                           name="tanggal_akhir"
                           class="form-control"
                           value="{{ request('tanggal_akhir') }}">
                </div>

                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                        Cari
                    </button>
                </div>

            </div>

            <div class="mt-2">
                <a href="{{ route('audit-logs.index') }}"
                   class="small text-decoration-none">

                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Reset Filter

                </a>
            </div>

        </form>

    </div>

    {{-- TABLE --}}
    <div class="table-responsive">

        <table class="table table-hover log-table mb-0">

            <thead>
                <tr>
                    <th width="55">No</th>
                    <th>Waktu</th>
                    <th>Pengguna</th>
                    <th>Modul</th>
                    <th>Aktivitas</th>
                    <th>Keterangan</th>
                    <th>Cabang</th>
                    <th width="75">Detail</th>
                </tr>
            </thead>

            <tbody>

                @forelse($logs as $index => $log)

                    @php
                        $action = strtolower($log->action ?? '');

                        $badgeClass = match($action) {
                            'create' => 'badge-create',
                            'update' => 'badge-update',
                            'delete' => 'badge-delete',
                            'login', 'logout' => 'badge-login',
                            default => 'badge-other'
                        };

                        $actionLabel = match($action) {
                            'create' => 'Tambah',
                            'update' => 'Ubah',
                            'delete' => 'Hapus',
                            'login' => 'Login',
                            'logout' => 'Logout',
                            'adjust' => 'Penyesuaian',
                            'finalize' => 'Finalisasi',
                            'cancel' => 'Pembatalan',
                            default => ucfirst($action ?: 'Aktivitas')
                        };
                    @endphp

                    <tr>

                        <td>
                            {{ $logs->firstItem() + $index }}
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $log->created_at?->format('d/m/Y') ?? '-' }}
                            </div>

                            <div class="log-subtext">
                                {{ $log->created_at?->format('H:i:s') ?? '-' }}
                            </div>
                        </td>

                        <td>
                            <div class="log-user">
                                {{ $log->actor_name ?: $log->actor_user ?: 'System' }}
                            </div>

                            <div class="log-subtext">
                                {{ $log->actor_user ?? '-' }}
                            </div>
                        </td>

                        <td>
                            {{ ucwords(str_replace('_', ' ', $log->module ?? '-')) }}
                        </td>

                        <td>
                            <span class="log-badge {{ $badgeClass }}">
                                {{ $actionLabel }}
                            </span>
                        </td>

                        <td>
                            <div class="log-description">
                                {{ $log->description ?? '-' }}
                            </div>
                        </td>

                        <td>
                            {{ $log->branch?->nama_cabang ?? ($log->branch_id ? 'Cabang ID '.$log->branch_id : 'Pusat') }}
                        </td>

                        <td>
                            <button type="button"
                                class="log-detail-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#logDetail{{ $log->id }}">

                                <i class="bi bi-eye"></i>

                            </button>
                        </td>

                    </tr>

                    {{-- MODAL DETAIL --}}
                    <div class="modal fade"
                         id="logDetail{{ $log->id }}"
                         tabindex="-1"
                         aria-hidden="true">

                        <div class="modal-dialog modal-lg modal-dialog-centered">

                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="bi bi-journal-text me-2"></i>
                                        Detail Log Aktivitas
                                    </h5>

                                    <button type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal">
                                    </button>
                                </div>

                                <div class="modal-body">

                                    <div class="row g-3 mb-3">

                                        <div class="col-md-6">
                                            <small class="text-muted">Pengguna</small>
                                            <div class="fw-semibold">
                                                {{ $log->actor_name ?: $log->actor_user ?: 'System' }}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Waktu</small>
                                            <div class="fw-semibold">
                                                {{ $log->created_at?->format('d/m/Y H:i:s') ?? '-' }}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Modul</small>
                                            <div class="fw-semibold">
                                                {{ $log->module ?? '-' }}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Aktivitas</small>
                                            <div class="fw-semibold">
                                                {{ $actionLabel }}
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <small class="text-muted">Keterangan</small>
                                            <div class="fw-semibold">
                                                {{ $log->description ?? '-' }}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">IP Address</small>
                                            <div>
                                                {{ $log->ip_address ?? '-' }}
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <small class="text-muted">Subject</small>
                                            <div>
                                                {{ class_basename($log->subject_type ?? '-') }}
                                                {{ $log->subject_id ? '#'.$log->subject_id : '' }}
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <small class="text-muted">Role Saat Aktivitas</small>
                                            <div>
                                                @php
                                                    $roles = $log->actor_roles;

                                                    if (is_string($roles)) {
                                                        $roles = json_decode($roles, true) ?? [];
                                                    }
                                                @endphp

                                                {{ is_array($roles) ? implode(', ', $roles) : ($roles ?: '-') }}
                                            </div>
                                        </div>

                                    </div>

                                    <hr>

                                    <div class="row g-3">

                                        <div class="col-md-6">

                                            <h6 class="fw-bold text-danger">
                                                <i class="bi bi-arrow-left-circle me-1"></i>
                                                Data Sebelum
                                            </h6>

                                            <pre class="bg-light border rounded p-3 small"
                                                 style="max-height:300px;overflow:auto;white-space:pre-wrap;">{{ is_array($log->old_values) ? json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($log->old_values ? json_encode(json_decode($log->old_values, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-') }}</pre>

                                        </div>

                                        <div class="col-md-6">

                                            <h6 class="fw-bold text-success">
                                                <i class="bi bi-arrow-right-circle me-1"></i>
                                                Data Sesudah
                                            </h6>

                                            <pre class="bg-light border rounded p-3 small"
                                                 style="max-height:300px;overflow:auto;white-space:pre-wrap;">{{ is_array($log->new_values) ? json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($log->new_values ? json_encode(json_decode($log->new_values, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-') }}</pre>

                                        </div>

                                    </div>

                                    <div class="mt-3">
                                        <small class="text-muted">User Agent</small>
                                        <div class="small text-break">
                                            {{ $log->user_agent ?? '-' }}
                                        </div>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal">
                                        Tutup
                                    </button>
                                </div>

                            </div>

                        </div>

                    </div>

                @empty

                    <tr>
                        <td colspan="8">
                            <div class="log-empty">
                                <i class="bi bi-journal-x"></i>
                                <div class="fw-semibold">
                                    Belum ada aktivitas tercatat
                                </div>
                                <small>
                                    Log aktivitas pengguna akan muncul di halaman ini.
                                </small>
                            </div>
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    @if($logs->hasPages() || $logs->total() > 0)

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-3">

            <small class="text-muted">
                Menampilkan
                {{ $logs->firstItem() ?? 0 }}
                sampai
                {{ $logs->lastItem() ?? 0 }}
                dari
                {{ $logs->total() }} aktivitas
            </small>

            <div>
                {{ $logs->appends(request()->query())->links() }}
            </div>

        </div>

    @endif

</div>

@endsection
