<?php

declare(strict_types=1);

namespace App\Domain\Asset\Models;

use Illuminate\Database\Eloquent\Model;

use AlexJustesen\FilamentSpatieLaravelActivitylog\Contracts\IsActivitySubject;
use App\Domain\Folder\Models\Folder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Activitylog\Models\Activity;
use Spatie\Sluggable\SlugOptions;

class Asset extends Model implements IsActivitySubject
{
    use LogsActivity;
    use SoftDeletes;
    use HasSlug;

    protected $fillable = [
        'author_id',
        'author_type',
        'folder_id',
        'name',
        'slug',
        'path',
        'file',
        'technical_information',
        'size',
        'file_type',
        'is_private',
    ];

    protected $casts = [
        'technical_information' => 'array',
        'deleted_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getActivitySubjectDescription(Activity $activity): string
    {
        return 'Asset: ' . $this->name;
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class)->with('folders', 'assets')->withCount('folders', 'assets');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo($this->getRouteKeyName());
    }
}
