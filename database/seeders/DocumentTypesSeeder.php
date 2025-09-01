<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypesSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    $items = [
      [
        'name' => 'INE',
      ],
      [
        'name' => 'COMPROBANTE DE DOMICILIO',
      ],
      [
        'name' => 'CERTIFICADO MEDICO GENERAL CON GRUPO SANGUINEO',
      ],
    ];

    DocumentType::insert($items);
  }
}
