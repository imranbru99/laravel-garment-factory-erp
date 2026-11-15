<?php

namespace App\Models\Concerns;

trait LimitsBuyerPortal
{
    public static function bootLimitsBuyerPortal(): void
    {
        static::addGlobalScope('buyer-portal', function ($query) {
            $user = auth()->user();

            if (! $user || $user->role !== 'buyer' || ! $user->buyer_id) {
                return;
            }

            $model = $query->getModel();
            $via = method_exists($model, 'buyerPortalRelation') ? $model->buyerPortalRelation() : null;

            if ($via) {
                $query->whereHas($via, fn ($related) => $related->where('buyer_id', $user->buyer_id));

                return;
            }

            $query->where($model->getTable().'.buyer_id', $user->buyer_id);
        });
    }
}
