<?php

namespace App\Models\Concerns;

use App\Models\Factory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToFactory
{
    public static function bootBelongsToFactory(): void
    {
        static::addGlobalScope('current-factory', function ($query) {
            if (app()->runningInConsole() && ! app()->runningUnitTests()) {
                return;
            }

            $factoryId = session('factory_id');

            if ($factoryId) {
                $query->where($query->getModel()->getTable().'.factory_id', $factoryId);
            }
        });

        static::creating(function ($model) {
            if (empty($model->factory_id) && session('factory_id')) {
                $model->factory_id = session('factory_id');
            }
        });
    }

    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }
}
