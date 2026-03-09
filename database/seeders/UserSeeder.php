<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sendo_pargula = [
            "id"=>1,
            "student_code"=>"INGINF-26327337",
            "full_name"=>"Victor Gonzalez",
            "email"=>"vjgg101@gmail.com",
            "phone"=>"0412-8226885",
            "password_hash"=>Hash::make("Vjgg+8544+"),
            "career_id"=>1,
            "specialization_id"=>1,
            "role"=>"admin",
            "age"=>28,
            "birth_date"=>"2000-01-01",
            "created_at" => now()
        ];

        DB::table("users")->insert($sendo_pargula);
    }
}
