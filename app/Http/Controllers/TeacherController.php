<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\TeacherDegree;
use Illuminate\Http\Request;
use App\Models\User;
use Throwable;
use DB;

class TeacherController extends Controller {
  public function index(Request $req) {
    try {
      return $this->apiRsp(
        200,
        'Registros retornados correctamente',
        ['items' => Teacher::getItems($req)]
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
        ['item' => Teacher::getItem($req, $id)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }

  public function destroy(Request $req, $id) {
    DB::beginTransaction();
    try {
      $item = Teacher::find($id);

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
      $item = Teacher::find($req->id);

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
        ['item' => Teacher::getItem(null, $item->id)]
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
      // $user_data->role_id = 4;
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

        $item = new Teacher;
      } else {
        $item = Teacher::find($id);
        $user = User::find($item->user_id);
        $email_current = $user->email;

        $user->updated_by_id = $req->user()->id;
      }

      $user = UserController::saveItem($user, $user_data);
      $item->user_id = $user->id;
      $item->save();

      $user = User::find($user->id);
      $user->curp_path = DocMgrController::save(
        $req->curp_path,
        DocMgrController::exist($req->user_curp_doc),
        $req->curp_dlt,
        'User'
      );
      $user->avatar_path = DocMgrController::save(
        $req->avatar_path,
        DocMgrController::exist($req->user_avatar_doc),
        $req->avatar_dlt,
        'User'
      );

      $user->save();

      if ($req->teacher_degrees) {
        $teacher_degrees = json_decode($req->teacher_degrees);
        foreach ($teacher_degrees as $key => $teacher_degree) {
          $teacher_degree = (array) $teacher_degree;
          $teacher_degree_item = TeacherDegree::find($teacher_degree['id']);
          if (!$teacher_degree_item) {
            $teacher_degree_item = new TeacherDegree;
          }
          $teacher_degree_item->is_active = GenController::filter($teacher_degree['is_active'], 'b');
          $teacher_degree_item->level_id = GenController::filter($teacher_degree['level_id'], 'id');
          $teacher_degree_item->institution_name = GenController::filter($teacher_degree['institution_name'], 'U');
          $teacher_degree_item->name = GenController::filter($teacher_degree['name'], 'U');
          $teacher_degree_item->license_number = GenController::filter($teacher_degree['license_number'], 'U');
          $teacher_degree_item->teacher_id = $item->id;
          $file_name = 'teacher_degrees_license_doc_' . $key;
          $teacher_degree_item->license_path = DocMgrController::save(
            $teacher_degree['license_path'],
            DocMgrController::exist($req->$file_name),
            $req->license_dlt,
            'TeacherDegrees'
          );
          // $teacher_degree_item->license_path = "---";
          $teacher_degree_item->save();
        }
      }

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

  }
}
