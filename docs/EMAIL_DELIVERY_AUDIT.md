# Audit email Resend

## Resume d'audit

- Le flux du formulaire de contact enregistre deja correctement le message avant tentative d'envoi email, ce qui evite toute perte de demande en cas d'erreur mail.
- La pile mail ne supportait pas encore Resend dans la configuration Laravel, alors que le projet doit fonctionner avec `MAIL_MAILER=resend` sur Laravel Cloud.
- Avec `QUEUE_CONNECTION=database`, un worker de queue doit tourner en production pour traiter les emails mis en attente.
- Le formulaire de contact doit continuer a cibler `config('mail.contact.address')` / `config('mail.contact.name')`.

## Changements appliques

- Contact marketing: `App\Http\Controllers\Marketing\ContactController`
- Mailable contact queue-compatible: `App\Mail\MarketingContactMailable`
- Smoke test manuel: `App\Console\Commands\SendMailSmokeTest` et `App\Mail\MailSmokeTestMailable`
- Base pour futures notifications email utilisateur: le transport mail Laravel est maintenant pret pour Resend sans casser les autres mailers existants.

## Configuration Laravel Cloud / production

- Utiliser `QUEUE_CONNECTION=database`
- Configurer `MAIL_MAILER=resend`
- Renseigner `MAIL_FROM_ADDRESS` et `MAIL_CONTACT_ADDRESS`
- Renseigner `MAIL_FROM_NAME` et `MAIL_CONTACT_NAME`
- Renseigner `RESEND_API_KEY`
- Verifier le domaine expediteur dans Resend avant tout envoi de production
- Verifier qu'un worker tourne en continu: `php artisan queue:work --queue=default`

## Configuration locale recommandee

- Pour tester Resend en local, utiliser `MAIL_MAILER=resend` avec une `RESEND_API_KEY` de dev ou sandbox.
- Pour travailler sans envoi reel, garder `MAIL_MAILER=log` ou basculer sur `MAIL_MAILER=smtp` avec Mailpit.
- Valeurs locales pratiques avec Mailpit: `MAIL_HOST=127.0.0.1`, `MAIL_PORT=1025`, `MAIL_ENCRYPTION=null`.

## Verification manuelle

1. Migrer la base et verifier la presence des tables `jobs` et `failed_jobs`.
2. Configurer `MAIL_MAILER=resend`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`, `MAIL_CONTACT_ADDRESS`, `MAIL_CONTACT_NAME`, `RESEND_API_KEY`.
3. Lancer un worker: `php artisan queue:work --queue=default`.
4. Verifier le domaine expediteur dans Resend.
5. Envoyer un test immediat: `php artisan app:mail-smoke-test destinataire@example.com`.
6. Envoyer un test via la queue: `php artisan app:mail-smoke-test destinataire@example.com --queue`.
7. Verifier la reception de l'email, les logs applicatifs et la table `failed_jobs`.

## Couverture de test ajoutee

- `tests/Feature/Console/SendMailSmokeTestCommandTest.php`
- `tests/Feature/Web/MarketingContactFeatureTest.php`