<?php

namespace Tests\Feature\Web;

use App\Mail\MarketingContactMailable;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class MarketingContactFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_is_accessible(): void
    {
        $this->get(route('marketing.contact'))
            ->assertOk()
            ->assertSee('Contact')
            ->assertSee('name="category"', false);
    }

    public function test_guest_contact_page_keeps_email_field_editable(): void
    {
        $this->get(route('marketing.contact'))
            ->assertOk()
            ->assertSee('name="email"', false)
            ->assertDontSee('readonly', false)
            ->assertDontSee('Email recupere depuis votre compte connecte.');
    }

    public function test_authenticated_contact_page_prefills_account_email_and_marks_it_readonly(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'member@erah.test',
        ]);

        $this->actingAs($user)
            ->get(route('marketing.contact'))
            ->assertOk()
            ->assertSee('name="email"', false)
            ->assertSee('value="member@erah.test"', false)
            ->assertSee('readonly', false)
            ->assertSee('Email recupere depuis votre compte connecte.');
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $token = $this->freshSubmissionToken();

        $this->post(route('marketing.contact.submit'), [
            'name' => '',
            'email' => 'invalid-email',
            'category' => 'invalid-category',
            'subject' => '',
            'message' => 'court',
            'website' => '',
            'submission_token' => $token,
        ])->assertSessionHasErrors([
            'name',
            'email',
            'category',
            'subject',
            'message',
        ]);
    }

    public function test_contact_form_stores_message_and_sends_email(): void
    {
        Mail::fake();

        config([
            'mail.contact.address' => 'contact@erah.local',
            'queue.default' => 'sync',
        ]);

        $token = $this->freshSubmissionToken();

        $this->post(route('marketing.contact.submit'), [
            'name' => 'Kylian Frost',
            'email' => 'kylian@example.com',
            'category' => ContactMessage::CATEGORY_PARTNERSHIP,
            'subject' => 'Question partenariat',
            'message' => 'Bonjour, nous aimerions organiser un partenariat local.',
            'website' => '',
            'submission_token' => $token,
        ])->assertRedirect(route('marketing.contact'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Kylian Frost',
            'email' => 'kylian@example.com',
            'category' => ContactMessage::CATEGORY_PARTNERSHIP,
            'subject' => 'Question partenariat',
            'status' => ContactMessage::STATUS_NEW,
        ]);

        Mail::assertSent(MarketingContactMailable::class, 1);
    }

    public function test_contact_form_queues_email_when_queue_is_active(): void
    {
        Mail::fake();

        config([
            'mail.contact.address' => 'contact@erah.local',
            'queue.default' => 'database',
        ]);

        $token = $this->freshSubmissionToken();

        $this->post(route('marketing.contact.submit'), [
            'name' => 'Queue User',
            'email' => 'queue@example.com',
            'category' => ContactMessage::CATEGORY_SUPPORT,
            'subject' => 'Question queue',
            'message' => 'Ce message doit etre mis en queue.',
            'website' => '',
            'submission_token' => $token,
        ])->assertRedirect(route('marketing.contact'))
            ->assertSessionHas('success');

        Mail::assertQueued(MarketingContactMailable::class, 1);
    }

    public function test_contact_form_keeps_message_when_email_fails(): void
    {
        config([
            'mail.contact.address' => 'contact@erah.local',
            'queue.default' => 'sync',
        ]);

        Mail::shouldReceive('to')
            ->once()
            ->andThrow(new RuntimeException('smtp failure'));

        $token = $this->freshSubmissionToken();

        $this->post(route('marketing.contact.submit'), [
            'name' => 'Lena Star',
            'email' => 'lena@example.com',
            'category' => ContactMessage::CATEGORY_SUPPORT,
            'subject' => 'Support compte',
            'message' => 'Je rencontre un blocage sur mon compte membre.',
            'website' => '',
            'submission_token' => $token,
        ])->assertRedirect(route('marketing.contact'))
            ->assertSessionHas('success')
            ->assertSessionHas('error');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Lena Star',
            'email' => 'lena@example.com',
            'subject' => 'Support compte',
            'status' => ContactMessage::STATUS_NEW,
        ]);
    }

    public function test_contact_form_reports_missing_recipient_configuration(): void
    {
        Mail::fake();

        config([
            'mail.contact.address' => null,
            'mail.from.address' => null,
            'queue.default' => 'sync',
        ]);

        $token = $this->freshSubmissionToken();

        $this->post(route('marketing.contact.submit'), [
            'name' => 'Lina Proxy',
            'email' => 'lina@example.com',
            'category' => ContactMessage::CATEGORY_OTHER,
            'subject' => 'Config test',
            'message' => 'Ce message doit signaler une configuration email manquante.',
            'website' => '',
            'submission_token' => $token,
        ])->assertRedirect(route('marketing.contact'))
            ->assertSessionHas('success')
            ->assertSessionHas('error');

        Mail::assertNothingSent();
    }

    public function test_contact_submission_token_prevents_duplicate_submit(): void
    {
        Mail::fake();

        config([
            'mail.contact.address' => 'contact@erah.local',
            'queue.default' => 'sync',
        ]);

        $token = $this->freshSubmissionToken();

        $payload = [
            'name' => 'Noah Prime',
            'email' => 'noah@example.com',
            'category' => ContactMessage::CATEGORY_JOIN_CLUB,
            'subject' => 'Candidature joueur',
            'message' => 'Je souhaite rejoindre le club pour la prochaine saison.',
            'website' => '',
            'submission_token' => $token,
        ];

        $this->post(route('marketing.contact.submit'), $payload)
            ->assertRedirect(route('marketing.contact'))
            ->assertSessionHas('success');

        $this->post(route('marketing.contact.submit'), $payload)
            ->assertRedirect(route('marketing.contact'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('contact_messages', 1);
    }

    public function test_authenticated_contact_form_uses_account_email_instead_of_browser_value(): void
    {
        Mail::fake();

        config([
            'mail.contact.address' => 'contact@erah.local',
            'queue.default' => 'sync',
        ]);

        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'secure-user@erah.test',
        ]);

        $token = $this->actingAs($user)->freshSubmissionToken();

        $this->post(route('marketing.contact.submit'), [
            'name' => 'Secure User',
            'email' => 'spoofed@example.com',
            'category' => ContactMessage::CATEGORY_SUPPORT,
            'subject' => 'Question support',
            'message' => 'Je souhaite confirmer que mon compte utilise bien son email source.',
            'website' => '',
            'submission_token' => $token,
        ])->assertRedirect(route('marketing.contact'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Secure User',
            'email' => 'secure-user@erah.test',
            'subject' => 'Question support',
            'status' => ContactMessage::STATUS_NEW,
        ]);

        $this->assertDatabaseMissing('contact_messages', [
            'email' => 'spoofed@example.com',
        ]);

        Mail::assertSent(MarketingContactMailable::class, 1);
    }

    public function test_admin_can_list_view_and_update_contact_messages(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $contactMessage = ContactMessage::query()->create([
            'name' => 'Sonia Vector',
            'email' => 'sonia@example.com',
            'subject' => 'Proposition evenement',
            'category' => ContactMessage::CATEGORY_EVENT_LAN,
            'message' => 'Nous pouvons co-organiser un evenement local.',
            'status' => ContactMessage::STATUS_NEW,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.contact-messages.index'))
            ->assertOk()
            ->assertSee('Demandes de contact')
            ->assertSee('Proposition evenement');

        $this->actingAs($admin)
            ->get(route('admin.contact-messages.show', $contactMessage))
            ->assertOk()
            ->assertSee('Message #'.$contactMessage->id)
            ->assertSee('Sonia Vector');

        $this->actingAs($admin)
            ->put(route('admin.contact-messages.status', $contactMessage), [
                'status' => ContactMessage::STATUS_PROCESSED,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('contact_messages', [
            'id' => $contactMessage->id,
            'status' => ContactMessage::STATUS_PROCESSED,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'contact.messages.status.updated',
            'target_id' => $contactMessage->id,
        ]);
    }

    public function test_non_admin_cannot_access_contact_messages_admin_pages(): void
    {
        /** @var User $member */
        $member = User::factory()->create(['role' => User::ROLE_USER]);
        $contactMessage = ContactMessage::query()->create([
            'name' => 'Member',
            'email' => 'member@example.com',
            'subject' => 'Question',
            'category' => ContactMessage::CATEGORY_OTHER,
            'message' => 'Message de test',
            'status' => ContactMessage::STATUS_NEW,
        ]);

        $this->actingAs($member)
            ->get(route('admin.contact-messages.index'))
            ->assertForbidden();

        $this->actingAs($member)
            ->get(route('admin.contact-messages.show', $contactMessage))
            ->assertForbidden();
    }

    private function freshSubmissionToken(): string
    {
        $this->get(route('marketing.contact'))->assertOk();

        return (string) collect(session('marketing_contact_submission_tokens', []))->last();
    }
}
