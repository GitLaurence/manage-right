<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'user_id', 'business_id', 'branch_id',
        'action', 'subject_type', 'subject_id', 'context',
    ];

    protected $casts = [
        'context' => 'array',
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

    public static function record(string $action, array $context = [], ?int $branchId = null): void
    {
        $user = auth()->user();

        if (! $user || ! $user->current_business_id) {
            return;
        }

        static::create([
            'user_id'     => $user->id,
            'business_id' => $user->current_business_id,
            'branch_id'   => $branchId,
            'action'      => $action,
            'context'     => $context ?: null,
        ]);
    }
}
