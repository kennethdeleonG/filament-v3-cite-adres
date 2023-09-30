<?php

namespace App\Filament\Faculty\Resources;

use App\Domain\Faculty\Models\Faculty;
use App\Filament\Faculty\Resources\ActivityResource\Pages\ListActivities;
use App\Filament\Faculty\Resources\ActivityResource\Pages\ViewActivity;
use Filament\Tables;
use Carbon\Carbon;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Str;

class ActivityResource extends Resource
{
    protected static ?string $navigationLabel = 'Activity Logs';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function getLabel(): string
    {
        return __('filament-spatie-activitylog::activity.label');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-spatie-activitylog::activity.plural_label');
    }

    public static function getEloquentQuery(): Builder
    {
        $faculty = auth()->user();

        $logs = Activity::query()
            ->where('causer_type', 'App\Domain\Faculty\Models\Faculty')
            ->where('causer_id', $faculty->id)
            ->orderBy('created_at', 'desc');

        return $logs;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
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
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label(__('filament-spatie-activitylog::activity.subject'))
                    ->formatStateUsing(function ($state) {
                        return $state === 'App\Domain\Asset\Models\Asset' ? 'Document' : 'Folder';
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('filament-spatie-activitylog::activity.description'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('log_name')
                    ->label(__('filament-spatie-activitylog::activity.log')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-spatie-activitylog::activity.logged_at'))
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->multiple()
                    ->options([
                        'created' => 'Created',
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
            ])
            ->bulkActions([])
            ->defaultSort('id', 'DESC');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-spatie-laravel-activitylog.resource.group') ?? parent::getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-spatie-laravel-activitylog.resource.sort') ?? parent::getNavigationSort();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
            'view' => ViewActivity::route('/{record}'),
        ];
    }
}
