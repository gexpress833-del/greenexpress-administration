<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function log(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?string $description = null,
        ?int $userId = null,
        ?string $ipAddress = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'ip_address' => $ipAddress,
        ]);
    }

    public function logFromRequest(Request $request, string $action, ?string $modelType = null, ?int $modelId = null, ?string $description = null): ActivityLog
    {
        return $this->log(
            $action,
            $modelType,
            $modelId,
            $description,
            $request->user()?->id,
            $request->ip()
        );
    }
}
