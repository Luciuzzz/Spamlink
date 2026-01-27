<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeLog extends Model
{
    protected $fillable = [
        'user_id', 'model_type', 'model_id', 'action', 'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function getGroupedChanges(): array
    {
        $grouped = [];

        if (!is_array($this->changes)) {
            return $grouped;
        }

        foreach ($this->changes as $field => $change) {
            $grouped[$this->action][] = [
                'field' => $field,
                'from' => $change['old'] ?? '—',
                'to' => $change['new'] ?? '—',
            ];
        }

        return $grouped;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
