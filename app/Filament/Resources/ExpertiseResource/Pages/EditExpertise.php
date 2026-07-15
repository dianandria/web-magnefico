<?php

namespace App\Filament\Resources\ExpertiseResource\Pages;

use App\Filament\Resources\ExpertiseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpertise extends EditRecord
{
    protected static string $resource = ExpertiseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
