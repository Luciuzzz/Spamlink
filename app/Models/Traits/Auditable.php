<?php

namespace App\Models\Traits;

use App\Models\ChangeLog;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::updating(function ($model) {
            $changes = [];

            foreach ($model->getDirty() as $key => $newValue) {
                $oldValue = $model->getOriginal($key);

                // Solo registrar si hay un cambio real
                if ($oldValue != $newValue) {
                    $changes[$key] = [
                        'from' => $oldValue,
                        'to'   => $newValue,
                    ];
                }
            }

            if (!empty($changes)) {
                $user ??= Auth::user();
                ChangeLog::create([
                    'user_id'    => $user->id,
                    'model_type' => get_class($model),
                    'model_id'   => $model->id,
                    'action'     => 'update',
                    'changes'    => $changes,
                ]);
            }
        });

        static::created(function ($model) {
            ChangeLog::create([
                'user_id'    => Auth::id(),
                'model_type' => get_class($model),
                'model_id'   => $model->id,
                'action'     => 'create',
                'changes'    => $model->toArray(),
            ]);
        });

        static::deleted(function ($model) {
            ChangeLog::create([
                'user_id'    => Auth::id(),
                'model_type' => get_class($model),
                'model_id'   => $model->id,
                'action'     => 'delete',
                'changes'    => $model->toArray(),
            ]);
        });
    }
}
