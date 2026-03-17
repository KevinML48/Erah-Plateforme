<?php

namespace App\Http\Controllers\Web\Admin;

use App\Application\Actions\Audit\StoreAuditLogAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Admin\PreviewAdminOutboundEmailRequest;
use App\Http\Requests\Web\Admin\SendAdminOutboundEmailRequest;
use App\Jobs\SendAdminOutboundEmailJob;
use App\Models\AdminOutboundEmail;
use App\Models\User;
use App\Support\AdminEmailTemplateCatalog;
use App\Support\QueueRouting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class AdminEmailController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('admin-emails.view');

        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', 'all'));
        $category = trim((string) $request->query('category', 'all'));
        $date = trim((string) $request->query('date', ''));

        if ($status !== 'all' && ! in_array($status, AdminOutboundEmail::statuses(), true)) {
            $status = 'all';
        }

        if ($category !== 'all' && ! in_array($category, AdminOutboundEmail::categories(), true)) {
            $category = 'all';
        }

        $emailsQuery = AdminOutboundEmail::query()
            ->with(['senderAdmin:id,name,email', 'recipientUser:id,name,email'])
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($category !== 'all', fn ($query) => $query->where('category', $category))
            ->when($date !== '', fn ($query) => $query->whereDate('created_at', $date))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested
                        ->where('recipient_email', 'like', '%'.$search.'%')
                        ->orWhere('recipient_name', 'like', '%'.$search.'%')
                        ->orWhere('subject', 'like', '%'.$search.'%')
                        ->orWhere('body_text', 'like', '%'.$search.'%')
                        ->orWhereHas('recipientUser', function ($recipientQuery) use ($search): void {
                            $recipientQuery->where('name', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%');
                        });
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $emails = $emailsQuery->paginate(20)->withQueryString();
        $baseQuery = AdminOutboundEmail::query();

        return view('pages.admin.emails.index', [
            'emails' => $emails,
            'search' => $search,
            'status' => $status,
            'category' => $category,
            'date' => $date,
            'statusLabels' => AdminOutboundEmail::statusLabels(),
            'categoryLabels' => AdminOutboundEmail::categoryLabels(),
            'stats' => [
                'total' => (clone $baseQuery)->count(),
                'draft' => (clone $baseQuery)->where('status', AdminOutboundEmail::STATUS_DRAFT)->count(),
                'queued' => (clone $baseQuery)->where('status', AdminOutboundEmail::STATUS_QUEUED)->count(),
                'sent' => (clone $baseQuery)->where('status', AdminOutboundEmail::STATUS_SENT)->count(),
                'failed' => (clone $baseQuery)->where('status', AdminOutboundEmail::STATUS_FAILED)->count(),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('admin-emails.send');

        $draftId = $request->integer('draft');
        $draft = $draftId > 0
            ? AdminOutboundEmail::query()->with(['recipientUser:id,name,email'])->findOrFail($draftId)
            : null;

        $recipientSearch = trim((string) $request->query('recipient_search', ''));
        $selectedUserId = (int) ($request->query('recipient_user_id', $draft?->recipient_user_id ?? 0));
        $selectedUser = $selectedUserId > 0
            ? User::query()->find($selectedUserId)
            : $draft?->recipientUser;

        $templateKey = trim((string) $request->query('template', data_get($draft?->meta, 'template_key', '')));
        $template = AdminEmailTemplateCatalog::find($templateKey);

        $userResults = collect();
        if ($recipientSearch !== '') {
            $userResults = User::query()
                ->with('socialAccounts:id,user_id,provider_user_id,email')
                ->where(function ($query) use ($recipientSearch): void {
                    $query->where('name', 'like', '%'.$recipientSearch.'%')
                        ->orWhere('email', 'like', '%'.$recipientSearch.'%')
                        ->orWhereHas('socialAccounts', function ($socialQuery) use ($recipientSearch): void {
                            $socialQuery->where('provider_user_id', 'like', '%'.$recipientSearch.'%')
                                ->orWhere('email', 'like', '%'.$recipientSearch.'%');
                        });
                })
                ->orderBy('name')
                ->limit(10)
                ->get();
        }

        $submissionToken = $this->issueComposeToken($request);

        return view('pages.admin.emails.create', [
            'draft' => $draft,
            'selectedUser' => $selectedUser,
            'userResults' => $userResults,
            'recipientSearch' => $recipientSearch,
            'submissionToken' => $submissionToken,
            'templates' => AdminEmailTemplateCatalog::all(),
            'selectedTemplateKey' => $templateKey,
            'template' => $template,
            'categoryLabels' => AdminOutboundEmail::categoryLabels(),
            'defaultValues' => [
                'subject' => $draft?->subject ?? ($template['subject'] ?? ''),
                'body_html' => $draft?->body_html ?? ($template['body'] ?? ''),
                'category' => $draft?->category ?? ($template['category'] ?? AdminOutboundEmail::CATEGORY_SUPPORT),
                'recipient_email' => $draft?->recipient_email ?? '',
                'recipient_name' => $draft?->recipient_name ?? '',
                'cc_admin' => (bool) data_get($draft?->meta, 'cc_admin', false),
            ],
        ]);
    }

    public function preview(
        PreviewAdminOutboundEmailRequest $request,
        StoreAuditLogAction $storeAuditLogAction
    ): View|RedirectResponse {
        Gate::authorize('admin-emails.send');

        if (! $this->consumeComposeToken($request, (string) $request->validated('submission_token'))) {
            return redirect()
                ->route('admin.emails.create')
                ->with('error', 'Le formulaire a deja ete soumis ou a expire. Rechargez la page puis recommencez.');
        }

        $payload = $request->validated();
        $recipientUser = ! empty($payload['recipient_user_id'])
            ? User::query()->findOrFail((int) $payload['recipient_user_id'])
            : null;

        $resolvedRecipientEmail = $recipientUser?->email ?: (string) ($payload['recipient_email'] ?? '');
        $resolvedRecipientName = $recipientUser?->name ?: trim((string) ($payload['recipient_name'] ?? ''));
        $templateKey = trim((string) ($payload['template_key'] ?? ''));

        $resolvedSubject = $this->replaceTemplateVariables((string) $payload['subject'], $resolvedRecipientName, $resolvedRecipientEmail);
        $resolvedBody = $this->replaceTemplateVariables((string) $payload['body_html'], $resolvedRecipientName, $resolvedRecipientEmail);
        $sanitizedBodyHtml = $this->sanitizeHtml($resolvedBody);
        $bodyText = $this->htmlToText($sanitizedBodyHtml);

        $email = AdminOutboundEmail::query()->create([
            'sender_admin_user_id' => $request->user()->id,
            'recipient_user_id' => $recipientUser?->id,
            'recipient_email' => $resolvedRecipientEmail,
            'recipient_name' => $resolvedRecipientName !== '' ? $resolvedRecipientName : null,
            'subject' => $resolvedSubject,
            'body_html' => $sanitizedBodyHtml,
            'body_text' => $bodyText,
            'category' => (string) $payload['category'],
            'status' => AdminOutboundEmail::STATUS_DRAFT,
            'mailer' => (string) config('mail.default'),
            'provider' => (string) config('mail.default'),
            'meta' => [
                'template_key' => $templateKey !== '' ? $templateKey : null,
                'cc_admin' => (bool) ($payload['cc_admin'] ?? false),
                'confirm_token' => (string) Str::uuid(),
                'recipient_source' => $recipientUser ? 'internal_user' : 'external_email',
            ],
        ]);

        $storeAuditLogAction->execute(
            action: 'admin.emails.created',
            actor: $request->user(),
            target: $email,
            context: [
                'record_id' => $email->id,
                'recipient_email' => $email->recipient_email,
                'recipient_user_id' => $email->recipient_user_id,
                'category' => $email->category,
            ],
        );

        Log::info('email_admin_created', [
            'admin_id' => $email->sender_admin_user_id,
            'recipient_email' => $email->recipient_email,
            'record_id' => $email->id,
            'status' => $email->status,
        ]);

        return view('pages.admin.emails.preview', [
            'email' => $email->fresh(['senderAdmin:id,name,email', 'recipientUser:id,name,email']),
            'categoryLabels' => AdminOutboundEmail::categoryLabels(),
        ]);
    }

    public function send(
        SendAdminOutboundEmailRequest $request,
        AdminOutboundEmail $adminEmail,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        Gate::authorize('admin-emails.send');

        if (! in_array((string) $adminEmail->status, [AdminOutboundEmail::STATUS_DRAFT, AdminOutboundEmail::STATUS_FAILED], true)) {
            return redirect()
                ->route('admin.emails.show', $adminEmail)
                ->with('error', 'Cet email a deja ete traite.');
        }

        if ((string) data_get($adminEmail->meta, 'confirm_token') !== (string) $request->validated('confirm_token')) {
            return redirect()
                ->route('admin.emails.show', $adminEmail)
                ->with('error', 'Confirmation invalide. Rechargez l apercu puis recommencez.');
        }

        return $this->dispatchAdminEmail($request->user(), $adminEmail, $storeAuditLogAction);
    }

    public function retry(
        Request $request,
        AdminOutboundEmail $adminEmail,
        StoreAuditLogAction $storeAuditLogAction
    ): RedirectResponse {
        Gate::authorize('admin-emails.send');

        if ($adminEmail->status !== AdminOutboundEmail::STATUS_FAILED) {
            return redirect()
                ->route('admin.emails.show', $adminEmail)
                ->with('error', 'Seuls les emails en echec peuvent etre relances.');
        }

        return $this->dispatchAdminEmail($request->user(), $adminEmail, $storeAuditLogAction, 'admin.emails.retried');
    }

    public function show(AdminOutboundEmail $adminEmail): View
    {
        Gate::authorize('admin-emails.view');

        return view('pages.admin.emails.show', [
            'email' => $adminEmail->load(['senderAdmin:id,name,email', 'recipientUser:id,name,email']),
            'categoryLabels' => AdminOutboundEmail::categoryLabels(),
            'statusLabels' => AdminOutboundEmail::statusLabels(),
        ]);
    }

    private function issueComposeToken(Request $request): string
    {
        $submissionToken = (string) Str::uuid();
        $tokens = collect((array) $request->session()->get('admin_email_submission_tokens', []))
            ->push($submissionToken)
            ->take(-20)
            ->values()
            ->all();

        $request->session()->put('admin_email_submission_tokens', $tokens);

        return $submissionToken;
    }

    private function consumeComposeToken(Request $request, string $submissionToken): bool
    {
        $tokens = collect((array) $request->session()->get('admin_email_submission_tokens', []))->values();

        if (! $tokens->contains($submissionToken)) {
            return false;
        }

        $request->session()->put(
            'admin_email_submission_tokens',
            $tokens->reject(fn (string $token): bool => $token === $submissionToken)->values()->all()
        );

        return true;
    }

    private function replaceTemplateVariables(string $value, ?string $recipientName, string $recipientEmail): string
    {
        return strtr($value, [
            '{name}' => $recipientName ?: 'membre ERAH',
            '{email}' => $recipientEmail,
            '{platform_name}' => (string) config('app.name', 'ERAH Plateforme'),
        ]);
    }

    private function sanitizeHtml(string $html): string
    {
        $trimmed = trim($html);
        if ($trimmed === '') {
            return '';
        }

        $containsHtml = $trimmed !== strip_tags($trimmed);
        if (! $containsHtml) {
            return nl2br(e($trimmed));
        }

        return (string) preg_replace(
            '/<a\s+([^>]*href=["\'][^"\']+["\'][^>]*)>/i',
            '<a $1 target="_blank" rel="noopener noreferrer">',
            strip_tags($trimmed, '<p><br><strong><em><ul><ol><li><a><blockquote>')
        );
    }

    private function htmlToText(string $html): string
    {
        $normalized = str_ireplace(['<br>', '<br/>', '<br />', '</p>', '</li>'], ["\n", "\n", "\n", "\n\n", "\n"], $html);

        return trim(html_entity_decode(strip_tags($normalized)));
    }

    private function dispatchAdminEmail(object $actor, AdminOutboundEmail $adminEmail, StoreAuditLogAction $storeAuditLogAction, string $auditAction = 'admin.emails.queued'): RedirectResponse
    {
        $queueConnection = QueueRouting::activeConnection();
        $queueName = QueueRouting::activeQueue();

        $adminEmail->forceFill([
            'status' => AdminOutboundEmail::STATUS_QUEUED,
            'queued_at' => now(),
            'failed_at' => null,
            'failure_reason' => null,
            'mailer' => (string) config('mail.default'),
            'provider' => (string) config('mail.default'),
        ])->save();

        $storeAuditLogAction->execute(
            action: $auditAction,
            actor: $actor,
            target: $adminEmail,
            context: [
                'record_id' => $adminEmail->id,
                'recipient_email' => $adminEmail->recipient_email,
                'queue_connection' => $queueConnection,
                'queue_name' => $queueName,
            ],
        );

        Log::info('email_admin_queued', [
            'admin_id' => $adminEmail->sender_admin_user_id,
            'recipient_email' => $adminEmail->recipient_email,
            'record_id' => $adminEmail->id,
            'status' => $adminEmail->status,
            'queue_connection' => $queueConnection,
            'queue_name' => $queueName,
        ]);

        try {
            if ($queueConnection !== 'sync') {
                SendAdminOutboundEmailJob::dispatch($adminEmail->id)->onQueue($queueName);

                return redirect()
                    ->route('admin.emails.show', $adminEmail)
                    ->with('success', 'Email place en queue ('.$queueName.').');
            }

            SendAdminOutboundEmailJob::dispatchSync($adminEmail->id);

            $adminEmail->refresh();

            return redirect()
                ->route('admin.emails.show', $adminEmail)
                ->with($adminEmail->status === AdminOutboundEmail::STATUS_SENT ? 'success' : 'error', $adminEmail->status === AdminOutboundEmail::STATUS_SENT
                    ? 'Email envoye.'
                    : 'L email a echoue. Consultez le detail pour le motif.');
        } catch (Throwable $exception) {
            return redirect()
                ->route('admin.emails.show', $adminEmail)
                ->with('error', 'L email a echoue. Consultez le detail pour le motif.');
        }
    }
}