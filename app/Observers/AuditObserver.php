<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    /**
     * Handle the model "created" event.
     */
    public function created($model): void
    {
        $this->logActivity('created', $model);
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated($model): void
    {
        $this->logActivity('updated', $model);
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted($model): void
    {
        $this->logActivity('deleted', $model);
    }

    /**
     * Log activity to the audit trail.
     */
    private function logActivity(string $action, $model): void
    {
        // Only log for specific models
        $allowedModels = [
            'Resident' => 'residents',
            'DocumentType' => 'document_types',
            'DocumentRequest' => 'document_requests',
            'Blotter' => 'blotters',
            'CaseFile' => 'cases',
            'Household' => 'households',
            'Official' => 'officials',
            'Event' => 'events',
            'Pet' => 'pets',
            'Announcement' => 'announcements',
        ];

        $modelName = class_basename($model);
        
        if (!isset($allowedModels[$modelName])) {
            return;
        }

        $module = $allowedModels[$modelName];
        
        // Skip logging if user is not authenticated (e.g., public registration)
        if (!Auth::check()) {
            return;
        }

        $userId = Auth::id();
        $recordId = $model->id ?? null;
        
        // Get old and new data for updates
        $oldData = null;
        $newData = null;
        
        if ($action === 'updated' && $model->wasChanged()) {
            $oldData = $model->getOriginal();
            $newData = $model->getAttributes();
        } elseif ($action === 'created') {
            $newData = $model->getAttributes();
        } elseif ($action === 'deleted') {
            $oldData = $model->getOriginal();
        }

        // Clean up data - remove sensitive fields and large text fields
        $oldData = $this->cleanData($oldData);
        $newData = $this->cleanData($newData);

        try {
            AuditLog::create([
                'user_id' => $userId,
                'module' => $module,
                'action' => $action,
                'record_id' => $recordId,
                'old_data' => $oldData,
                'new_data' => $newData,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't break the application if logging fails
        }
    }

    /**
     * Clean data before storing in audit log.
     */
    private function cleanData(?array $data): ?array
    {
        if (empty($data)) {
            return null;
        }

        // Fields to exclude from logging
        $excludeFields = [
            'password',
            'password_confirmation',
            'token',
            'remember_token',
            'api_token',
            'otp_code',
            'otp_expires_at',
            'photo',
            'photo_path',
            'template_path',
            'created_at',
            'updated_at',
        ];

        // Limit text field lengths
        $textFields = [
            'description',
            'narrative',
            'remarks',
            'address',
        ];

        foreach ($data as $key => $value) {
            // Remove excluded fields
            if (in_array($key, $excludeFields)) {
                unset($data[$key]);
                continue;
            }

            // Truncate long text fields
            if (in_array($key, $textFields) && is_string($value) && strlen($value) > 200) {
                $data[$key] = substr($value, 0, 200) . '...';
            }
        }

        return empty($data) ? null : $data;
    }
}
