<?php declare(strict_types=1);

namespace App\Models\TimeTrackingContext;

use App\Models\CoreContext\User;
use App\Models\CRMContext\Task;
use App\Traits\Uuids;
use App\ValueObject\CheckStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Check extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;
    use Uuids;

    protected $appends = [
        'start', 'end', 'title', 'resourceId', 'classNames',
    ];

    protected $fillable = [
        'company_id',
        'user_id',
        'status',
        'approved_by',
        'check_type_id',
        'summary',
        'task_id',
        'project_id',
        'client_id',
        'date_started',
        'date_ended',
    ];

    public function close(string $dateEnded, bool $close = true): void
    {
        $this->update([
            'date_ended' => $dateEnded,
            'status' => $close ? CheckStatus::closed() : CheckStatus::pendingApproval(),
        ]);
        $this->updateTimestamps();
    }

    public function getTitleAttribute(): string
    {
        return $this->tasks ? $this->tasks->name : 'ok';
    }

    public function getStartAttribute(): ?string
    {
        return $this->date_started;
    }

    public function getEndAttribute(): ?string
    {
        return $this->date_ended ?? null;
    }

    public function getResourceIdAttribute(): ?string
    {
        return $this->user_id;
    }

    public function getClassNamesAttribute(): array
    {
        return $this->date_ended ? [] : ['progress-bar', 'progress-bar-striped', 'active'];
    }

    public function tasks(): BelongsTo
    {
        return $this->belongsTo(Tasks::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
