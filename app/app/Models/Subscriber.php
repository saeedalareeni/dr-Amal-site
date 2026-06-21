<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscriber extends Model
{
    public const STATUSES = ['pending', 'verified', 'unsubscribed'];

    protected $guarded = [];

    protected $hidden = ['verification_token'];

    protected function casts(): array
    {
        return [
            'verification_expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    public function downloads(): HasMany { return $this->hasMany(Download::class); }
}
