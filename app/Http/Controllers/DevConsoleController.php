<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\View\View;
use ReflectionClass;

class DevConsoleController extends Controller
{
    public function index(): View
    {
        $dbPath = config('database.connections.sqlite.database');
        $queue = config('queue.default');
        $cache = config('cache.default');

        $status = [
            'Environment' => app()->environment(),
            'DB driver' => config('database.default'),
            'DB path' => is_string($dbPath) ? $dbPath : 'N/A',
            'Queue driver' => is_string($queue) ? $queue : 'N/A',
            'Cache driver' => is_string($cache) ? $cache : 'N/A',
            'Auth user' => Auth::check() ? (Auth::user()->email ?? 'connected') : 'guest',
            'Sanctum stateful' => (string) config('sanctum.stateful', 'N/A'),
            'Google client' => filled(config('services.google.client_id')) ? 'configured' : 'missing',
            'Discord client' => filled(config('services.discord.client_id')) ? 'configured' : 'missing',
        ];

        try {
            if (Schema::hasTable('users')) {
                $userColumns = Schema::getColumnListing('users');
                $select = array_values(array_filter(['id', 'name', 'email', 'role'], fn (string $col): bool => in_array($col, $userColumns, true)));
                $users = User::query()
                    ->select($select)
                    ->latest('id')
                    ->limit(20)
                    ->get();
            } else {
                $users = collect();
            }
        } catch (\Throwable) {
            $users = collect();
        }

        $quickTables = ['users', 'clips', 'duels', 'matches', 'bets', 'notifications', 'gifts', 'mission_templates'];
        $tableCounts = [];

        foreach ($quickTables as $table) {
            $tableCounts[] = [
                'table' => $table,
                'exists' => Schema::hasTable($table),
                'count' => Schema::hasTable($table) ? $this->safeCount($table) : null,
            ];
        }

        return view('dev-console.index', [
            'status' => $status,
            'users' => $users,
            'tableCounts' => $tableCounts,
            'availableJobs' => $this->discoverDispatchableJobs(),
        ]);
    }

    public function routes(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $prefix = trim((string) $request->query('prefix', ''));
        $method = strtoupper(trim((string) $request->query('method', '')));
        $token = (string) $request->session()->get('dev_api_token', '');
        $baseUrl = rtrim(config('app.url') ?: $request->getSchemeAndHttpHost(), '/');

        $allRoutes = collect(RouteFacade::getRoutes()->getRoutes())
            ->map(fn (Route $route): array => $this->mapRoute($route, $baseUrl, $token))
            ->values();

        $prefixes = $allRoutes
            ->map(function (array $route): string {
                $segment = explode('/', $route['uri'])[0] ?? '';
                return $segment !== '' ? $segment : '/';
            })
            ->unique()
            ->sort()
            ->values();

        $filtered = $allRoutes->filter(function (array $route) use ($search, $prefix, $method): bool {
            if ($method !== '' && !in_array($method, $route['methods'], true)) {
                return false;
            }

            if ($prefix !== '') {
                $needle = ltrim($prefix, '/');
                if ($needle !== '/' && !str_starts_with($route['uri'], $needle)) {
                    return false;
                }
            }

            if ($search !== '') {
                $haystack = strtolower($route['uri'].' '.$route['name'].' '.$route['action'].' '.implode(',', $route['middleware']));
                if (!str_contains($haystack, strtolower($search))) {
                    return false;
                }
            }

            return true;
        })->values();

        return view('dev-console.routes', [
            'routes' => $filtered,
            'prefixes' => $prefixes,
            'filters' => [
                'search' => $search,
                'prefix' => $prefix,
                'method' => $method,
            ],
        ]);
    }

    public function data(): View
    {
        $tables = collect(DB::select(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
        ))->pluck('name')->values();

        $tableSummaries = $tables->map(function (string $table): array {
            return [
                'name' => $table,
                'count' => $this->safeCount($table),
            ];
        });

        $keyTables = [
            'users',
            'clips',
            'clip_comments',
            'clip_likes',
            'clip_favorites',
            'duels',
            'matches',
            'bets',
            'notifications',
            'mission_templates',
            'mission_instances',
            'user_missions',
            'gifts',
            'gift_redemptions',
            'points_transactions',
            'wallet_transactions',
            'reward_wallet_transactions',
        ];

        $snapshots = [];
        foreach ($keyTables as $table) {
            $exists = $tables->contains($table);
            $snapshots[$table] = [
                'exists' => $exists,
                'rows' => $exists ? $this->latestRows($table, 8) : [],
            ];
        }

        return view('dev-console.data', [
            'tableSummaries' => $tableSummaries,
            'snapshots' => $snapshots,
        ]);
    }

