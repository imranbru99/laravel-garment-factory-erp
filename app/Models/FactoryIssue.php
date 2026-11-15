<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToFactory;
use App\Models\Concerns\LimitsBuyerPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ImranDev\UniversalSlug\Attributes\Slug;

class FactoryIssue extends Model
{
    use Auditable, BelongsToFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'suggested_actions' => 'array',
            'detected_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function actions(): HasMany { return $this->hasMany(IssueAction::class); }

    public function openActions(): void
    {
        foreach ($this->suggested_actions ?? [] as $action) {
            $this->actions()->create([
                'title' => $action['title'] ?? 'Corrective action',
                'owner_role' => $action['owner_role'] ?? null,
                'due_on' => $action['due_on'] ?? now()->addDay()->toDateString(),
                'notes' => $action['detail'] ?? null,
                'status' => 'open',
            ]);
        }

        $this->update(['status' => 'in_progress']);
    }
}
