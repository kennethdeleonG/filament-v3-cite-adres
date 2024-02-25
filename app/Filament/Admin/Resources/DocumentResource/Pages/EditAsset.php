<?php

namespace App\Filament\Admin\Resources\DocumentResource\Pages;

use App\Filament\Admin\Resources\DocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Alignment;

class EditAsset extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    public static string | Alignment $formActionsAlignment = Alignment::Right;

    public string $docLabel = '';

    public function mount(int | string $record, string $label = ''): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();

        $this->docLabel = $label;
    }

    public function getBreadcrumbs(): array
    {
        return [
            $this->docLabel,
            $this->record->name,
            'Edit'
        ];
    }

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
