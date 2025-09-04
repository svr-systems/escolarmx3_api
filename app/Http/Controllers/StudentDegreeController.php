<?php

namespace App\Http\Controllers;

use App\Models\StudentDegree;
use DB;
use Illuminate\Http\Request;
use Throwable;

class StudentDegreeController extends Controller
{
  public function index(Request $req) {
    try {
      return $this->apiRsp(
        200,
        'Registros retornados correctamente',
        ['items' => StudentDegree::getItems($req)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }

  public function show(Request $req, $id) {
    try {
      return $this->apiRsp(
        200,
        'Registro retornado correctamente',
        ['item' => StudentDegree::getItem($req, $id)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }

  public function destroy(Request $req, $id) {
    DB::beginTransaction();
    try {
      $item = StudentDegree::find($id);

      if (!$item) {
        return $this->apiRsp(422, 'ID no existente');
      }

      $item->is_active = false;
      $item->updated_by_id = $req->user()->id;
      $item->save();

      DB::commit();
      return $this->apiRsp(
        200,
        'Registro inactivado correctamente'
      );
    } catch (Throwable $err) {
      DB::rollback();
      return $this->apiRsp(500, null, $err);
    }

  }

  public function restore(Request $req) {
    DB::beginTransaction();
    try {
      $item = StudentDegree::find($req->id);

      if (!$item) {
        return $this->apiRsp(422, 'ID no existente');
      }

      $item->is_active = true;
      $item->updated_by_id = $req->user()->id;
      $item->save();

      DB::commit();
      return $this->apiRsp(
        200,
        'Registro activado correctamente',
        ['item' => StudentDegree::getItem(null, $item->id)]
      );
    } catch (Throwable $err) {
      DB::rollback();
      return $this->apiRsp(500, null, $err);
    }
  }

  public function store(Request $req) {
    return $this->storeUpdate($req, null);
  }

  public function update(Request $req, $id) {
    return $this->storeUpdate($req, $id);
  }

  public function storeUpdate($req, $id) {
    DB::beginTransaction();
    try {

      $valid = StudentDegree::valid($req->all());

      if ($valid->fails()) {
        return $this->apiRsp(422, $valid->errors()->first());
      }

      $store_mode = is_null($id);

      if ($store_mode) {
        $item = new StudentDegree;
        $item->created_by_id = $req->user()->id;
        $item->updated_by_id = $req->user()->id;
      } else {
        $item = StudentDegree::find($id);
        $item->updated_by_id = $req->user()->id;
      }

      $item = $this->saveItem($item, $req);

      DB::commit();
      return $this->apiRsp(
        $store_mode ? 201 : 200,
        'Registro ' . ($store_mode ? 'agregado' : 'editado') . ' correctamente',
        $store_mode ? ['item' => ['id' => $item->id]] : null
      );
    } catch (Throwable $err) {
      DB::rollback();
      return $this->apiRsp(500, null, $err);
    }
  }

  public static function saveItem($item, $data, $is_req = true) {
    if (!$is_req) {
      $item->active = GenController::filter($data->active, 'b');
    }

    $item->student_id = GenController::filter($data->student_id, 'id');
    $item->level_id = GenController::filter($data->level_id, 'id');
    $item->institution_name = GenController::filter($data->institution_name, 'U');
    $item->name = GenController::filter($data->name, 'U');
    $item->municipality_id = GenController::filter($data->municipality_id, 'id');
    $item->start_at = GenController::filter($data->start_at, 'd');
    $item->end_at = GenController::filter($data->end_at, 'd');
    $item->license_number = GenController::filter($data->license_number, 'U');
    $item->license_path = DocMgrController::save(
      $data->license_path,
      DocMgrController::exist($data->license_doc),
      $data->license_dlt,
      'StudentDegrees'
    );
    $item->certificate_path = DocMgrController::save(
      $data->certificate_path,
      DocMgrController::exist($data->certificate_doc),
      $data->certificate_dlt,
      'StudentDegrees'
    );
    $item->title_path = DocMgrController::save(
      $data->title_path,
      DocMgrController::exist($data->title_doc),
      $data->title_dlt,
      'StudentDegrees'
    );
    
    // $item->license_path = "test.tst";
    // $item->certificate_path = "test.tst";
    $item->save();

    return $item;
  }
}
