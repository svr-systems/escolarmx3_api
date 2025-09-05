<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Validator;

class Course extends Model {
  protected function serializeDate(DateTimeInterface $date) {
    return Carbon::instance($date)->toISOString(true);
  }
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
    'updated_at' => 'datetime:Y-m-d H:i:s',
  ];

  public static function valid($data, $is_req = true) {
    $rules = [
      'program_id' => 'required|numeric',
      'name' => 'required|min:2|max:100',
      'course_type_id' => 'required|numeric',
      'code' => 'required|min:2|max:10',
      'alt_code' => 'min:2|max:10',
      'credits' => 'required|numeric',
      'session_minutes' => 'required|numeric',
      'term' => 'required|numeric',
      'prerequisite_course_id' => 'nullable|numeric',
    ];

    if (!$is_req) {
      array_push($rules, ['is_active' => 'required|in:true,false,1,0']);
    }

    $msgs = [];

    return Validator::make($data, $rules, $msgs);
  }

  static public function getUiid($id) {
    return 'C-' . str_pad($id, 4, '0', STR_PAD_LEFT);
  }

  static public function getItems($req) {
    $items = Course::
    where('program_id',$req->program_id)->
    where('is_active', boolval($req->is_active))->
    orderBy('code');

    $items = $items->
    get([
        'id',
        'is_active',
        'name',
        'course_type_id',
        'code',
        'alt_code',
        'credits',
      ]);

    foreach ($items as $key => $item) {
      $item->key = $key;
      $item->course_type = CourseType::find($item->course_type_id, ['name']);
      // $item->uiid = Course::getUiid($item->id);
    }

    return $items;
  }

  static public function getItem($req, $id) {
    $item = Course::
    find($id, [
        'id',
        'is_active',
        'created_at',
        'updated_at',
        'created_by_id',
        'updated_by_id',
        // 'program_id',
        'name',
        'course_type_id',
        'code',
        'alt_code',
        'credits',
        'session_minutes',
        'term',
        'prerequisite_course_id',
      ]);

    if ($item) {
      $item->uiid = Course::getUiid($item->id);
      $item->created_by = User::find($item->created_by_id, ['email']);
      $item->updated_by = User::find($item->updated_by_id, ['email']);
      // $item->program = User::find($item->program_id);
      $item->course_type = CourseType::find($item->course_type_id,['name']);
      $item->prerequisite_course = Course::find($item->prerequisite_course_id);
    }

    return $item;
  }
}
