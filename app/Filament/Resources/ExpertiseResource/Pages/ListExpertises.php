<?php

namespace App\Filament\Resources\ExpertiseResource\Pages;

use App\Filament\Resources\ExpertiseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpertises extends ListRecords
{
    protected static string $resource = ExpertiseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
