<?php

declare(strict_types=1);

namespace App\Support;

use App\Domain\Asset\Models\Asset;
use App\Domain\Report\Models\Report;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExportCollection implements FromCollection, WithHeadingRow
{
    public function __construct(
        protected Collection $data
    ) {
    }

    public function collection()
    {
        $records = new Collection();

        $headingRow = [
            'Owner', 'Name', 'Size', 'Created At'
        ];

        $records->push($headingRow);

        foreach ($this->data as $record) {
            $transformedRecord = [
                'owner' => $record->creator->last_name . ", " . $record->creator->first_name,
                'name' => $record->name,
                'size' => $this->getFolderSize($record),
                'created_at' => $record->created_at
            ];

            $records->push($transformedRecord);
        }

        return $records;
    }

    private function getFolderSize($record)
    {
        $folderIds = $record->descendants->pluck('id')->toArray();
        return $this->convertedAssetSize(
            (int) Asset::whereIn('folder_id', $folderIds)
                ->orWhere('folder_id', $record->id)
                ->sum('size')
        );
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
