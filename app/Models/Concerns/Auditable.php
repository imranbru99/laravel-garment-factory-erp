<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable(): void
    {
        foreach (['created' => 'created', 'updated' => 'updated', 'deleted' => 'deleted'] as $event => $name) {
            static::$event(function (Model $model) use ($name) {
                if ($model instanceof AuditLog || ! config('factory.audit', true)) {
                    return;
                }

                AuditLog::query()->create([
                    'user_id' => auth()->id(),
                    'auditable_type' => $model->getMorphClass(),
                    'auditable_id' => $model->getKey() ?? 0,
                    'event' => $name,
                    'old_values' => $name === 'created' ? null : $model->getOriginal(),
                    'new_values' => $name === 'deleted' ? null : $model->getAttributes(),
                    'ip' => request()?->ip(),
                ]);
            });
        }
    }
}
