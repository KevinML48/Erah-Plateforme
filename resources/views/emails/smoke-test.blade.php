<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Smoke test email ERAH</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h1 style="font-size: 20px; margin-bottom: 16px;">Smoke test email ERAH</h1>
    <p>{{ $messageLine }}</p>
    <p>Mailer actif : {{ $mailer }}</p>
    <p>Connexion queue : {{ $queueConnection }}</p>
    <p>Horodatage : {{ now()->format('d/m/Y H:i:s') }}</p>
</body>
</html>