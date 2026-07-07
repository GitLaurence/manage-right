<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftTemplate extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = ['business_id', 'name', 'start_time', 'end_time', 'break_minutes', 'color'];

    const COLORS = [
        'blue'   => ['bg' => 'bg-blue-100 dark:bg-blue-900',   'text' => 'text-blue-800 dark:text-blue-200',   'border' => 'border-blue-300 dark:border-blue-700'],
        'green'  => ['bg' => 'bg-green-100 dark:bg-green-900',  'text' => 'text-green-800 dark:text-green-200',  'border' => 'border-green-300 dark:border-green-700'],
        'amber'  => ['bg' => 'bg-amber-100 dark:bg-amber-900',  'text' => 'text-amber-800 dark:text-amber-200',  'border' => 'border-amber-300 dark:border-amber-700'],
        'purple' => ['bg' => 'bg-purple-100 dark:bg-purple-900','text' => 'text-purple-800 dark:text-purple-200','border' => 'border-purple-300 dark:border-purple-700'],
        'rose'   => ['bg' => 'bg-rose-100 dark:bg-rose-900',    'text' => 'text-rose-800 dark:text-rose-200',    'border' => 'border-rose-300 dark:border-rose-700'],
        'zinc'   => ['bg' => 'bg-zinc-100 dark:bg-zinc-700',    'text' => 'text-zinc-700 dark:text-zinc-200',    'border' => 'border-zinc-300 dark:border-zinc-500'],
    ];

    public function colorClasses(): array
    {
        return self::COLORS[$this->color] ?? self::COLORS['blue'];
    }

    public function formattedHours(): string
    {
        return date('g:i A', strtotime($this->start_time)).' – '.date('g:i A', strtotime($this->end_time));
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function scheduleEntries(): HasMany
    {
        return $this->hasMany(ScheduleEntry::class);
    }
}
