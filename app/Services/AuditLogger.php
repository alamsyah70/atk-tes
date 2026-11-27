<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Mencatat aktivitas pengguna.
     *
     * Panggil di dalam DB::transaction() jika audit
     * harus ikut dibatalkan ketika transaksi gagal.
     */
    public static function log(
        string $action,
        string $module,
        string $description,
        ?Model $subject = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?int $branchId = null
    ): AuditLog {
        $user = Auth::user();

        // Jangan simpan kredensial atau data rahasia.
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'remember_token',
            'token',
            'api_token',
            'secret',
        ];

        $sanitize = function (?array $values) use ($sensitiveKeys) {
            if ($values === null) {
                return null;
            }

            foreach ($values as $key => $value) {
                if (in_array(strtolower((string) $key), $sensitiveKeys, true)) {
                    unset($values[$key]);
                }
            }

            return $values;
        };

        return AuditLog::create([
            'user_id' => $user?->id,
            'branch_id' => $branchId ?? $user?->branch_id,

            // Kolom login aplikasi adalah users.user.
            'actor_user' => $user?->user,
            'actor_name' => $user?->name
                ?? $user?->user
                ?? 'System',

            'actor_roles' => $user
                ? $user->getRoleNames()->values()->all()
                : [],

            'action' => $action,
            'module' => $module,
            'description' => $description,

            'subject_type' => $subject
                ? get_class($subject)
                : null,

            'subject_id' => $subject?->getKey(),

            'old_values' => $sanitize($oldValues),
            'new_values' => $sanitize($newValues),

            'ip_address' => request()?->ip(),
            'user_agent' => substr(
                (string) request()?->userAgent(),
                0,
                2000
            ),

            'created_at' => now(),
        ]);
    }
}
