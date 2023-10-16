<?php

namespace App\Models\CoreContext;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    use Uuids;

    protected $table = 'subscriptions_custom';

    protected $fillable = [
        'company_id',
        'types',
        'start_date',
        'next_renew_date',
        'end_date',
        'status',
        'number_of_users',
    ];

    protected $casts = [
        'types' => 'array',
    ];
}
