<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class Machine extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['last_service_on' => 'date', 'next_service_on' => 'date'];
    }

    public function sewingLine(): BelongsTo { return $this->belongsTo(SewingLine::class); }
    public function breakdowns(): HasMany { return $this->hasMany(Breakdown::class); }
}
