<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Activity extends SpatieActivity
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
}
