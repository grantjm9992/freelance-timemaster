<?php declare(strict_types=1);

namespace App\Models\CoreContext;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'name',
        'company_id',
    ];
}
