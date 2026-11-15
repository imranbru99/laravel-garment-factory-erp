<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class CostingSheet extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'smv' => 'decimal:2',
            'cm_cost' => 'decimal:4',
            'fabric_cost' => 'decimal:4',
            'trim_cost' => 'decimal:4',
            'wash_cost' => 'decimal:4',
            'overhead' => 'decimal:4',
            'fob' => 'decimal:4',
            'margin_percent' => 'decimal:2',
        ];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
}
