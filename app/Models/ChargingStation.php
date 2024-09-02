<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargingStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'connector_types',
        'business_status',
        'place_id',
        'address',
    ];

    protected $casts = [
        'name' => 'array',
        'location' => 'array',
        'connector_types' => 'array',
        'rating' => 'float'
    ];
}
