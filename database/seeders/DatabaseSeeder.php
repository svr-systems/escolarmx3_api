<?php

namespace Database\Seeders;

use App\Models\Kinship;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(RoleSeeder::class);
        // $this->call(MaritalStatusesSeeder::class);
        // $this->call(UserSeeder::class);
        // $this->call(StateSeeder::class);
        // $this->call(MunicipalitiesSeeder::class);
        // $this->call(LevelsSeeder::class);
        // $this->call(AccreditationsSeeder::class);
        // $this->call(ModalitiesSeeder::class);
        // $this->call(ShiftsSeeder::class);
        // $this->call(TermsSeeder::class);
        // $this->call(CourseTypesSeeder::class);
        $this->call(KinshipsSeeder::class);
    }
}
