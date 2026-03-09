<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $specializations = [
            [
                'id' => 1,
                'career_id' => 1,
                'name' => 'Redes y Telecomunicaciones',
            ],
            [
                'id' => 2,
                'career_id' => 1,
                'name' => 'Seguridad Informática',
            ],
            [
                'id' => 3,
                'career_id' => 1,
                'name' => 'Automatización de Procesos',
            ],
            [
                'id' => 4,
                'career_id' => 1,
                'name' => 'Gestión de Datos',
            ],
        ];

        DB::table('specializations')->insert($specializations);
    }
}
