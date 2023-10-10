<?php

namespace App\Filament\Admin\Pages;

use App\Domain\Asset\Models\Asset;
use App\Domain\Faculty\Models\Faculty;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;
use App\Domain\Folder\Models\Folder;
use App\Support\Enums\UserType;
use App\Support\ExportCollection;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Facades\Excel;

class Reports extends Page implements HasTable
{
    use InteractsWithTable;

    public Folder $folder;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.admin.reports';

    protected static ?string $slug = '/reports/{folderId?}';

    public function mount(string $folderId = null): void
    {
        $this->folder = Folder::find($folderId);
    }

    public function getHeading(): string | Htmlable
    {
        return $this->folder->name . ' Reports';
    }

    protected function getTableQuery(): Builder
    {
        $result = Folder::with(['descendants', 'creator' => function ($query) {
            $query->orderBy('last_name', 'asc');
        }])
            ->where('folder_id', $this->folder->id)
            ->where('author_type', UserType::FACULTY->value);

        return $result;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('uuid')
                ->label('Owner')
                ->formatStateUsing(function ($record) {
                    $faculty = Faculty::find($record->author_id);

                    return $faculty->last_name . ", " . $faculty->first_name;
                }),
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('id')
                ->label('Size')
                ->formatStateUsing(function ($record) {
                    $folderIds = $record->descendants->pluck('id')->toArray();
                    return self::convertedAssetSize(
                        (int) Asset::whereIn('folder_id', $folderIds)
                            ->orWhere('folder_id', $record->id)
                            ->sum('size')
                    );
                }),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Date Modified'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
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
                        $indicators['logged_from'] = 'Created from ' . $data['logged_from'];
                    }

                    if ($data['logged_until'] ?? null) {
                        $indicators['logged_until'] = 'Created until ' . Carbon::parse($data['logged_until'])->toFormattedDateString();
                    }

                    return $indicators;
                }),

        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkAction::make('export')
                ->action(function (Collection $records) {
                    return Excel::download(new ExportCollection($records), $this->folder->name . '_' . Carbon::now('Asia/Manila') . '.xls');
                }),
        ];
    }

    public function convertedAssetSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
