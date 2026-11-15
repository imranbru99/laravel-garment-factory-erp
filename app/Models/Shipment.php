<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class Shipment extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['etd' => 'date', 'eta' => 'date'];
    }

    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function cartons(): HasMany { return $this->hasMany(Carton::class); }
}
