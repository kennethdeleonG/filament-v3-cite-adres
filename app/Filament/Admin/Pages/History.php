<?php

namespace App\Filament\Admin\Pages;

use App\Domain\Asset\Models\Asset;
use App\Domain\Faculty\Models\Faculty;
use App\Domain\Folder\Models\Folder;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;
use Filament\Forms;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Str;

class History extends Page implements HasTable
{
    use InteractsWithTable;

    public ?string $subject_type = null;
    public ?int $subject_id = null;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.admin.history';

    protected static ?string $slug = '/documents/history/{subjectType?}/{subjectId?}';

    protected ?string $heading = '';

    public string $headerTitle = '';

    /** @var array */
    protected $queryString = [
        'tableFilters',
        'tableSortColumn',
        'tableSortDirection',
        'tableSearchQuery' => ['except' => ''],
        'tableColumnSearchQueries',
    ];

    public function mount(string $subjectType = null, string $subjectId = null): void
    {
        $this->subject_type = $subjectType == "folders" ? Folder::class : Asset::class;
        $this->subject_id = intval($subjectId);

        if ($subjectType == 'folders') {
            $folder = Folder::find(intval($subjectId));
            $this->headerTitle = $folder ? $folder->name : '';
        } else {
            $asset = Asset::find(intval($subjectId));
            $this->headerTitle = $asset ? $asset->name : '';
        }
    }

    // protected function getBreadcrumbs(): array
    // {
    //     return [
    //         url()->previous() => trans('Asset Management'),
    //         route('filament.resources.activities.index') => trans('Logs'),
    //         self::getUrl() => trans($this->headerTitle),
    //     ];
    // }

    /** @return \Illuminate\Database\Eloquent\Builder<Activity> */
    protected function getTableQuery(): Builder
    {

        $logs = Activity::query()
            ->where('subject_type', $this->subject_type)
            ->where('subject_id', $this->subject_id)
            ->orderBy('created_at', 'desc');

        return $logs;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')->label('ID'),
            Tables\Columns\TextColumn::make('description'),
            Tables\Columns\TextColumn::make('created_at')->label('Logged at')->sortable()
                ->dateTime(timezone: 'Asia/Manila'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\ViewAction::make()
                ->translateLabel()
                ->form([
                    Forms\Components\TextInput::make('causer_type')
                        ->label(__('filament-spatie-activitylog::activity.causer_type'))
                        ->formatStateUsing(function ($state) {
                            return $state === 'App\Domain\Faculty\Models\Faculty' ? 'Faculty' : 'Admin';
                        })
                        ->columnSpan([
                            'default' => 2,
                            'sm' => 1,
                        ]),
                    Forms\Components\TextInput::make('causer_id')
                        ->label('Causer Name')
                        ->formatStateUsing(function ($record) {
                            if ($record->causer_type === 'App\Domain\Faculty\Models\Faculty') {
                                $faculty = Faculty::find(intval($record->causer_id));
                                $name = Str::headline($faculty ? $faculty->first_name : '') . ' ' . Str::headline($faculty ? $faculty->last_name : '');

                                return $name;
                            } else {
                                return 'Admin';
                            }
                        })
                        ->columnSpan([
                            'default' => 2,
                            'sm' => 1,
                        ]),
                    Forms\Components\TextInput::make('subject_type')
                        ->label(__('filament-spatie-activitylog::activity.subject_type'))
                        ->formatStateUsing(function ($state) {
                            return $state === 'App\Domain\Asset\Models\Asset' ? 'Document' : 'Folder';
                        })
                        ->columnSpan([
                            'default' => 2,
                            'sm' => 1,
                        ]),
                    Forms\Components\TextInput::make('subject_id')
                        ->label(__('filament-spatie-activitylog::activity.subject_id'))
                        ->columnSpan([
                            'default' => 2,
                            'sm' => 1,
                        ]),
                    Forms\Components\TextInput::make('description')
                        ->label(__('filament-spatie-activitylog::activity.description'))->columnSpan(2),
                    Forms\Components\TextInput::make('batch_uuid')
                        ->label('Log Batch ID')->columnSpan(2),
                    Forms\Components\KeyValue::make('properties.attributes')
                        ->label(__('filament-spatie-activitylog::activity.attributes'))
                        ->columnSpan([
                            'default' => 2,
                            'sm' => 1,
                        ]),
                    Forms\Components\KeyValue::make('properties.old')
                        ->label(__('filament-spatie-activitylog::activity.old'))
                        ->columnSpan([
                            'default' => 2,
                            'sm' => 1,
                        ]),
                ]),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('event')
                ->multiple()
                ->options([
                    'created' => 'Created',
                    'moved' => 'Moved',
                    'updated' => 'Updated',
                    'deleted' => 'Deleted',
                ]),
            Tables\Filters\Filter::make('created_at')
                ->form([
                    Forms\Components\DatePicker::make('logged_from')
                        ->label('Logged from'),
                    Forms\Components\DatePicker::make('logged_until')
                        ->label('Logged until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['logged_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['logged_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['logged_from'] ?? null) {
                        $indicators['logged_from'] = 'Created from ' . Carbon::parse($data['logged_from'])->toFormattedDateString();
                    }

                    if ($data['logged_until'] ?? null) {
                        $indicators['logged_until'] = 'Created until ' . Carbon::parse($data['logged_until'])->toFormattedDateString();
                    }

                    return $indicators;
                }),

        ];
    }
}
