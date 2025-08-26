<?php

namespace Database\Seeders;
use App\Models\Term;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TermsSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    $items = [
      [
        'id' => 1,
        'name' => 'SEMESTRE',
        'code' => '91'
      ],
      [
        'id' => 2,
        'name' => 'BIMESTRE',
        'code' => '92'
      ],
      [
        'id' => 3,
        'name' => 'CUATRIMESTRE',
        'code' => '93'
      ],
      [
        'id' => 4,
        'name' => 'TETRAMESTRE',
        'code' => '94'
      ],
      [
        'id' => 5,
        'name' => 'TRIMESTRE',
        'code' => '260'
      ],
      [
        'id' => 6,
        'name' => 'MODULAR',
        'code' => '261'
      ],
      [
        'id' => 7,
        'name' => 'ANUAL',
        'code' => '262'
      ],
    ];

    Term::insert($items);
  }
}
