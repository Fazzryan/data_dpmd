<?php

namespace App\Filament\Resources\DanaDesaResource\Pages;

use App\Filament\Resources\DanaDesaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDanaDesa extends EditRecord
{
    protected static string $resource = DanaDesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
