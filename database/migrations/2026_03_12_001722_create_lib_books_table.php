<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lib_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained();
            $table->tinyText("title");
            $table->text("description");
            $table->tinyText("cover_path");
            $table->string("file_path")->unique();
            $table->enum("status",["active","pending","prohibited"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lib_books');
    }
};
