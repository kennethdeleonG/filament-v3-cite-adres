<?php

namespace App\Filament\Admin\Resources;

use AlexJustesen\FilamentSpatieLaravelActivitylog\Resources\ActivityResource as BaseActivityResource;

class ActivityResource extends BaseActivityResource
{
    protected static ?string $navigationLabel = 'Activity Logs';

    protected static ?string $navigationGroup = 'System';
}
