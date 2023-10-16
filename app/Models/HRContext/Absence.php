<?php

namespace App\Models\HRContext;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'company_id',
        'user_id',
        'start_date',
        'end_date',
        'comments',
        'status',
        'absence_type_id',
    ];
}
