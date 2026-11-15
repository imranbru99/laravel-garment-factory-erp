<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class Sample extends Model
{
    use Auditable, LimitsBuyerPortal;

    protected $guarded = ['id'];

    public function buyerPortalRelation(): string
    {
        return 'style';
    }

    protected function casts(): array
    {
        return ['requested_on' => 'date', 'due_on' => 'date'];
    }

    public function style(): BelongsTo { return $this->belongsTo(Style::class); }
    public function buyerOrder(): BelongsTo { return $this->belongsTo(BuyerOrder::class); }
    public function comments(): HasMany { return $this->hasMany(SampleComment::class); }
}
