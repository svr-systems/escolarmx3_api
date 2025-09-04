<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCampus extends Model {
  use HasFactory;
  public $timestamps = false;

  static public function getUserCampuses($user_id) {
    $items = UserCampus::where('user_id', $user_id)->
      Where('is_active', 1)->
      get(['campus_id']);

    foreach ($items as $item) {
      $item->campus = Campus::find($item->campus_id, ['name', 'code']);
    }

    return $items;
  }
}
