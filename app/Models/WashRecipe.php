<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class WashRecipe extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['chemicals' => 'array', 'temperature_c' => 'decimal:1'];
    }

    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
    public function lots(): HasMany { return $this->hasMany(WashLot::class); }
}
