<?php

namespace App\Http\Controllers\Api;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends BaseApiController
{
    /**
     * Display a listing of system activity logs (Admin / Sanctum Protected)
     */
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = (int) $request->get('per_page', 20);
        $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->sendResponse(
            $logs,
            'Data log aktivitas sistem berhasil diambil.'
        );
    }
}
