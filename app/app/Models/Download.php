<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    protected $guarded = [];
    protected function casts(): array { return ['downloaded_at' => 'datetime']; }
    public function subscriber(): BelongsTo { return $this->belongsTo(Subscriber::class); }
}
