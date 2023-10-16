<?php

namespace App\Models\CoreContext;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'status',
        'payment_method',
        'payment_response',
        'date',
        'subscription_id',
        'amount',
    ];
}
