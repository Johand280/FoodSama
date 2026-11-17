<?php

namespace App\Models\Concerns;

use App\Models\Business;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBusiness
{
    /**
     * Boot the tenant scoping behavior for the trait.
     */
    protected static function bootBelongsToBusiness(): void
    {
        static::addGlobalScope('business', function (Builder $builder): void {
            if (! auth()->check()) {
                return;
            }

            $businessId = auth()->user()?->business_id;

            if ($businessId === null) {
                return;
            }

            $builder->where($builder->getModel()->qualifyColumn('business_id'), $businessId);
        });

        static::creating(function (Model $model): void {
            if ($model->business_id !== null || ! auth()->check()) {
                return;
            }

            $model->business_id = auth()->user()?->business_id;
        });
    }

    /**
     * Get the business that owns the model.
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
