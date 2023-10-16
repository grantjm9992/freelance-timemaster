<?php declare(strict_types=1);

namespace App\Models\CoreContext;

use App\Models\TimeTrackingContext\Clients;
use App\Traits\Uuids;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

class Company extends Model
{
    use CrudTrait;
    use HasFactory;
    use Uuids;

    protected $fillable = [
        'name',
        'admin_user_id',
        'address',
        'city',
        'country',
        'postcode',
        'number_of_employees',
        'sector_id',
        'configuration',
    ];

    protected $casts = [
        'configuration' => 'array',
    ];

    public function clients(): HasMany
    {
        return $this->hasMany(Clients::class);
    }


    public function employees(): HasMany
    {
        return $this->hasMany(User::class);
    }


    public function subscription(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