    public function dbReset(Request $request): RedirectResponse
    {
        if ($request->input('confirm') !== 'RESET') {
            return back()->with('error', 'Confirmation invalide. Tape RESET pour confirmer.');
        }

        try {
            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            $output = trim(Artisan::output());

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('dev.index')
                ->with('success', 'Base reinitialisee (migrate:fresh --seed). '.$output);
        } catch (\Throwable $e) {
            return back()->with('error', 'Reset DB echoue: '.$e->getMessage());
        }
    }

    public function seed(): RedirectResponse
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            $messages = [trim(Artisan::output())];

            if (class_exists(User::class) && Schema::hasTable('users')) {
                $admin = User::query()->where('email', 'admin@erah.local')->first();
                if (!$admin) {
                    $admin = new User();
                    $admin->forceFill([
                        'name' => 'ERAH Admin',
                        'email' => 'admin@erah.local',
                        'password' => Hash::make('password'),
                    ]);

                    if (Schema::hasColumn('users', 'role')) {
                        $admin->forceFill(['role' => 'admin']);
                    }

                    $admin->save();
                }

                $messages[] = 'Admin local: '.$admin->email;

                if (method_exists(User::class, 'factory') && User::query()->count() < 8) {
                    User::factory()->count(8 - User::query()->count())->create();
                    $messages[] = 'Users supplementaires crees via factory.';
                }
            }

