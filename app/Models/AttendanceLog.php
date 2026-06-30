<?php

namespace App\Models;

use App\Services\SupabaseStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    protected $fillable = [
        'user_id', 'business_id', 'branch_id', 'schedule_entry_id',
        'type', 'selfie_path', 'latitude', 'longitude', 'logged_at',
        'status', 'manager_note', 'reviewed_by', 'reviewed_at',
        'late_minutes', 'undertime_minutes', 'overtime_minutes',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function scheduleEntry(): BelongsTo
    {
        return $this->belongsTo(ScheduleEntry::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function selfieUrl(int $expiresIn = 3600): ?string
    {
        if (! $this->selfie_path) {
            return null;
        }

        if (config('services.supabase.service_role_key')) {
            return app(SupabaseStorage::class)->signedUrl($this->selfie_path, $expiresIn);
        }

        return null;
    }

    public function isLate(): bool
    {
        return ($this->late_minutes ?? 0) > 0;
    }

    public function hasUndertime(): bool
    {
        return ($this->undertime_minutes ?? 0) > 0;
    }

    public function hasOvertime(): bool
    {
        return ($this->overtime_minutes ?? 0) > 0;
    }
}
