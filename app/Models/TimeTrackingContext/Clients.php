<?php declare(strict_types=1);

namespace App\Models\TimeTrackingContext;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clients extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'name',
        'description',
        'company_id',
        'active',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Projects::class, 'client_id');
    }
}
