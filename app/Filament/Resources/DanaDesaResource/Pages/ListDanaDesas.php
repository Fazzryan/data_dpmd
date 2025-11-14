<?php

namespace App\Filament\Resources\DanaDesaResource\Pages;

use App\Filament\Resources\DanaDesaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDanaDesas extends ListRecords
{
    protected static string $resource = DanaDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
