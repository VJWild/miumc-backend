<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicRecord extends Model
{
    public $timestamps = false;

    protected $fillable = [
        "user_id",
        "subject_id",
        "grade",
        "status",
    ];

    protected $casts = [
        "approved_at" => "datetime"
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

    public static function getApprovedSubjectsByStudentCode($code){
        return static::from("academic_records as ar")
                    ->join("users as u","ar.user_id","=","u.id")
                    ->join("subjects as s","ar.subject_id","=","s.id")
                    ->select("s.code")
                    ->where("u.student_code",$code)
                    ->where("ar.status","aprobada")
                    ->get();
    }
}
