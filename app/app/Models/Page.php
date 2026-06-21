<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = ['slug', 'published_version_id'];

    public function versions(): HasMany
    {
        return $this->hasMany(PageVersion::class);
    }

    public function publishedVersion(): BelongsTo
    {
        return $this->belongsTo(PageVersion::class, 'published_version_id');
    }

    public function draft(): ?PageVersion
    {
        return $this->versions()->where('status', 'draft')->latest('version')->first();
    }
}
