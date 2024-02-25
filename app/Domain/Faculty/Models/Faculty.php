<?php

declare(strict_types=1);

namespace App\Domain\Faculty\Models;

use App\Domain\Faculty\Enums\FacultyStatuses;
use App\Domain\Faculty\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Faculty extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use SoftDeletes;
    use Notifiable;
    use InteractsWithMedia;

    protected $guard = 'faculties';

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'address',
        'mobile',
        'gender',
        'designation',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => FacultyStatuses::class,
        'email_verified_at' => 'datetime',
        'password' => 'hashed'
    ];

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail());
    }

    public function registerMediaCollections(): void
    {
        $registerMediaConversions = function (Media $media) {
            $this->addMediaConversion('preview')
                ->fit(Manipulations::FIT_CROP, 300, 300);
        };

        $this->addMediaCollection('image')
            ->singleFile()
            ->registerMediaConversions($registerMediaConversions);
    }
}
