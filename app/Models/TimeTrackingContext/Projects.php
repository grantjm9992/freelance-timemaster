<?php declare(strict_types=1);

namespace App\Models\TimeTrackingContext;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Projects extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'name',
        'company_id',
        'client_id',
        'active',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Tasks::class, 'project_id');
    }
}
