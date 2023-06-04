<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sims extends Model
{
    protected $table = 'sims';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uniqueId',
        'phone',
        'networkId',
        'countryCode',
        'status',
        'success',
        'failed',
        'metadata',
        'userid',
        'locked_services',
        'working_services',
    ];
}
