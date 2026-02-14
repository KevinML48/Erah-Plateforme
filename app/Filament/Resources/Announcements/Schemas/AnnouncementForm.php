<?php

declare(strict_types=1);

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required()->maxLength(255),
                RichEditor::make('body')->required()->columnSpanFull(),
                Toggle::make('is_active')->default(true)->required(),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('ends_at')->afterOrEqual('starts_at'),
            ]);
    }
}
