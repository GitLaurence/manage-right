<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeRequest extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'user_id', 'business_id', 'branch_id', 'type', 'leave_type',
        'from_date', 'to_date', 'hours', 'reason', 'status',
        'manager_remarks', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'from_date'   => 'date',
        'to_date'     => 'date',
        'reviewed_at' => 'datetime',
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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'leave'     => 'Leave',
            'overtime'  => 'Overtime',
            'undertime' => 'Undertime',
            default     => ucfirst($this->type),
        };
    }

    public function leaveTypeLabel(): string
    {
        return match ($this->leave_type) {
            'sick'      => 'Sick Leave',
            'vacation'  => 'Vacation Leave',
            'emergency' => 'Emergency Leave',
            'unpaid'    => 'Unpaid Leave',
            default     => '',
        };
    }
}
