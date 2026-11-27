<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $isGlobal = $user->isGlobalUser();

        $query = AuditLog::query();

        // Pembatasan data berdasarkan cabang.
        if (!$isGlobal) {
            abort_if(
                !$user->branch_id,
                403,
                'Akun Anda belum memiliki cabang.'
            );

            $query->where('branch_id', $user->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Pencarian aktivitas.
        if ($request->filled('q')) {
            $search = trim($request->q);

            $query->where(function ($q) use ($search) {
                $q->where('actor_user', 'like', "%{$search}%")
                    ->orWhere('actor_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%")
                    ->orWhere('subject_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->tanggal_mulai
            );
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->tanggal_selesai
            );
        }

        $logs = $query
            ->latest('created_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $branches = $isGlobal
            ? Branch::orderBy('nama_cabang')->get()
            : collect();

        $modules = AuditLog::query()
            ->when(!$isGlobal, function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })
            ->when($isGlobal && $request->filled('branch_id'), function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            })
            ->select('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view('audit-logs.index', compact(
            'logs',
            'branches',
            'modules',
            'isGlobal'
        ));
    }
}
