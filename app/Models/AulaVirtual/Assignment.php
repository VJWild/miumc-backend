<?php

namespace App\Models\AulaVirtual;

use App\Models\Classroom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $table = "av_assignments";

    protected $fillable = [
        'classroom_id',
        'title',
        'description',
        'file_path',
        'due_time',
        'points'
    ];

    protected function casts()
    {
        return [
            'due_time' => 'datetime',
            'created_at'=> 'datetime',
            'updated_at' => 'datetime'
        ];
    }

    //Relationships

    public function classroom() : BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function submissions() : HasMany
    {
        return $this->hasMany(Submission::class);
    }
    
}
