<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Classroom extends Model
{
    protected $table = "av_classrooms";

    protected $fillable = [
        "title",
        "description",
        "cover_path",
        "access_code"
    ];

    protected $hidden = [
        "access_code"
    ];

    //Relationships

    public function members() : BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'av_classroom_user',
            "classroom_id",
            "user_id"
        )->withPivot('role');
    }

    public function teacher() : BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'av_classroom_user',
            'classroom_id',
            'user_id'
        )->wherePivot('role','teacher')
         ->withPivot('role')
         ->one();
    }

    public function students() : BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'av_classroom_user',
            'classroom_id',
            'user_id'
        )->wherePivot('role','student')
         ->withPivot('role')
         ->withTimestamps();
    }
}
