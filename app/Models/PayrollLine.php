<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class PayrollLine extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'present_days' => 'decimal:1',
            'overtime_hours' => 'decimal:1',
            'piece_amount' => 'decimal:2',
            'attendance_amount' => 'decimal:2',
            'gross' => 'decimal:2',
        ];
    }

    public function payrollPeriod(): BelongsTo { return $this->belongsTo(PayrollPeriod::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}
