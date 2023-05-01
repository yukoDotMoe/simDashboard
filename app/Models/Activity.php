<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    protected $table = 'activitieslog';
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uniqueId',
        'userid',
        'phone',
        'networkId',
        'countryCode',
        'serviceId',
        'serviceName',
        'smsContent',
        'code',
        'status',
        'reason',
        'metadata',
    ];
}
