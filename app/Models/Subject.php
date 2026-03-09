<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Subject extends Model
{
    protected $fillable = [
        "code",
        "name",
        "uc",
        "semester",
        "prelacion_text",
        "specialization_id",
        "type"
    ];

    //Relationships

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    //Query Functions

    public static function getManyByCodesCollection(?Collection $codes = null) : Collection
    {
        if(!$codes){
            return static::make();
        }
        return static::whereIn('code',$codes)->get();
    }
}
