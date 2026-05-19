<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Log an action to the audit trail.
     *
     * @param string $module Module name (residents, documents, blotters, etc.)
     * @param string $action Action type (created, updated, deleted, printed, released, etc.)
     * @param int|null $recordId The ID of the record being modified
     * @param array|null $oldData Previous state of the record
     * @param array|null $newData New state of the record
     * @param Request|null $request Optional request for IP detection
     * @return AuditLog
     */
    public function log(
        string $module,
        string $action,
        ?int $recordId = null,
        ?array $oldData = null,
        ?array $newData = null,
        ?\Illuminate\Http\Request $request = null
    ): AuditLog {
        $userId = Auth::id();
        $ipAddress = $request ? $request->ip() : request()->ip();

        return AuditLog::create([
            'user_id' => $userId,
            'module' => $module,
            'action' => $action,
            'record_id' => $recordId,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Log a create action.
     */
    public function created(string $module, int $recordId, array $data, ?\Illuminate\Http\Request $request = null): AuditLog
    {
        return $this->log($module, 'created', $recordId, null, $data, $request);
    }

    /**
     * Log an update action.
     */
    public function updated(string $module, int $recordId, array $oldData, array $newData, ?\Illuminate\Http\Request $request = null): AuditLog
    {
        return $this->log($module, 'updated', $recordId, $oldData, $newData, $request);
    }

    /**
     * Log a delete action.
     */
    public function deleted(string $module, int $recordId, array $data, ?\Illuminate\Http\Request $request = null): AuditLog
    {
        return $this->log($module, 'deleted', $recordId, $data, null, $request);
    }

    /**
     * Log a custom action (printed, released, etc.)
     */
    public function action(string $module, string $action, int $recordId, ?array $data = null, ?\Illuminate\Http\Request $request = null): AuditLog
    {
        return $this->log($module, $action, $recordId, null, $data, $request);
    }
}
