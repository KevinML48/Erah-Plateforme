<?php

declare(strict_types=1);

namespace App\Filament\Resources\AdminAuditLogs\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AdminAuditLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('actor.name')->label('Actor')->placeholder('-'),
                TextEntry::make('action'),
                TextEntry::make('entity_type'),
                TextEntry::make('entity_id')->numeric()->placeholder('-'),
                KeyValueEntry::make('metadata_json')->label('Metadata')->columnSpanFull(),
                TextEntry::make('ip')->placeholder('-'),
                TextEntry::make('user_agent')->placeholder('-')->limit(120),
                TextEntry::make('created_at')->dateTime(),
            ]);
    }
}
