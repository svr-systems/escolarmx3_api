<?php

namespace App\Models;

use App\Http\Controllers\DocMgrController;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Validator;

class StudentDegree extends Model
{
  protected function serializeDate(DateTimeInterface $date) {
    return Carbon::instance($date)->toISOString(true);
  }
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
    'updated_at' => 'datetime:Y-m-d H:i:s',
  ];

  public static function valid($data, $is_req = true) {
    $rules = [
      'student_id' => 'required|numeric',
      'level_id' => 'required|numeric',
      'institution_name' => 'required|min:2|max:100',
      'name' => 'required|min:2|max:100',
      'municipality_id' => 'required|numeric',
      'start_at' => 'required|date',
      'end_at' => 'required|date',
      'license_number' => 'required|min:2|max:20',
    ];

    if (!$is_req) {
      array_push($rules, ['is_active' => 'required|in:true,false,1,0']);
    }

    $msgs = [];

    return Validator::make($data, $rules, $msgs);
  }

  static public function getUiid($id) {
    return 'AE-' . str_pad($id, 4, '0', STR_PAD_LEFT);
  }

  static public function getItems($req) {
    $items = StudentDegree::
    where('student_id',$req->student_id)->
    where('is_active', boolval($req->is_active));

    $items = $items->
    get([
        'id',
        'is_active',
        'level_id',
        'institution_name',
        'name',
        'start_at',
        'end_at',
        'license_number',
      ]);

    foreach ($items as $key => $item) {
      $item->key = $key;
      $item->uiid = StudentDegree::getUiid($item->id);
      $item->level = Level::find($item->id);
    }

    return $items;
  }

  static public function getItem($req, $id) {
    $item = StudentDegree::
    find($id, [
        'id',
        'is_active',
        'created_at',
        'updated_at',
        'created_by_id',
        'updated_by_id',
        'level_id',
        'institution_name',
        'name',
        'municipality_id',
        'start_at',
        'end_at',
        'license_number',
        'license_path',
        'certificate_path',
        'title_path',
      ]);

    if ($item) {
      $item->uiid = StudentDegree::getUiid($item->id);
      $item->created_by = User::find($item->created_by_id, ['email']);
      $item->updated_by = User::find($item->updated_by_id, ['email']);
      $item->level = Level::find($item->level_id,['name']);
      $item->municipality = Municipality::find($item->municipality_id,['name','state_id']);
      $item->municipality->state = State::find($item->municipality->state_id, ['name']);
      $item->license_b64 = DocMgrController::getB64($item->license_path, 'StudentDegrees');
      $item->license_doc = null;
      $item->license_dlt = false;
      $item->certificate_b64 = DocMgrController::getB64($item->certificate_path, 'StudentDegrees');
      $item->certificate_doc = null;
      $item->certificate_dlt = false;
      $item->title_b64 = DocMgrController::getB64($item->title_path, 'StudentDegrees');
      $item->title_doc = null;
      $item->title_dlt = false;
    }

    return $item;
  }
}
