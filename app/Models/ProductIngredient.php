<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductIngredient extends Pivot
{
    use HasFactory, HasUuids;

    protected $table = 'ingredient_product';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];
}
