<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\ContactRequest;
use App\Mail\MarketingContactMailable;
use App\Models\ContactMessage;
use App\Support\QueueRouting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Illuminate\View\View;
use Illuminate\Support\Str;
use RuntimeException;

class ContactController extends Controller
{
    public function show(Request $request): View
    {
        $submissionToken = (string) Str::uuid();

        $tokens = collect((array) $request->session()->get('marketing_contact_submission_tokens', []))
            ->push($submissionToken)
            ->take(-15)
            ->values()
            ->all();

        $request->session()->put('marketing_contact_submission_tokens', $tokens);

        return view('marketing.contact', [
            'contactCategories' => ContactMessage::categoryLabels(),
            'contactSubmissionToken' => $submissionToken,
        ]);
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $submissionToken = (string) $payload['submission_token'];

        if (! $this->consumeSubmissionToken($request, $submissionToken)) {
            return redirect()
                ->route('marketing.contact')
                ->with('error', 'Votre formulaire a deja ete soumis ou a expire. Rechargez la page puis reessayez.')
                ->withInput($request->except(['website', 'submission_token']));
        }

        $contactMessage = ContactMessage::query()->create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'subject' => $payload['subject'],
            'category' => $payload['category'] ?? null,
            'message' => $payload['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => ContactMessage::STATUS_NEW,
        ]);

        $mailFailed = false;

        $toAddress = config('mail.contact.address', config('mail.from.address'));
        $toName = config('mail.contact.name', config('mail.from.name'));

        if (filled($toAddress)) {
            try {
                $this->dispatchContactEmail($contactMessage, (string) $toAddress, $toName);
            } catch (Throwable $exception) {
                Log::error('marketing.contact.mail_failed', [
                    'contact_message_id' => $contactMessage->id,
                    'mailer' => config('mail.default'),
                    'queue_connection' => config('queue.default'),
                    'contact_address' => $toAddress,
                    'error' => $exception->getMessage(),
                ]);
                report($exception);
                $mailFailed = true;
            }
        } else {
            Log::warning('marketing.contact.mail_skipped', [
                'contact_message_id' => $contactMessage->id,
                'reason' => 'missing_contact_recipient',
                'mailer' => config('mail.default'),
            ]);
            report(new RuntimeException('Contact email recipient is not configured.'));
            $mailFailed = true;
        }

        $response = redirect()
            ->route('marketing.contact')
            ->with('success', 'Votre message a bien ete enregistre. Notre equipe vous repondra rapidement.');

        if ($mailFailed) {
            $response->with('error', 'La notification email a echoue, mais votre demande est bien enregistree et sera traitee.');
        }

        return $response;
    }

    private function dispatchContactEmail(ContactMessage $contactMessage, string $toAddress, ?string $toName): void
    {
        $mailer = Mail::to($toAddress, $toName);
        $mailable = new MarketingContactMailable($contactMessage);
        $queueConnection = QueueRouting::activeConnection();
        $queueName = QueueRouting::activeQueue();

        if ($queueConnection !== 'sync') {
            $mailer->queue($mailable->onQueue($queueName));

            Log::info('marketing.contact.mail_queued', [
                'contact_message_id' => $contactMessage->id,
                'mailer' => config('mail.default'),
                'queue_connection' => $queueConnection,
                'queue_name' => $queueName,
                'contact_address' => $toAddress,
            ]);

            return;
        }

        $mailer->send($mailable);

        Log::info('marketing.contact.mail_sent', [
            'contact_message_id' => $contactMessage->id,
            'mailer' => config('mail.default'),
            'queue_connection' => $queueConnection,
            'queue_name' => $queueName,
            'contact_address' => $toAddress,
        ]);
    }

    private function consumeSubmissionToken(Request $request, string $submissionToken): bool
    {
        $tokens = collect((array) $request->session()->get('marketing_contact_submission_tokens', []))
            ->values();

        if (! $tokens->contains($submissionToken)) {
            return false;
        }

        $request->session()->put(
            'marketing_contact_submission_tokens',
            $tokens->reject(fn (string $token): bool => $token === $submissionToken)->values()->all()
        );

        return true;
    }
}


