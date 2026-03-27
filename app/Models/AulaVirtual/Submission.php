<?php

namespace App\Models\AulaVirtual;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Submission extends Model
{
    protected $table = 'av_submissions';

    protected $fillable = [
        'assignment_id',
        'classroom_user_id',
        'content',
        'file_path',
        'is_graded',
        'grade',
        'teacher_feedback'
    ];

    //Relationships

    public function assignment() : BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student() : HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            ClassroomUser::class,
            'user_id',
            'classroom_user_id',
            'id',
            'id'
        );
    }
}
