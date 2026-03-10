<?php

return [
    'assistant' => [
        'mode' => env('HELP_ASSISTANT_MODE', 'knowledge_base'),
        'placeholder' => env('HELP_ASSISTANT_PLACEHOLDER', 'Posez votre question sur ERAH, ses modules, ses points ou votre profil...'),
        'provider' => env('HELP_ASSISTANT_PROVIDER'),
    ],
];
