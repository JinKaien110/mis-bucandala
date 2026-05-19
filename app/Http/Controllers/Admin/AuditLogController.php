<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'module' => ['nullable', 'string'],
            'action' => ['nullable', 'string'],
            'user_id' => ['nullable', 'integer'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $logs = AuditLog::with('user:id,email')
            ->when($validated['module'] ?? null, fn($q, $m) => $q->where('module', $m))
            ->when($validated['action'] ?? null, fn($q, $a) => $q->where('action', $a))
            ->when($validated['user_id'] ?? null, fn($q, $u) => $q->where('user_id', $u))
            ->when($validated['date_from'] ?? null, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($validated['date_to'] ?? null, fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($validated['search'] ?? null, function ($q) use ($validated) {
                $s = $validated['search'];
                $q->where(function ($qq) use ($s) {
                    $qq->where('module', 'like', "%{$s}%")
                       ->orWhere('action', 'like', "%{$s}%")
                       ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$s}%"));
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        // Get unique modules and actions for filters
        $modules = AuditLog::distinct()->pluck('module')->sort()->values();
        $actions = AuditLog::distinct()->pluck('action')->sort()->values();

        return view('admin.logs.index', [
            'logs' => $logs,
            'filters' => $validated,
            'modules' => $modules,
            'actions' => $actions,
        ]);
    }

    public function show(AuditLog $log)
    {
        $log->load('user');
        return view('admin.logs.show', ['log' => $log]);
    }
}
