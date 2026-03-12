<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("
            INSERT INTO academic_records (user_id, subject_id, approved_at)
            SELECT 1, id , NOW()
            FROM subjects 
            WHERE (semester <= 4)
                OR (semester BETWEEN 5 AND 8 AND (specialization_id IS NULL OR specialization_id = 1))
                OR (code = 'AUS-904');
        ");
    }
}
