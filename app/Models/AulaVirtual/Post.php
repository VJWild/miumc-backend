<?php

namespace App\Models\AulaVirtual;

use App\Models\AulaVirtual\Classroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Post extends Model
{
    protected $table = "av_posts";

    protected $fillable = [
        'classroom_id',
        'user_id',
        'type',
        'title',
        'content',
        'file_path'
    ];

    //Relationships

    public function classroom() : BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function owner() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
