<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\JsonResponse;

class AdminAuditController extends Controller
{
    public function index(): JsonResponse
    {
        $logs = AuditLog::query()
            ->with('actor:id,name,email')
            ->orderByDesc('id')
            ->paginate(100);

        return response()->json($logs);
    }
}

