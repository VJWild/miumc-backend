<?php

namespace App\Models\Gestor;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $fillable = [
        "user_id",
        "subject_id",
        "period",
        "schedule_data",
    ];

    protected $casts = [
        "created_at" => "datetime"
    ];

    //Relationships

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject() : BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    //Query Functions

    public static function getEnrolledSubjectsByStudentCode($code = null,$period = "2026-I"){
        return static::query()
                    ->from("enrollments as e")
                    ->join("users as u","e.user_id","=","u.id")
                    ->join("subjects as s","e.subject_id")
                    ->where("u.student_code",$code)
                    ->where("e.period",$period)
                    ->get();
    }
}
