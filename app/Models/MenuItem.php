<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Database\Factories\MenuItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'business_id',
    'category',
    'name',
    'description',
    'price',
    'cost',
    'is_available',
    'sort_order',
])]
class MenuItem extends Model
{
    /** @use HasFactory<MenuItemFactory> */
    use BelongsToBusiness, HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
            'is_available' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Only include items that are currently offered.
     */
    #[Scope]
    protected function available(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    /**
     * Get the estimated profit per unit.
     */
    public function margin(): ?float
    {
        return $this->cost === null ? null : (float) $this->price - (float) $this->cost;
    }
}
