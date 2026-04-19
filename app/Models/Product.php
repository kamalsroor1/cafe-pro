<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(ProductAddon::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)
            ->using(ProductIngredient::class)
            ->withPivot('amount_needed')
            ->withTimestamps();
    }
}
