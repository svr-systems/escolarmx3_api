<?php

namespace Database\Seeders;

use App\Models\MaritalStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaritalStatusesSeeder extends Seeder {
  public function run(): void {
    $items = [
      [
        'id' => 1,
        'name' => 'SOLTERO',
      ],
      [
        'id' => 2,
        'name' => 'CASADO',
      ],
      [
        'id' => 3,
        'name' => 'UNIÓN LIBRE',
      ],
      [
        'id' => 4,
        'name' => 'DIVORCIADO',
      ],
      [
        'id' => 5,
        'name' => 'VIUDO',
      ],
      [
        'id' => 6,
        'name' => 'SEPARADO',
      ],
    ];

    MaritalStatus::insert($items);
  }
}
