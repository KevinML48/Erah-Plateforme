<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContentPages\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ContentPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')->required()->alphaDash()->maxLength(255)->unique(ignoreRecord: true),
                TextInput::make('title')->required()->maxLength(255),
                RichEditor::make('body')->required()->columnSpanFull(),
                Toggle::make('is_active')->default(true)->required(),
            ]);
    }
}