            return back()->with('success', implode(' | ', array_filter($messages)));
        } catch (\Throwable $e) {
            return back()->with('error', 'Seed echoue: '.$e->getMessage());
        }
    }

    public function impersonate(Request $request): RedirectResponse
    {
        if ($request->input('action') === 'logout') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('dev.index')->with('success', 'Session fermee.');
        }

        $identifier = trim((string) $request->input('identifier'));
        if ($identifier === '') {
            return back()->with('error', 'Renseigne un ID ou un email.');
        }

        if (!Schema::hasTable('users')) {
            return back()->with('error', 'Table users absente.');
        }

        $query = User::query();
        $user = is_numeric($identifier)
            ? $query->whereKey((int) $identifier)->first()
            : $query->where('email', $identifier)->first();

        if (!$user) {
            return back()->with('error', 'Utilisateur introuvable.');
        }

        Auth::loginUsingId($user->id, true);

        return back()->with('success', 'Connecte en tant que '.$user->email);
    }

    public function dispatchJob(Request $request): RedirectResponse
    {
        $jobClass = trim((string) $request->input('job_class'));
        $available = collect($this->discoverDispatchableJobs())->pluck('class');

        if ($jobClass === '' || !$available->contains($jobClass)) {
            return back()->with('error', 'Job invalide ou non dispatchable.');
        }

        try {
            $job = app($jobClass);
            dispatch($job);

            return back()->with('success', 'Job dispatch: '.$jobClass);
        } catch (\Throwable $e) {
            return back()->with('error', 'Dispatch job echoue: '.$e->getMessage());
        }
    }

    public function logs(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $logPath = storage_path('logs/laravel.log');
        $lines = [];

        if (is_file($logPath)) {
            $content = file_get_contents($logPath) ?: '';
            $allLines = preg_split('/\r\n|\r|\n/', trim($content)) ?: [];
            $allLines = array_filter($allLines, fn (string $line): bool => $line !== '');

            if ($search !== '') {
                $allLines = array_values(array_filter($allLines, fn (string $line): bool => str_contains(strtolower($line), strtolower($search))));
            }

            $lines = array_slice($allLines, -200);
        }

        return view('dev-console.logs', [
            'logPath' => $logPath,
            'lines' => $lines,
            'search' => $search,
        ]);
    }

    public function api(Request $request): View
    {
        $token = (string) $request->session()->get('dev_api_token', '');
        $baseUrl = rtrim(config('app.url') ?: $request->getSchemeAndHttpHost(), '/');
        $search = trim((string) $request->query('search', ''));
        $method = strtoupper(trim((string) $request->query('method', '')));

        $routes = collect(RouteFacade::getRoutes()->getRoutes())
            ->filter(fn (Route $route): bool => str_starts_with($route->uri(), 'api/'))
            ->map(fn (Route $route): array => $this->mapRoute($route, $baseUrl, $token))
            ->filter(function (array $route) use ($search, $method): bool {
                if ($method !== '' && !in_array($method, $route['methods'], true)) {
                    return false;
                }

                if ($search !== '') {
                    $haystack = strtolower($route['uri'].' '.$route['name'].' '.$route['action']);
                    if (!str_contains($haystack, strtolower($search))) {
                        return false;
                    }
                }

                return true;
            })
            ->values();

        return view('dev-console.api', [
            'routes' => $routes,
            'token' => $token,
            'filters' => [
                'search' => $search,
                'method' => $method,
            ],
        ]);
    }

    public function apiToken(Request $request): RedirectResponse
    {
        $token = trim((string) $request->input('token', ''));
        $request->session()->put('dev_api_token', $token);

        return back()->with('success', 'Token API mis a jour.');
    }

    private function safeCount(string $table): ?int
    {
        try {
            return DB::table($table)->count();
        } catch (\Throwable) {
            return null;
        }
    }

    private function latestRows(string $table, int $limit = 8): array
    {
        try {
            $columns = Schema::getColumnListing($table);
            $query = DB::table($table);

            if (in_array('id', $columns, true)) {
                $query->orderByDesc('id');
            } elseif (in_array('created_at', $columns, true)) {
                $query->orderByDesc('created_at');
            }

            $rows = $query->limit($limit)->get()->map(function (object $row): array {
                $data = (array) $row;
                foreach ($data as $key => $value) {
                    if (is_string($value) && mb_strlen($value) > 120) {
                        $data[$key] = mb_substr($value, 0, 120).'...';
                    } elseif (is_array($value) || is_object($value)) {
                        $data[$key] = json_encode($value);
                    }
                }

                return $data;
            })->all();

            return $rows;
        } catch (\Throwable) {
            return [];
        }
    }

    private function discoverDispatchableJobs(): array
    {
        $candidates = [
            \App\Jobs\LockMatchesJob::class,
            \App\Jobs\SettleMatchesJob::class,
            \App\Jobs\GenerateDailyMissionsJob::class,
            \App\Jobs\GenerateWeeklyMissionsJob::class,
        ];

        $jobs = [];
        foreach ($candidates as $class) {
            if (!class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);
            $constructor = $reflection->getConstructor();
            $required = $constructor ? $constructor->getNumberOfRequiredParameters() : 0;

            if ($required === 0) {
                $jobs[] = [
                    'class' => $class,
                    'label' => class_basename($class),
                ];
            }
        }

        return $jobs;
    }

    private function mapRoute(Route $route, string $baseUrl, string $token): array
    {
        $methods = array_values(array_filter($route->methods(), fn (string $m): bool => $m !== 'HEAD'));
        $primaryMethod = $methods[0] ?? 'GET';
        $uri = $route->uri();
        $action = $route->getActionName();
        $name = $route->getName() ?? '-';
        $middleware = $route->gatherMiddleware();
        $requiresToken = collect($middleware)->contains(fn (string $m): bool => str_contains($m, 'auth:sanctum'));
        $withToken = $requiresToken ? ($token !== '' ? $token : '<TOKEN>') : '';

        return [
            'methods' => $methods,
            'uri' => $uri,
            'name' => $name,
            'action' => $action,
            'middleware' => $middleware,
            'open_url' => !str_contains($uri, '{') ? $baseUrl.'/'.$uri : null,
            'curl' => $this->buildCurl(
                method: $primaryMethod,
                url: $baseUrl.'/'.$uri,
                token: $withToken,
                needsJson: in_array($primaryMethod, ['POST', 'PUT', 'PATCH', 'DELETE'], true),
            ),
        ];
    }

    private function buildCurl(string $method, string $url, string $token = '', bool $needsJson = false): string
    {
        $parts = [
            'curl -X '.$method,
            '"'.$url.'"',
        ];

        if ($token !== '') {
            $parts[] = '-H "Authorization: Bearer '.$token.'"';
        }

        if ($needsJson) {
            $parts[] = '-H "Content-Type: application/json"';
            $parts[] = "-d '{\"sample\":\"value\"}'";
        }

        return implode(' ', $parts);
    }
}
