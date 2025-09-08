<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelsSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    $items = [
      [
        'id' => 1,
        'name' => 'DOCTORADO',
        'code' => '95'
      ],
      [
        'id' => 2,
        'name' => 'ESPECIALIDAD',
        'code' => '85'
      ],
      [
        'id' => 3,
        'name' => 'TÉCNICO SUPERIOR UNIVERSITARIO',
        'code' => '84'
      ],
      [
        'id' => 4,
        'name' => 'PROFESIONAL ASOCIADO',
        'code' => '83'
      ],
      [
        'id' => 5,
        'name' => 'MAESTRÍA',
        'code' => '82'
      ],
      [
        'id' => 6,
        'name' => 'LICENCIATURA',
        'code' => '81'
      ],
      [
        'id' => 7,
        'name' => 'BACHILLERATO',
        'code' => 'BC'
      ],
    ];

    Level::insert($items);
  }
}
