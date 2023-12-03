<?php

namespace App\Filament\Admin\Resources\SummaryReportResource\Pages;

use App\Filament\Admin\Resources\FacultyResource;
use App\Filament\Admin\Resources\SummaryReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListSummaryReport extends ListRecords
{
    protected static string $resource = SummaryReportResource::class;

    public function getTitle(): string | Htmlable
    {
        return "List of faculties that didnt submit yet";
    }
}
