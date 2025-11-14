<?php

namespace App\Filament\Resources\BantuanProvinsiResource\Pages;

use App\Filament\Resources\BantuanProvinsiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBantuanProvinsis extends ListRecords
{
    protected static string $resource = BantuanProvinsiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
