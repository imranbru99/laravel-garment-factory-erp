<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class Factory extends Model
{
    use Auditable;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function departments(): HasMany { return $this->hasMany(Department::class); }
    public function sewingLines(): HasMany { return $this->hasMany(SewingLine::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
    public function orders(): HasMany { return $this->hasMany(BuyerOrder::class); }
    public function issues(): HasMany { return $this->hasMany(FactoryIssue::class); }
}
