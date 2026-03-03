<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\ContactRequest;
use App\Mail\MarketingContactMailable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('marketing.contact');
    }

    public function store(ContactRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        $to = config('mail.contact.address', config('mail.from.address'));

        if (filled($to)) {
            $mailable = new MarketingContactMailable($payload);

            if (config('queue.default') !== 'sync') {
                Mail::to($to)->queue($mailable);
            } else {
                Mail::to($to)->send($mailable);
            }
        }

        return redirect()
            ->route('marketing.contact')
            ->with('success', 'Votre message a bien ete envoye. Nous vous repondrons rapidement.');
    }
}


