# Admin Emails

## Objectif

La section Admin Emails permet a un administrateur autorise d envoyer un email individuel administratif ou support, vers un membre de la plateforme ou vers une adresse externe, avec historique et statut consultables en base.

## Autorisation

- Acces reserve aux comptes `role=admin`
- Capacites definies par Gate:
  - `admin-emails.view`
  - `admin-emails.send`
- Les routes sont egalement protegees par le middleware `admin`

## Stockage en base

Table principale: `admin_outbound_emails`

Champs principaux:

- `sender_admin_user_id`
- `recipient_user_id` nullable
- `recipient_email`
- `recipient_name`
- `subject`
- `body_html`
- `body_text`
- `category`
- `status`
- `mailer`
- `provider`
- `provider_message_id`
- `queued_at`
- `sent_at`
- `failed_at`
- `failure_reason`
- `meta`

Statuts utilises:

- `draft`
- `queued`
- `sent`
- `failed`

## Templates reutilisables

La V1 utilise un catalogue simple en configuration dans `config/admin-email-templates.php`.

Modeles fournis:

- reponse support
- information compte
- information supporter
- recompense / cadeau
- autre / libre

Variables disponibles:

- `{name}`
- `{email}`
- `{platform_name}`

## Flux d envoi

1. L admin ouvre `Admin > Emails`
2. Il recherche un membre ou saisit un email externe
3. Il choisit un modele si besoin
4. Il redige le sujet et le contenu
5. Un brouillon `draft` est cree au moment de l apercu
6. L admin confirme l envoi
7. Le record passe a `queued` puis le job `SendAdminOutboundEmailJob` traite l envoi
8. Le statut final devient `sent` ou `failed`

## Queue et robustesse

- Si `QUEUE_CONNECTION != sync`, l envoi est passe au job `SendAdminOutboundEmailJob`
- Si `QUEUE_CONNECTION=sync`, le job est execute immediatement via `dispatchSync`
- Le record reste toujours en base meme en cas d echec
- Les doubles envois sont limites par:
  - un token de soumission cote compose
  - un token de confirmation cote preview
  - le controle de statut avant envoi

## Logs applicatifs

Evenements emis:

- `email_admin_created`
- `email_admin_queued`
- `email_admin_sent`
- `email_admin_failed`

Contexte journalise:

- `admin_id`
- `recipient_email`
- `record_id`
- `status`

## Audit logs

Actions d audit en base:

- `admin.emails.created`
- `admin.emails.queued`

Le detail principal de l historique reste dans `admin_outbound_emails`.

## Test local

1. Migrer la base
2. Verifier la config mail active
3. Lancer un worker si queue active: `php artisan queue:work --queue=default`
4. Connectez-vous avec un compte admin
5. Ouvrir `console/admin/emails`
6. Composer un email, verifier l apercu puis envoyer
7. Controler le statut dans l historique admin

## Verification production

- verifier que le worker queue tourne
- verifier la reception de l email cible
- verifier `failed_jobs`
- verifier les logs `email_admin_*`
- verifier les statuts `queued`, `sent`, `failed` dans `admin_outbound_emails`

## Non objectif V1

- pas de newsletter massive
- pas de multi-destinataires libres
- pas de marketing bulk
- pas de WYSIWYG complexe