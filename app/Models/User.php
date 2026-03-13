<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_code',
        'full_name',
        'password_hash',
        'email',
        'phone',
        'age',
        'birth_date',
        'career_id',
        'specialization_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //Relationships

    public function enrollments() : HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function career() : BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function specialization() : BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }
    public function academic_records() : HasMany
    {
        return $this->hasMany(AcademicRecord::class);
    }

    public function classrooms() : BelongsToMany
    {
        return $this->belongsToMany(
            Classroom::class,
            'av_classroom_user',
            'user_id',
            'classroom_id'
        );
    }

    //Query Functions

    public static function getByCode($code = null) : ?static
        {
        if(!$code){
            return null;
        }

        return static::where('student_code',$code)->first();
    }

    public static function findByCodeWithRelationships($code = null){
        if(!$code){
            return null;
        }

        return static::query()
                    ->select('*')
                    ->where('student_code',$code)
                    ->with(['career','specialization'])
                    ->first();
    }

    public static function listAllWithRelationships() : Collection
    {
        return static::with(['career','specialization'])
                    ->orderBy("role","asc")
                    ->orderBy("full_name","asc")
                    ->get();
    }


}
