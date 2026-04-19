<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_number', 'name'); // or however the relationship maps
    }

    public function activeOrder()
    {
        return $this->hasOne(Order::class, 'table_number', 'name')->where('status', 'pending')->latest();
    }
}
