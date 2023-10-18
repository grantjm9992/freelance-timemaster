<?php

namespace App\Models\BillingContext;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'company_id',
        'type',
        'resource_id',
        'address',
        'city',
        'county',
        'country',
        'postcode',
    ];
}
