<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'name', 'address', 'timezone'];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(BusinessUser::class);
    }
}
