<?php

namespace App\Http\Controllers;
use App\Mail\GenMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Http\Request;

class EmailController extends Controller
{
  public static function userAccountConfirmation($email, $data) {
    $email = GenController::isAppDebug() ? env('MAIL_DEBUG') : env('MAIL_DEBUG');

    if (!GenController::empty($email)) {
      $data->link =
        (GenController::isAppDebug() ? env('SERVER_DEBUG') : env('SERVER')) .
        '/confirmar_cuenta/' .
        Crypt::encryptString($data->id);
      Mail::to($email)->send(new GenMailable($data, 'Confirmar cuenta', 'UserAccountConfirmation'));
    }
  }
}
