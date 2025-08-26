<?php

namespace Database\Seeders;

use App\Models\Modality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModalitiesSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    $items = [
      [
        'id' => 1,
        'name' => 'ESCOLARIZADA',
      ],
      [
        'id' => 2,
        'name' => 'NO ESCOLARIZADA',
      ],
      [
        'id' => 3,
        'name' => 'TÉCNICO SUPERIOR UNIVERSITARIO',
      ],
      [
        'id' => 4,
        'name' => 'MIXTA',
      ],
      [
        'id' => 5,
        'name' => 'VIRTUAL',
      ],
    ];

    Modality::insert($items);
  }
}
