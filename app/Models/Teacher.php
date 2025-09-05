<?php

namespace App\Models;

use App\Http\Controllers\DocMgrController;
use App\Http\Controllers\GenController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Validator;

class Teacher extends Model {
  use HasFactory;
  public $timestamps = false;

  public static function valid($data, $is_req = true) {
    $rules = [
      'name' => 'required|min:2|max:100',
      'code' => 'required|min:2|max:8',
      'cct' => 'required|min:2|max:8',
    ];

    if (!$is_req) {
      array_push($rules, ['is_active' => 'required|in:true,false,1,0']);
    }

    $msgs = [];

    return Validator::make($data, $rules, $msgs);
  }

  static public function getUiid($id) {
    return 'D-' . str_pad($id, 4, '0', STR_PAD_LEFT);
  }

  static public function getItems($req) {
    $items = Teacher::
      join('users', 'teachers.user_id', 'users.id')->
      where('is_active', boolval($req->is_active));

    $items = $items->
      get([
        'teachers.id',
        'users.is_active',
        'user_id',
      ]);

    foreach ($items as $key => $item) {
      $item->key = $key;
      $item->uiid = Teacher::getUiid($item->id);
      $item->user = User::find($item->user_id);
      $item->user->full_name = GenController::getFullName($item->user);
    }

    return $items;
  }

  static public function getItem($req, $id) {
    $item = Teacher::
      find($id, [
        'id',
        'user_id',
        'cv_path',
      ]);

    if ($item) {
      $item->uiid = Teacher::getUiid($item->id);
      $item->created_by = User::find($item->created_by_id, ['email']);
      $item->updated_by = User::find($item->updated_by_id, ['email']);
      $item->user = User::getItem(null, $item->user_id);
      $item->cv_b64 = DocMgrController::getB64($item->cv_path, 'Teachers');
      $item->cv_doc = null;
      $item->cv_dlt = false;

      $item->teacher_degrees = TeacherDegree::where('is_active', true)->where('teacher_id', $item->id)->get();
      foreach ($item->teacher_degrees as $key => $teacher_degree) {
        $teacher_degree->license_b64 = DocMgrController::getB64($teacher_degree->license_path, 'TeacherDegrees');
        $teacher_degree->license_doc = null;
        $teacher_degree->license_dlt = false;
        $teacher_degree->level = Level::find($teacher_degree->level_id, ['name', 'code']);
      }
    }

    return $item;
  }
}
