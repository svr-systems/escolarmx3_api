<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserCampus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AuthController extends Controller {
  public function login(Request $req) {
    try {
      $email = GenController::filter($req->email, 'l');

      $user = User::
        where('email', $email)->
        first();

      // if ($user && is_null($user->email_verified_at)) {
      //   return $this->apiRsp(422, 'E-mail pendiente de verificación, revisa tu bandeja de entrada', null);
      // }

      if (
        !Auth::attempt([
          'email' => $email,
          'password' => trim($req->password)
        ])
      ) {
        return $this->apiRsp(422, 'Datos de acceso inválidos', null);
      }

      // if (!boolval(Auth::user()->active)) {
      //   return $this->apiRsp(422, 'Cuenta inactiva', null);
      // }

      $campus_id = null;
      $user_campuses = UserCampus::getUserCampuses(Auth::id());

      if ($user->role_id === 2) {
        $campus_id = ($user_campuses) ? $user_campuses[0]->campus_id : null;
      }



      return $this->apiRsp(
        200,
        'Datos de acceso validos',
        [
          'auth' => [
            'token' => Auth::user()->createToken('passportToken')->accessToken,
            'user' => User::getItemAuth(Auth::id()),
            'user_campuses' => $user_campuses,
            'campus_id' => $campus_id
          ]
        ]
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }

  public function logout(Request $req) {
    try {
      $req->user()->token()->revoke();

      return $this->apiRsp(
        200,
        'Sesión finalizada correctamente'
      );
    } catch (Throwable $err) {
      return $this->apiRsp(500, null, $err);
    }
  }
}
