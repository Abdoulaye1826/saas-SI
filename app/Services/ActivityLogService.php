<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Journalise les actions utilisateurs dans activity_logs.
 */
class ActivityLogService
{
    /**
     * @param array<string, mixed>|null $oldValues Valeurs avant modification (audit financier).
     * @param array<string, mixed>|null $newValues Valeurs après modification (audit financier).
     */
    public function log(
        string $action,
        ?Model $model,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?Request $request = null
    ): void {
        $request ??= request();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? $model::class : null,
            'model_id' => $model?->getKey(),
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
