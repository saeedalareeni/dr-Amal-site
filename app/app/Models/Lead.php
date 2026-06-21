<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    public const STATUSES = ['new', 'contacted', 'qualified', 'won', 'lost', 'spam'];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['consultation_date' => 'date', 'follow_up_at' => 'datetime'];
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class)->latest();
    }
}
