<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

#[Slug(from: 'name', to: 'slug')]
class Style extends Model
{
    use Auditable, LimitsBuyerPortal, \ImranDev\UniversalSlug\Traits\HasUniversalSlug;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['smv' => 'decimal:2', 'ai_listing' => 'array'];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function buyer(): BelongsTo { return $this->belongsTo(Buyer::class); }
    public function techPacks(): HasMany { return $this->hasMany(TechPack::class); }
    public function samples(): HasMany { return $this->hasMany(Sample::class); }
    public function operations(): HasMany { return $this->hasMany(Operation::class); }
    public function washRecipes(): HasMany { return $this->hasMany(WashRecipe::class); }
    public function orders(): HasMany { return $this->hasMany(BuyerOrder::class); }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
