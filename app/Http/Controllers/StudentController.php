<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Throwable;

class StudentController extends Controller {
  public function index(Request $req) {
    try {
      return $this->apiRsp(
        200,
        'Registros retornados correctamente',
        ['items' => Student::getItems($req)]
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
        ['item' => Student::getItem($req, $id)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }

  public function destroy(Request $req, $id) {
    DB::beginTransaction();
    try {
      $item = Student::find($id);

      if (!$item) {
        return $this->apiRsp(422, 'ID no existente');
      }

      $user = User::find($item->user_id);
      $user->is_active = false;
      $user->updated_by_id = $req->user()->id;
      $user->save();

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
      $item = Student::find($req->id);

      if (!$item) {
        return $this->apiRsp(422, 'ID no existente');
      }

      $user = User::find($item->user_id);
      $user->is_active = true;
      $user->updated_by_id = $req->user()->id;
      $user->save();

      DB::commit();
      return $this->apiRsp(
        200,
        'Registro activado correctamente',
        ['item' => Student::getItem(null, $item->id)]
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
      $user_data = json_decode($req->user);
      $user_data->role_id = 5;
      $email_current = null;
      $email = GenController::filter($user_data->email, 'l');

      $valid = User::validEmail(['email' => $email], $user_data->id);
      if ($valid->fails()) {
        return $this->apiRsp(422, $valid->errors()->first());
      }

      $valid = User::valid((array) $user_data);
      if ($valid->fails()) {
        return $this->apiRsp(422, $valid->errors()->first());
      }

      $store_mode = is_null($id);

      if ($store_mode) {
        $user = new User;
        $user->created_by_id = $req->user()->id;
        $user->updated_by_id = $req->user()->id;

        $item = new Student;
      } else {
        $item = Student::find($id);
        $user = User::find($item->user_id);
        $email_current = $user->email;

        $user->updated_by_id = $req->user()->id;
      }

      $user = UserController::saveItem($user, $user_data);
      $req->user_id = $user->id;
      $this->saveItem($item, $req);
      
      $user = User::find($user->id);
      UserController::saveDocuments($user, $req, $user_data, 'user_');

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

    $item->user_id = GenController::filter($data->user_id, 'id');
    $item->student_number = GenController::filter($data->student_number, 'U');
    $item->save();

    return $item;
  }
}
