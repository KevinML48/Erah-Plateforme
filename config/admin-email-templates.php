<?php

return [
    'templates' => [
        'support-response' => [
            'key' => 'support-response',
            'name' => 'Reponse support',
            'category' => 'support',
            'subject' => 'Retour de notre equipe support - {platform_name}',
            'body' => "Bonjour {name},\n\nNous revenons vers vous concernant votre demande support.\n\nMerci de votre patience.\n\nCordialement,\n{platform_name}",
        ],
        'account-information' => [
            'key' => 'account-information',
            'name' => 'Information compte',
            'category' => 'account',
            'subject' => 'Information concernant votre compte - {platform_name}',
            'body' => "Bonjour {name},\n\nNous vous contactons au sujet de votre compte {platform_name}.\n\nSi besoin, repondez directement a cet email.\n\nCordialement,\n{platform_name}",
        ],
        'supporter-information' => [
            'key' => 'supporter-information',
            'name' => 'Information supporter',
            'category' => 'supporter',
            'subject' => 'Information supporter - {platform_name}',
            'body' => "Bonjour {name},\n\nVoici une information importante concernant votre espace supporter.\n\nMerci pour votre soutien.\n\nCordialement,\n{platform_name}",
        ],
        'reward-update' => [
            'key' => 'reward-update',
            'name' => 'Recompense / cadeau',
            'category' => 'reward',
            'subject' => 'Mise a jour sur votre recompense - {platform_name}',
            'body' => "Bonjour {name},\n\nNous avons une mise a jour concernant votre recompense ou votre cadeau.\n\nCordialement,\n{platform_name}",
        ],
        'free-form' => [
            'key' => 'free-form',
            'name' => 'Autre / libre',
            'category' => 'other',
            'subject' => 'Message de {platform_name}',
            'body' => "Bonjour {name},\n\n\n\nCordialement,\n{platform_name}",
        ],
    ],
];