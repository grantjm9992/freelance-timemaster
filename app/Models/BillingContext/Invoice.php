<?php

namespace App\Models\BillingContext;

use App\Traits\Uuids;
use Jenssegers\Mongodb\Eloquent\Model;

class Invoice extends Model
{
    protected $connection = 'mongodb';
    protected $fillable = [
        'company_id',
        'user_id',
        'client_id',
        'project_id',
        'task_id',
        'status',
        'recipient',
        'payer',
        'items',
        'total',
        'currency',
        'tax_rate',
        'tax_applied',
        'total_including_tax',
        'create_date',
        'due_date',
        'paid_date',
        'amount_paid',
        'title',
        'description',
    ];
}
