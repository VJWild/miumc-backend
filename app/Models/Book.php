<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    protected $table = "lib_books";

    protected $fillable = [
        "title",
        "description",
        "cover_path",
        "file_path",
        "status",
        "user_id"
    ];

    protected $casts = [
        "created_at" => "datetime",
        "updated_at" => "datetime"
    ];

    //Relationships

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
