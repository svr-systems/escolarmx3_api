<?php

namespace App\Http\Controllers;

use App\Models\StudentDocument;
use DB;
use Illuminate\Http\Request;
use Throwable;

class StudentDocumentController extends Controller
{
  public function index(Request $req)
  {
    try {
      return $this->apiRsp(
        200,
        'Registros retornados correctamente',
        ['items' => StudentDocument::getItems($req->student_id, $req->is_active)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }

  public function show(Request $req, $id)
  {
    try {
      return $this->apiRsp(
        200,
        'Registro retornado correctamente',
        ['item' => StudentDocument::getItem($req, $id)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }

  public function destroy(Request $req, $id)
  {
    DB::beginTransaction();
    try {
      $item = StudentDocument::find($id);

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

  public function restore(Request $req)
  {
    DB::beginTransaction();
    try {
      $item = StudentDocument::find($req->id);

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
        ['item' => StudentDocument::getItem(null, $item->id)]
      );
    } catch (Throwable $err) {
      DB::rollback();
      return $this->apiRsp(500, null, $err);
    }
  }

  public function store(Request $req)
  {
    return $this->storeUpdate($req, null);
  }

  public function update(Request $req, $id)
  {
    return $this->storeUpdate($req, $id);
  }

  public function storeUpdate($req, $id)
  {
    DB::beginTransaction();
    try {

      $valid = StudentDocument::valid($req->all());

      if ($valid->fails()) {
        return $this->apiRsp(422, $valid->errors()->first());
      }

      $store_mode = is_null($id);

      if ($store_mode) {
        $item = new StudentDocument;
        $item->created_by_id = $req->user()->id;
        $item->updated_by_id = $req->user()->id;
      } else {
        $item = StudentDocument::find($id);
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

  public static function saveItem($item, $data, $is_req = true)
  {
    if (!$is_req) {
      $item->active = GenController::filter($data->active, 'b');
    }

    $item->student_id = GenController::filter($data->student_id, 'id');
    $item->received_at = GenController::filter($data->received_at, 'd');
    $item->document_type_id = GenController::filter($data->document_type_id, 'id');
    $item->is_original_left = GenController::filter($data->is_original_left, 'b');
    $item->copies_count = GenController::filter($data->copies_count, 'i');
    $item->document_path = DocMgrController::save(
      $data->document_path,
      DocMgrController::exist($data->document_doc),
      $data->document_dlt,
      'StudentDocuments'
    );
    // $item->document_path = "test.tst";
    $item->save();

    return $item;
  }
}
