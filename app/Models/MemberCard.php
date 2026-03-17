<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MemberCard extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (MemberCard $card) {
            if (empty($card->token)) {
                $card->token = (string) Str::uuid();
            }
            if (empty($card->issued_at)) {
                $card->issued_at = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
