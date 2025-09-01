<?php

namespace App\Http\Controllers;

use App\Models\Kinship;
use Illuminate\Http\Request;
use Throwable;

class KinshipController extends Controller {
  public function index(Request $req) {
    try {
      return $this->apiRsp(
        200,
        'Registros retornados correctamente',
        ['items' => Kinship::getItems($req)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }
}
