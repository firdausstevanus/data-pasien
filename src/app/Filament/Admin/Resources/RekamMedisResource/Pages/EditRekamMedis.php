<?php

namespace App\Filament\Admin\Resources\RekamMedisResource\Pages;

use App\Filament\Admin\Resources\RekamMedisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRekamMedis extends EditRecord
{
    protected static string $resource = RekamMedisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
