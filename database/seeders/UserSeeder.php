<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder {
  public function run() {
    $now = Carbon::now()->format('Y-m-d H:i:s');

    $items = [
      [
        'id' => 1,
        'created_at' => $now,
        'updated_at' => $now,
        'created_by_id' => null,
        'updated_by_id' => null,
        'email_verified_at' => $now,
        'name' => 'ADMIN',
        'surname_p' => 'SISTEMA',
        'email' => 'admin@svr.com',
        'curp' => 'aaaaaaaaaaaaaaaaaa',
        'password' => bcrypt('Svr_1029*'),
        'role_id' => 1,
        'marital_status_id' => 1,
      ],
    ];

    User::insert($items);
  }
}
