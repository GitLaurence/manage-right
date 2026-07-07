<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::check()) {
            return;
        }

        $tenantId = Auth::user()->current_business_id;

        if (! $tenantId) {
            return;
        }

        $builder->where($model->getTable().'.business_id', $tenantId);
    }
}
