<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\Clip;
use App\Models\EsportMatch;
use App\Models\GiftRedemption;
use App\Models\MissionTemplate;
use App\Models\Notification;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'users_total' => (int) User::query()->count(),
            'admins_total' => (int) User::query()->where('role', User::ROLE_ADMIN)->count(),
            'clips_published' => (int) Clip::query()->where('is_published', true)->count(),
            'clips_draft' => (int) Clip::query()->where('is_published', false)->count(),
            'matches_open' => (int) EsportMatch::query()
                ->whereIn('status', [
                    EsportMatch::STATUS_SCHEDULED,
                    EsportMatch::STATUS_LOCKED,
                    EsportMatch::STATUS_LIVE,
                ])
                ->count(),
            'matches_settled' => (int) EsportMatch::query()->whereNotNull('settled_at')->count(),
            'bets_pending' => (int) Bet::query()
                ->whereIn('status', [Bet::STATUS_PENDING, Bet::STATUS_PLACED])
                ->count(),
            'bets_settled' => (int) Bet::query()
                ->whereIn('status', [Bet::STATUS_WON, Bet::STATUS_LOST, Bet::STATUS_VOID])
                ->count(),
            'notifications_unread' => (int) Notification::query()->whereNull('read_at')->count(),
            'redemptions_pending' => (int) GiftRedemption::query()
                ->where('status', GiftRedemption::STATUS_PENDING)
                ->count(),
            'missions_active' => (int) MissionTemplate::query()->where('is_active', true)->count(),
            'wallet_volume_today' => (int) WalletTransaction::query()
                ->whereDate('created_at', today())
                ->sum('amount'),
        ];

        $managementLinks = [
            [
                'title' => 'Utilisateurs',
                'description' => 'Roles, profils et progression globale des membres.',
                'route' => route('users.index'),
                'action' => 'Gerer users',
                'count' => $stats['users_total'],
            ],
            [
                'title' => 'Clips',
                'description' => 'Publier, modifier ou depublier les clips plateforme.',
                'route' => route('admin.clips.index'),
                'action' => 'Gerer clips',
                'count' => $stats['clips_published'],
            ],
            [
                'title' => 'Matchs',
                'description' => 'Creation, statut, resultat et settlement des matchs.',
                'route' => route('admin.matches.index'),
                'action' => 'Gerer matchs',
                'count' => $stats['matches_open'],
            ],
            [
                'title' => 'Missions',
                'description' => 'Templates missions, activation et suivi des cycles.',
                'route' => route('admin.missions.index'),
                'action' => 'Gerer missions',
                'count' => $stats['missions_active'],
            ],
            [
                'title' => 'Cadeaux',
                'description' => 'Catalogue gifts et traitement des redemptions.',
                'route' => route('admin.gifts.index'),
                'action' => 'Gerer cadeaux',
                'count' => $stats['redemptions_pending'],
            ],
            [
                'title' => 'Wallets',
                'description' => 'Credits bet_points et audit des transactions.',
                'route' => route('admin.wallets.grant.create'),
                'action' => 'Gerer wallets',
                'count' => $stats['wallet_volume_today'],
            ],
        ];

        return view('pages.admin.dashboard', [
            'stats' => $stats,
            'managementLinks' => $managementLinks,
        ]);
    }
}
