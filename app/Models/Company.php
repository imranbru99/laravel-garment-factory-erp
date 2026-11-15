<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class Company extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function factories(): HasMany { return $this->hasMany(Factory::class); }
    public function buyers(): HasMany { return $this->hasMany(Buyer::class); }
    public function suppliers(): HasMany { return $this->hasMany(Supplier::class); }
    public function certifications(): HasMany { return $this->hasMany(Certification::class); }
    public function styles(): HasMany { return $this->hasMany(Style::class); }
}
