<?php

namespace App\Http\Controllers\Web\Admin;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\UpdateContactMessageStatusRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'all'));

        if ($status !== 'all' && ! in_array($status, ContactMessage::statuses(), true)) {
            $status = 'all';
        }

        $messagesQuery = ContactMessage::query()
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested
                        ->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('subject', 'like', '%'.$search.'%')
                        ->orWhere('message', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $messages = $messagesQuery
            ->paginate(20)
            ->withQueryString();

        $baseQuery = ContactMessage::query();

        return view('pages.admin.contact-messages.index', [
            'messages' => $messages,
            'search' => $search,
            'status' => $status,
            'statusLabels' => ContactMessage::statusLabels(),
            'stats' => [
                'total' => (clone $baseQuery)->count(),
                'new' => (clone $baseQuery)->where('status', ContactMessage::STATUS_NEW)->count(),
                'processused' => (clone $baseQuery)->where('status', ContactMessage::STATUS_PROCESSED)->count(),
                'archived' => (clone $baseQuery)->where('status', ContactMessage::STATUS_ARCHIVED)->count(),
            ],
        ]);
    }

    public function show(ContactMessage $contactMessage): View
    {
        return view('pages.admin.contact-messages.show', [
            'contactMessage' => $contactMessage,
        ]);
    }

    public function updateStatus(
        UpdateContactMessageStatusRequest $request,
        ContactMessage $contactMessage,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        $newStatus = (string) $request->validated('status');
        $previousStatus = $contactMessage->status;

        $contactMessage->forceFill([
            'status' => $newStatus,
        ])->save();

        $storeAuditLogAction->execute(
            action: 'contact.messages.status.updated',
            actor: $request->user(),
            target: $contactMessage,
            context: [
                'contact_message_id' => $contactMessage->id,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
            ],
        );

        return back()->with('success', 'Statut du message mis a jour.');
    }
}

