<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class SewingLine extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'target_efficiency' => 'decimal:2'];
    }

    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
    public function machines(): HasMany { return $this->hasMany(Machine::class); }
    public function productionLogs(): HasMany { return $this->hasMany(ProductionLog::class); }
    public function linePlans(): HasMany { return $this->hasMany(LinePlan::class); }
}
