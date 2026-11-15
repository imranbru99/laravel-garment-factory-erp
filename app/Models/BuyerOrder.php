<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class BuyerOrder extends Model
{
    use Auditable, BelongsToFactory, LimitsBuyerPortal;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['ex_factory_date' => 'date', 'fob_price' => 'decimal:2'];
    }

    public function buyer(): BelongsTo { return $this->belongsTo(Buyer::class); }
    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
    public function matrixLines(): HasMany { return $this->hasMany(OrderMatrixLine::class); }
    public function milestones(): HasMany { return $this->hasMany(TnaMilestone::class); }
    public function costingSheets(): HasMany { return $this->hasMany(CostingSheet::class); }
    public function bundles(): HasMany { return $this->hasMany(Bundle::class); }
    public function purchaseOrders(): HasMany { return $this->hasMany(PurchaseOrder::class); }
    public function inspections(): HasMany { return $this->hasMany(Inspection::class); }
    public function shipments(): HasMany { return $this->hasMany(Shipment::class); }

    public function refreshQuantity(): void
    {
        $this->order_qty = (int) $this->matrixLines()->sum('quantity');
        $this->save();
    }
}
