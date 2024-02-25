<?php

namespace App\Filament\Faculty\Resources\DocumentResource\Pages;

use App\Domain\Asset\Actions\CreateAssetAction;
use App\Domain\Asset\DataTransferObjects\AssetData;
use App\Domain\Folder\Models\Folder;
use App\Filament\Faculty\Resources\DocumentResource;
use App\Support\Enums\UserType;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    protected ?string $heading = 'Create Document';

    public mixed $ownerRecord = null;

    public static string | Alignment $formActionsAlignment = Alignment::Right;

    public function mount(string $ownerRecord = '', string $label = ''): void
    {
        $this->ownerRecord = app(Folder::class)->resolveRouteBinding($ownerRecord);

        $this->heading = $label;

        parent::mount();
    }

    public function hydrate()
    {
        $components = request()->input('components', []);

        if (isset($components[0]['snapshot'])) {
            $snapshotData = json_decode($components[0]['snapshot'], true);

            $path = $snapshotData['memo']['path'];

            $pathSegments = explode('/', $path);

            $value = end($pathSegments);

            $this->heading = $value;
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            $this->heading,
            "Create",
        ];
    }

    public static function canCreateAnother(): bool
    {
        return false;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data['folder_id'] = $this->ownerRecord ? $this->ownerRecord->id : null;
        $data['path'] = $this->ownerRecord ? $this->ownerRecord->path : '' . '/' . Str::slug($data['name']);
        $data['author_id'] = auth()->user()->id;
        $data['author_type'] = UserType::FACULTY->value;

        return DB::transaction(
            fn () => app(CreateAssetAction::class)
                ->execute(AssetData::fromArray($data))
        );
    }

    protected function getRedirectUrl(): string
    {
        $resource = static::getResource();

        return $resource::getUrl(
            'edit',
            [
                $this->record,
                $this->ownerRecord,
                'label' => $this->heading
            ]
        );
    }
}
