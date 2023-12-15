<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Resources\ArticleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

<<<<<<< HEAD
=======
    protected static ?string $pluralLabel = 'Artikel';


>>>>>>> origin/master
    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
