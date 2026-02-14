<?php

namespace App\Filament\Resources\AdminAuditLogs\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AdminAuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('actor_user_id')->numeric()->disabled(),
                TextInput::make('action')->disabled(),
                TextInput::make('entity_type')->disabled(),
                TextInput::make('entity_id')->numeric()->disabled(),
                KeyValue::make('metadata_json')->disabled()->columnSpanFull(),
            ]);
    }
}
