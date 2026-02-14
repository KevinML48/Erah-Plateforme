<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\InsufficientPointsException;
use App\Exceptions\InvalidTicketSelectionException;
use App\Exceptions\MatchLockedException;
use App\Exceptions\MatchNotOpenException;
use App\Exceptions\StakeLimitException;
use App\Exceptions\TicketAlreadyExistsException;
use App\Http\Requests\CreateTicketRequest;
use App\Enums\TicketStatus;
use App\Models\EsportMatch;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function store(CreateTicketRequest $request, EsportMatch $match, TicketService $ticketService): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        try {
            $ticket = $ticketService->createTicket(
                user: $user,
                match: $match,
                stake: (int) $request->integer('stake_points'),
                selections: (array) $request->input('selections', [])
            );
        } catch (
            MatchNotOpenException|
            MatchLockedException|
            TicketAlreadyExistsException|
            StakeLimitException|
            InvalidTicketSelectionException|
            InsufficientPointsException $exception
        ) {
            if (!$request->expectsJson()) {
                return back()->withInput()->withErrors(['ticket' => $exception->getMessage()]);
            }

            return response()->json(['message' => $exception->getMessage()], 422);
        }

        if (!$request->expectsJson()) {
            return redirect()->route('me.tickets.show', $ticket)->with('success', 'Ticket cree avec succes.');
        }

        return response()->json([
            'message' => 'Ticket created.',
            'ticket' => $ticket,
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, 401);

        $tickets = Ticket::query()
            ->with([
                'match:id,title,starts_at,status',
                'selections.option:id,label,key',
                'selections.market:id,name,code',
            ])
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json($tickets);
    }

    public function show(Request $request, Ticket $ticket): JsonResponse|View
    {
        $user = $request->user();
        abort_unless($user, 401);
        abort_unless($ticket->user_id === $user->id, 403);

        $ticket->load([
            'match:id,title,starts_at,status',
            'selections.option:id,label,key,odds_decimal',
            'selections.market:id,name,code,status',
        ]);

        if (!$request->expectsJson()) {
            $isValidated = $ticket->status === TicketStatus::Won;

            return view('pages.tickets.show', [
                'title' => 'Mon ticket',
                'ticket' => $ticket,
                'isValidated' => $isValidated,
            ]);
        }

        return response()->json(['ticket' => $ticket]);
    }
}
