<?php

namespace App\Models\CoreContext;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'role_id',
        'permission_id',
    ];
}
