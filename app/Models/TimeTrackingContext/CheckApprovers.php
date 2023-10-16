<?php

namespace App\Models\TimeTrackingContext;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckApprovers extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'company_id',
        'user_id',
    ];
}
