<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $emailRecord->subject }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.65;">
    {!! $emailRecord->body_html ?: nl2br(e($emailRecord->body_text ?? '')) !!}
</body>
</html>