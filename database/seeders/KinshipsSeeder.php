<?php

namespace Database\Seeders;

use App\Models\Kinship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KinshipsSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    $items = [
      [
        'name' => 'PADRE',
      ],
      [
        'name' => 'MADRE',
      ],
      [
        'name' => 'PADRASTRO',
      ],
      [
        'name' => 'MADRASTRA',
      ],
      [
        'name' => 'TUTOR LEGAL',
      ],
      [
        'name' => 'ABUELO',
      ],
      [
        'name' => 'ABUELA',
      ],
      [
        'name' => 'TÍO',
      ],
      [
        'name' => 'TÍA',
      ],
      [
        'name' => 'HERMANO',
      ],
      [
        'name' => 'HERMANA',
      ],
      [
        'name' => 'PRIMO / PRIMA',
      ],
      [
        'name' => 'PADRE DE ACOGIDA',
      ],
      [
        'name' => 'MADRE DE ACOGIDA',
      ],
      [
        'name' => 'CUIDADOR',
      ],
      [
        'name' => 'OTRO',
      ],
    ];

    Kinship::insert($items);
  }
}
