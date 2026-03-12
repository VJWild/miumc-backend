<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $careers = [
            ['code' => 'INGINF', 'name' => 'Ingeniería Informática'],
            ['code' => 'INGM',   'name' => 'Ingeniería Marítima'],
            ['code' => 'INGAMB', 'name' => 'Ingeniería Ambiental'],
            ['code' => 'ADM',    'name' => 'Administración'],
            ['code' => 'TUR',    'name' => 'Turismo'],
            ['code' => 'TSUACU', 'name' => 'TSU Transporte Acuático'],
        ];

        $careers = array_map(function ($item) use ($now) {
            return array_merge($item, ['created_at' => $now]);
        }, $careers);

        DB::table('careers')->insert($careers);
    }
}
