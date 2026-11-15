<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class Employee extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'is_operator' => 'boolean',
            'is_active' => 'boolean',
            'piece_rate' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function sewingLine(): BelongsTo { return $this->belongsTo(SewingLine::class); }
    public function shift(): BelongsTo { return $this->belongsTo(Shift::class); }
    public function leaveRequests(): HasMany { return $this->hasMany(LeaveRequest::class); }
    public function attendance(): HasMany { return $this->hasMany(WorkerAttendance::class); }
    public function payrollLines(): HasMany { return $this->hasMany(PayrollLine::class); }
}
