<?php

namespace App\Models\AulaVirtual;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassroomUser extends Model
{
    protected $table = 'av_classroom_user';

    protected $fillable = [
        'classroom_id',
        'user_id',
    ];

    //Relationships

    public function classroom() : BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
