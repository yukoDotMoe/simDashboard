<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';
    use HasFactory;

    protected $fillable = [
        'uniqueId',
        'serviceName',
        'used',
        'status',
        'metadata',
        'price',
        'used',
        'limit',
        'cooldown',
        'structure',
        'valid',
    ];
}
