<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Balance extends Model
{
    protected $table = 'balanceslog';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uniqueId',
        'accountId',
        'oldBalance',
        'newBalance',
        'totalChange',
        'status',
        'reason',
        'metadata',
    ];
}
