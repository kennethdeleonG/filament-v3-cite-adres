<?php

declare(strict_types=1);

namespace App\Domain\Folder\Models;

use Illuminate\Database\Eloquent\Model;
use AlexJustesen\FilamentSpatieLaravelActivitylog\Contracts\IsActivitySubject;
use App\Domain\Asset\Models\Asset;
use App\Domain\Faculty\Models\Faculty;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;
use Kalnoy\Nestedset\NodeTrait;

class Folder extends Model implements IsActivitySubject
{
    use LogsActivity;
    use SoftDeletes;
    use NodeTrait;

    protected $fillable = [
        'uuid',
        'author_id',
        'author_type',
        'folder_id',
        'slug',
        'name',
        'path',
        'is_private',
    ];

    protected $casts = [
        'data' => 'json',
        'deleted_at' => 'datetime',
    ];

    /** @return string*/
    public function getParentIdName(): string
    {
        return 'folder_id';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'author_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'folder_id')->with(['parent', 'folders', 'assets']);
    }

    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class)->with(['folders', 'descendants', 'assets']);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function getParentKeyName(): string
    {
        return 'folder_id';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getActivitySubjectDescription(Activity $activity): string
    {
        return 'Folder: ' . $this->name;
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
