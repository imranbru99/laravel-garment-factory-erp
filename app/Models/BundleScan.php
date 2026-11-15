<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class BundleScan extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['scanned_at' => 'datetime'];
    }

    public function bundle(): BelongsTo { return $this->belongsTo(Bundle::class); }
    public function operation(): BelongsTo { return $this->belongsTo(Operation::class); }
    public function sewingLine(): BelongsTo { return $this->belongsTo(SewingLine::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}
