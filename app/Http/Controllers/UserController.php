<?php

namespace App\Http\Controllers;

use App\Models\UserCampus;
use Illuminate\Http\Request;
use App\Models\User;
use Throwable;
use DB;

class UserController extends Controller {
  public function index(Request $req) {
    try {
      return $this->apiRsp(
        200,
        'Registros retornados correctamente',
        ['items' => User::getItems($req)]
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
        ['item' => User::getItem($req, $id)]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }

  public function destroy(Request $req, $id) {
    DB::beginTransaction();
    try {
      $item = User::find($id);

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

  public function store(Request $req) {
    return $this->storeUpdate($req, null);
  }

  public function update(Request $req, $id) {
    return $this->storeUpdate($req, $id);
  }

  public function storeUpdate($req, $id) {
    DB::beginTransaction();
    try {
      $email_current = null;
      $email = GenController::filter($req->email, 'l');

      $valid = User::validEmail(['email' => $email], $id);
      if ($valid->fails()) {
        return $this->apiRsp(422, $valid->errors()->first());
      }

      $valid = User::valid($req->all());
      if ($valid->fails()) {
        return $this->apiRsp(422, $valid->errors()->first());
      }

      $store_mode = is_null($id);

      if ($store_mode) {
        $item = new User;
        $item->created_by_id = $req->user()->id;
        $item->updated_by_id = $req->user()->id;
      } else {
        $item = User::find($id);
        $email_current = $item->email;

        $item->updated_by_id = $req->user()->id;
      }

      $item = $this->saveItem($item, $req);

      if ($email_current != $email) {
        $item->email_verified_at = null;
        $item->save();

        EmailController::userAccountConfirmation($item->email, $item);
      }

      if ($req->user_campuses) {
        foreach ($req->user_campuses as $user_campus) {
          $user_campus_item = UserCampus::find($user_campus['id']);
          if (!$user_campus_item) {
            $user_campus_item = new UserCampus;
          }
          $user_campus_item->is_active = GenController::filter($user_campus['is_active'], 'b');
          $user_campus_item->campus_id = GenController::filter($user_campus['campus_id'], 'id');
          $user_campus_item->user_id = $item->id;
          $user_campus_item->save();
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
    if (!$is_req) {
      $item->active = GenController::filter($data->active, 'b');
    }

    $item->name = GenController::filter($data->name, 'U');
    $item->surname_p = GenController::filter($data->surname_p, 'U');
    $item->surname_m = GenController::filter($data->surname_m, 'U');
    $item->curp = GenController::filter($data->curp, 'U');
    $item->phone = GenController::filter($data->phone, 'U');
    $item->email = GenController::filter($data->email, 'l');
    $item->role_id = GenController::filter($data->role_id, 'id');
    $item->marital_status_id = GenController::filter($data->marital_status_id, 'id');
    $item->avatar_url = DocMgrController::save(
      $data->avatar_url,
      DocMgrController::exist($data->avatar_doc),
      $data->avatar_dlt,
      'User'
    );
    $item->save();

    return $item;
  }

  public function getDni(Request $req) {
    try {
      $image_controller = new ImageController;
      $img_b64 = $image_controller->UserDNI($req->user_id);
      return $this->apiRsp(
        200,
        'Registros retornados correctamente',
        [
          'img64' => $img_b64['jpg64'],
          'ext' => '.jpg'
        ]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }
}
