<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\ContactRequest;
use App\Mail\MarketingContactMailable;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\View\View;

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
                $mailable = new MarketingContactMailable($contactMessage);

                if (config('queue.default') !== 'sync') {
                    Mail::to($toAddress, $toName)->queue($mailable);
                } else {
                    Mail::to($toAddress, $toName)->send($mailable);
                }
            } catch (Throwable $exception) {
                report($exception);
                $mailFailed = true;
            }
        }

        $response = redirect()
            ->route('marketing.contact')
            ->with('success', 'Votre message a bien ete enregistre. Notre equipe vous repondra rapidement.');

        if ($mailFailed) {
            $response->with('error', 'La notification email a echoue, mais votre demande est bien enregistree et sera traitee.');
        }

        return $response;
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


