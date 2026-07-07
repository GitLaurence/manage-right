<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = ['business_id', 'branch_id', 'week_start', 'published_at'];

    protected $casts = [
        'week_start' => 'date',
        'published_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ScheduleEntry::class);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }
}
