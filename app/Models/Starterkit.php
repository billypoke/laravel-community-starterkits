<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Starterkit extends Model
{
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $appends = ['is_bookmarked'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'starterkit_bookmarks');
    }

    public function getIsBookmarkedAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        return $this->bookmarks()->where('user_id', auth()->id())->exists();
    }
}
