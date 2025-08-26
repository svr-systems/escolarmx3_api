<?php

namespace App\Http\Controllers;

use App\Models\CourseType;
use Illuminate\Http\Request;
use Throwable;

class CourseTypeCotroller extends Controller
{
  public function index(Request $req) {
    try {
      return $this->apiRsp(
        200,
        'Registros retornados correctamente',
        ['items' => CourseType::getItems($req)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }
}
