<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Nouveau message - ERAH</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.5; color: #111;">
    <h2 style="margin-bottom: 8px;">Nouveau message depuis le formulaire de contact</h2>

    <p><strong>Nom :</strong> {{ $payload['name'] }}</p>
    <p><strong>Email :</strong> {{ $payload['email'] }}</p>
    <p><strong>Sujet :</strong> {{ $payload['subject'] }}</p>

    <hr style="margin: 18px 0;">

    <p style="white-space: pre-line;">{{ $payload['message'] }}</p>
</body>
</html>

