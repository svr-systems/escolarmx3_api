<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use DB;
use Illuminate\Http\Request;
use Throwable;

class CycleController extends Controller
{
  public function index(Request $req) {
    try {
      return $this->apiRsp(
        200,
        'Registros retornados correctamente',
        ['items' => Cycle::getItems($req)]
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
        ['item' => Cycle::getItem($req, $id)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }

  public function destroy(Request $req, $id) {
    DB::beginTransaction();
    try {
      $item = Cycle::find($id);

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
      $item = Cycle::find($req->id);

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
        ['item' => Cycle::getItem(null, $item->id)]
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

      $valid = Cycle::validCode(['code' => $req->code], $id);
      if ($valid->fails()) {
        return $this->apiRsp(422, $valid->errors()->first());
      }

      $valid = Cycle::valid($req->all());

      if ($valid->fails()) {
        return $this->apiRsp(422, $valid->errors()->first());
      }

      $store_mode = is_null($id);

      if ($store_mode) {
        $item = new Cycle;
        $item->created_by_id = $req->user()->id;
        $item->updated_by_id = $req->user()->id;
      } else {
        $item = Cycle::find($id);
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

    $item->code = GenController::filter($data->code, 'U');
    $item->term_id = GenController::filter($data->term_id, 'id');
    $item->ops_start_at = GenController::filter($data->ops_start_at, 'd');
    $item->ops_end_at = GenController::filter($data->ops_end_at, 'd');
    $item->term_start_at = GenController::filter($data->term_start_at, 'd');
    $item->term_end_at = GenController::filter($data->term_end_at, 'd');
    $item->save();

    return $item;
  }
}
