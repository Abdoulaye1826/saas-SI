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
    public function log(string $action, ?Model $model, string $description, ?Request $request = null): void
    {
        $request ??= request();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? $model::class : null,
            'model_id' => $model?->getKey(),
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
