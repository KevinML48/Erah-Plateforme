<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminOperationsCockpitService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request, AdminOperationsCockpitService $cockpitService): View
    {
        $payload = $cockpitService->dashboardPayload($request->query());

        return view('pages.admin.dashboard', [
            ...$payload,
        ]);
    }
}
