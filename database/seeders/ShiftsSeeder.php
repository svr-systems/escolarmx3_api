<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $items = [
      [
        'id' => 1,
        'name' => 'MATUTINO',
      ],
      [
        'id' => 2,
        'name' => 'VESPERTINO',
      ],
      [
        'id' => 3,
        'name' => 'MIXTO',
      ],
    ];

    Shift::insert($items);
    }
}
