<?php

namespace App\Models;

use App\Http\Controllers\DocMgrController;
use App\Http\Controllers\GenController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Validator;

class Student extends Model {
  use HasFactory;
  public $timestamps = false;

  public static function valid($data, $is_req = true) {
    $rules = [
      'user_id' => 'required|numeric',
      'student_number' => 'nullable|min:2|max:15',
      'guardian_kinship_id' => 'nullable|numeric',
      'guardian_name' => 'nullable|min:2|max:100',
      'guardian_phone' => 'nullable|min:2|max:15',
    ];

    if (!$is_req) {
      array_push($rules, ['is_active' => 'required|in:true,false,1,0']);
    }

    $msgs = [];

    return Validator::make($data, $rules, $msgs);
  }

  static public function getUiid($id) {
    return 'E-' . str_pad($id, 4, '0', STR_PAD_LEFT);
  }

  static public function getItems($req) {
    $items = Student::
      join('users', 'students.user_id', 'users.id')->
      where('is_active', boolval($req->is_active));

    $items = $items->
      get([
        'students.id',
        'users.is_active',
        'user_id',
        'student_number',
        'guardian_kinship_id',
        'guardian_name',
        'guardian_phone',
      ]);

    foreach ($items as $key => $item) {
      $item->key = $key;
      $item->uiid = Student::getUiid($item->id);
      $item->user = User::find($item->user_id, ['name', 'surname_p', 'surname_m', 'curp']);
      $item->user->full_name = GenController::getFullName($item->user);
    }

    return $items;
  }

  static public function getItem($req, $id) {
    $item = Student::
      find($id, [
        'id',
        'user_id',
        'student_number',
        'guardian_kinship_id',
        'guardian_name',
        'guardian_phone',
        'birth_certificate_path',
      ]);

    if ($item) {
      $item->uiid = Student::getUiid($item->id);
      $item->created_by = User::find($item->created_by_id, ['email']);
      $item->updated_by = User::find($item->updated_by_id, ['email']);
      $item->user = User::getItem(null,$item->user_id);
      $item->guardian_kinship = Kinship::find($item->guardian_kinship_id);
      $item->birth_certificate_b64 = DocMgrController::getB64($item->birth_certificate_path, 'Students');
      $item->birth_certificate_doc = null;
      $item->birth_certificate_dlt = false;
    }

    return $item;
  }
}
