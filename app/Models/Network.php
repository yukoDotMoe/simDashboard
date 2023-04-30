<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    protected $table = 'networks';
    use HasFactory;

    protected $fillable = [
        'uniqueId',
        'networkName',
        'status',
        'metadata',
    ];
}
