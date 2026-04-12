<?php

namespace App\Models\Gestor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialization extends Model
{
    protected $fillable = [
        "career_id",
        "name"
    ];

    //Relationships

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
