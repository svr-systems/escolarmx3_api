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

  public function restore(Request $req) {
    DB::beginTransaction();
    try {
      $item = User::find($req->id);

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
        ['item' => User::getItem(null, $item->id)]
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

      $this->saveDocuments($item,$req,$req);

      if ($email_current != $email) {
        $item->email_verified_at = null;
        $item->save();

        EmailController::userAccountConfirmation($item->email, $item);
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

    $item->role_id = GenController::filter($data->role_id, 'id');
    $item->name = GenController::filter($data->name, 'U');
    $item->surname_p = GenController::filter($data->surname_p, 'U');
    $item->surname_m = GenController::filter($data->surname_m, 'U');
    $item->curp = GenController::filter($data->curp, 'U');
    $item->email = GenController::filter($data->email, 'l');
    $item->phone = GenController::filter($data->phone, 'U');
    $item->marital_status_id = GenController::filter($data->marital_status_id, 'id');
    $item->contact_name = GenController::filter($data->contact_name, 'U');
    $item->contact_phone = GenController::filter($data->contact_phone, 'U');
    $item->save();

    if (isset($data->user_campuses)) {
      foreach ($data->user_campuses as $user_campus) {
        $user_campus = (array) $user_campus;
        $user_campus_item = UserCampus::find($user_campus['id']);
        if (!$user_campus_item) {
          $user_campus_item = new UserCampus;
        }
        $user_campus_item->is_active = GenController::filter($user_campus['is_active'], 'b');
        $user_campus_item->user_id = $item->id;
        $user_campus_item->campus_id = GenController::filter($user_campus['campus_id'], 'id');
        $user_campus_item->save();
      }
    }

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

  public static function saveDocuments($item, $req, $data,$prefix = '') {
    $avatar_doc = $prefix . 'avatar_doc';
    $item->avatar_path = DocMgrController::save(
      $data->avatar_path,
      DocMgrController::exist($req->$avatar_doc),
      $data->avatar_dlt,
      'User'
    );

    $curp_doc = $prefix . 'curp_doc';
    $item->curp_path = DocMgrController::save(
      $data->curp_path,
      DocMgrController::exist($req->$curp_doc),
      $data->curp_dlt,
      'User'
    );
    
    $birth_certificate_doc = $prefix . 'birth_certificate_doc';
    $item->birth_certificate_path = DocMgrController::save(
      $data->birth_certificate_path,
      DocMgrController::exist($req->$birth_certificate_doc),
      $data->birth_certificate_dlt,
      'User'
    );
    
    $ine_doc = $prefix . 'ine_doc';
    $item->ine_path = DocMgrController::save(
      $data->ine_path,
      DocMgrController::exist($req->$ine_doc),
      $data->ine_dlt,
      'User'
    );
    $item->save();
  }
}
