<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class Department extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    public function sewingLines(): HasMany { return $this->hasMany(SewingLine::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
}
