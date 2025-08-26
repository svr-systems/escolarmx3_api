<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder {
  public function run() {
    $items = [
      [
        'id' => 1,
        'name' => 'SUPER-ADMIN',
      ],
      [
        'id' => 2,
        'name' => 'INSTITUCION',
      ],
      [
        'id' => 3,
        'name' => 'USUARIO',
      ],
      [
        'id' => 4,
        'name' => 'DOCENTE',
      ],
      [
        'id' => 5,
        'name' => 'ALUMNO',
      ],
    ];

    Role::insert($items);
  }
}
