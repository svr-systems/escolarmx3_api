<?php

namespace App\Http\Controllers;

use App\Models\Program;
use DB;
use Illuminate\Http\Request;
use Throwable;

class ProgramController extends Controller {
  public function index(Request $req) {
    try {
      return $this->apiRsp(
        200,
        'Registros retornados correctamente',
        ['items' => Program::getItems($req)]
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
        ['item' => Program::getItem($req, $id)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }

  public function destroy(Request $req, $id) {
    DB::beginTransaction();
    try {
      $item = Program::find($id);

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
      $item = Program::find($req->id);

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
        ['item' => Program::getItem(null, $item->id)]
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

      $valid = Program::validCode(['code' => $req->code], $id);
      if ($valid->fails()) {
        return $this->apiRsp(422, $valid->errors()->first());
      }

      $valid = Program::valid($req->all());

      if ($valid->fails()) {
        return $this->apiRsp(422, $valid->errors()->first());
      }

      $store_mode = is_null($id);

      if ($store_mode) {
        $item = new Program;
        $item->created_by_id = $req->user()->id;
        $item->updated_by_id = $req->user()->id;
      } else {
        $item = Program::find($id);
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

    $item->campus_id = GenController::filter($data->campus_id, 'id');
    $item->name = GenController::filter($data->name, 'U');
    $item->code = GenController::filter($data->code, 'U');
    $item->issued_at = GenController::filter($data->issued_at, 'U');
    $item->accreditation_id = GenController::filter($data->accreditation_id, 'id');
    $item->modality_id = GenController::filter($data->modality_id, 'id');
    $item->shift_id = GenController::filter($data->shift_id, 'id');
    $item->responsible_curp = GenController::filter($data->responsible_curp, 'U');
    $item->plan_year = GenController::filter($data->plan_year, 'i');
    $item->level_id = GenController::filter($data->level_id, 'id');
    $item->term_id = GenController::filter($data->term_id, 'id');
    $item->terms_count = GenController::filter($data->terms_count, 'f');
    $item->grade_min = GenController::filter($data->grade_min, 'f');
    $item->grade_max = GenController::filter($data->grade_max, 'f');
    $item->grade_pass = GenController::filter($data->grade_pass, 'f');
    $item->save();

    return $item;
  }
}
