<?php

namespace Database\Seeders;

use App\Models\Accreditation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccreditationsSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    $items = [
      [
        'id' => 1,
        'name' => 'RVOE FEDERAL',
      ],
      [
        'id' => 2,
        'name' => 'RVOE ESTATAL',
      ],
      [
        'id' => 3,
        'name' => 'TÉCNICO SUPERIOR UNIVERSITARIO',
      ],
      [
        'id' => 4,
        'name' => 'AUTORIZACIÓN FEDERAL',
      ],
      [
        'id' => 5,
        'name' => 'AUTORIZACIÓN ESTATAL',
      ],
      [
        'id' => 6,
        'name' => 'ACTA DE SESIÓN',
      ],
      [
        'id' => 7,
        'name' => 'ACUERDO DE INCORPORACIÓN',
      ],
      [
        'id' => 8,
        'name' => 'ACUERDO SECRETARIAL SEP',
      ],
      [
        'id' => 9,
        'name' => 'DECRETO DE CREACIÓN',
      ],
      [
        'id' => 10,
        'name' => 'OTRO',
      ],
    ];

    Accreditation::insert($items);
  }
}
