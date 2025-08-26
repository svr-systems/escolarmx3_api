<?php

namespace Database\Seeders;

use App\Models\CourseType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseTypesSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    $items = [
      [
        'id' => 1,
        'name' => 'OBLIGATORIA',
        'code' => '263'
      ],
      [
        'id' => 2,
        'name' => 'OPTATIVA',
        'code' => '264'
      ],
      [
        'id' => 3,
        'name' => '*ADICIONAL',
        'code' => '265'
      ],
      [
        'id' => 4,
        'name' => '**COMPLEMENTARIA',
        'code' => '266'
      ],
    ];

    CourseType::insert($items);
  }
}
