<?php

namespace App\Models\BillingContext;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'company_id',
        'user_id',
        'client_id',
        'title',
        'pay_amount',
        'rate_of_pay',
        'period_of_pay',
        'currency',
        'start_date',
        'end_date',
        'status',
    ];
}