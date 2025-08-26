<?php

namespace App\Http\Controllers;

use App\Models\Modality;
use Illuminate\Http\Request;
use Throwable;

class ModalityController extends Controller
{
  public function index(Request $req) {
    try {
      return $this->apiRsp(
        200,
        'Registros retornados correctamente',
        ['items' => Modality::getItems($req)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }
}
