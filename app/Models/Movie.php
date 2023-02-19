<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'rating', 'image'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
