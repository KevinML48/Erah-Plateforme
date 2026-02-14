<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PointActivityController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user, 401);

        $user->loadMissing('rank');
        $logs = $user->pointLogs()
            ->latest('id')
            ->paginate(20);

        return view('pages.points.activity', [
            'title' => 'Activite points',
            'logs' => $logs,
            'user' => $user,
        ]);
    }
}

