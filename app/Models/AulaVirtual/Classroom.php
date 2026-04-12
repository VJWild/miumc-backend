<?php

namespace App\Models\AulaVirtual;

use App\Models\AulaVirtual\Assignment;
use App\Models\AulaVirtual\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Classroom extends Model
{
    protected $table = "av_classrooms";

    protected $fillable = [
        "title",
        "description",
        "cover_path",
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
        );
    }

    public function professor() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignments() : HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function posts() : HasMany
    {
        return $this->hasMany(Post::class);
    }

    //Utilities

    public static function generateUniqueCode()
    {
        do{
            $code = Str::upper(Str::random(6));
        } while (static::where('access_code',$code)->exists());

        return $code;
    }

    //Scopes

    public function scopeWithAll($query)
    {
        return $query->with(['members','professor','assignments','posts']);
    }
}
