<?php

namespace App\Models;

use App\Models\Specialization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Career extends Model
{
    protected $fillable = [
        "name",
        "code"
    ];

    protected $casts = [
        "created_at" => "datetime"
    ];

    //Relationships

    public function specializations() : HasMany
    {
        return $this->hasMany(Specialization::class,'career_id','id');
    }

}
