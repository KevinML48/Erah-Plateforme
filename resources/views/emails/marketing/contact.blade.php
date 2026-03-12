<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Nouveau message - ERAH</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.5; color: #111;">
    <h2 style="margin-bottom: 8px;">Nouvelle demande de contact ERAH</h2>

    <table cellpadding="8" cellspacing="0" border="0" style="border-collapse: collapse; width: 100%; max-width: 720px;">
        <tbody>
            <tr>
                <td style="width: 180px;"><strong>Nom</strong></td>
                <td>{{ $contactMessage->name }}</td>
            </tr>
            <tr>
                <td><strong>Email</strong></td>
                <td>{{ $contactMessage->email }}</td>
            </tr>
            <tr>
                <td><strong>Categorie</strong></td>
                <td>{{ $contactMessage->categoryLabel() }}</td>
            </tr>
            <tr>
                <td><strong>Sujet</strong></td>
                <td>{{ $contactMessage->subject }}</td>
            </tr>
            <tr>
                <td><strong>Date</strong></td>
                <td>{{ optional($contactMessage->created_at)->format('d/m/Y H:i') ?: now()->format('d/m/Y H:i') }}</td>
            </tr>
        </tbody>
    </table>

    <hr style="margin: 18px 0;">

    <p style="margin: 0 0 6px;"><strong>Message</strong></p>
    <p style="white-space: pre-line; margin: 0;">{{ $contactMessage->message }}</p>
</body>
</html>
