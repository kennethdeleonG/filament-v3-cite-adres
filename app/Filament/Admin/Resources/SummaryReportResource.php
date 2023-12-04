<?php

namespace App\Filament\Admin\Resources;

use App\Domain\Faculty\Enums\FacultyStatuses;
use App\Domain\Faculty\Models\Faculty;
use App\Domain\Folder\Models\Folder;
use App\Filament\Admin\Resources\SummaryReportResource\Pages;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class SummaryReportResource extends Resource
{
    protected static ?string $model = Faculty::class;

    protected static ?string $navigationGroup = 'Documents';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Faculty Reports';

    protected static ?string $pluralModelLabel = 'Faculty';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('Name'))
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy('first_name', $direction);
                    })
                    ->formatStateUsing(function ($record) {
                        return Str::limit($record->first_name . ' ' . $record->last_name, 50);
                    }),
                Tables\Columns\TextColumn::make('designation')
                    ->label(trans('Designation'))
                    ->formatStateUsing(function ($record) {
                        return $record->designation ?? 'N/A';
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Remarks')
                    ->badge()
                    ->color('danger')
                    ->formatStateUsing(function ($livewire) {
                        $folder_id = $livewire->getTable()->getFilter('folder_id')->getState()['value'];

                        $folder = Folder::where('id', $folder_id)
                            ->where('due_date', '<=', now())->first();

                        if ($folder) {
                            return "LATE";
                        }
                    }),
            ])
            ->filters([
                SelectFilter::make('folder_id')
                    ->label('Folder')
                    ->options(function () {
                        return Folder::query()
                            ->whereNull('folder_id')
                            ->orderBy('name', 'asc')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        $query->when(filled($data['value']), function (Builder $query) use ($data) {
                            $query->whereDoesntHave('folders', function (Builder $query) use ($data) {
                                $query->whereDescendantOf($data['value']);
                            });
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('notify')
                    ->label('Notify')
                    ->requiresConfirmation()
                    ->color('primary')
                    ->action(function (Faculty $record, $livewire) {
                        $folder_id = $livewire->getTable()->getFilter('folder_id')->getState()['value'];

                        $folder = Folder::find($folder_id);

                        $record->notify(
                            Notification::make()
                                ->title('Please submit your file.')
                                ->body("Admin wants you to pass your $folder->name files.")
                                ->actions([
                                    Action::make('Mark as read')
                                        ->outlined()
                                        ->markAsRead(),
                                ])
                                ->toDatabase(),
                        );
                    })
                    ->hidden(function ($livewire) {
                        $folder_id = $livewire->getTable()->getFilter('folder_id')->getState()['value'];

                        return is_null($folder_id);
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSummaryReport::route('/'),
        ];
    }
}
