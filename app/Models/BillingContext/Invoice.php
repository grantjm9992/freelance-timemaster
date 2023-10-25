<?php

namespace App\Models\BillingContext;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Invoice extends Model
{
    use Uuids;
    use HasFactory;

    protected $connection = 'mongodb';
    protected $fillable = [
        'id',
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
