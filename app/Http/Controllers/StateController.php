<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use Throwable;

class StateController extends Controller
{
  public function index(Request $req) {
    try {
      return $this->apiRsp(
        200,
        'Registros retornados correctamente',
        ['items' => State::getItems($req)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }
}
