<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Validator;

class Program extends Model {
  protected function serializeDate(DateTimeInterface $date) {
    return Carbon::instance($date)->toISOString(true);
  }
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
    'updated_at' => 'datetime:Y-m-d H:i:s',
  ];

  public static function valid($data, $is_req = true) {
    $rules = [
      'campus_id' => 'required|numeric',
      'name' => 'required|min:2|max:100',
      'code' => 'required|min:2|max:10',
      'issued_at' => 'required|date',
      'accreditation_id' => 'required|numeric',
      'modality_id' => 'required|numeric',
      'shift_id' => 'required|numeric',
      'responsible_curp' => 'min:2|max:18',
      'plan_year' => 'required|numeric',
      'level_id' => 'required|numeric',
      'term_id' => 'required|numeric',
      'terms_count' => 'required|numeric',
      'grade_min' => 'required|numeric',
      'grade_max' => 'required|numeric',
      'grade_pass' => 'required|numeric',
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
    $items = Program::
    where('is_active', boolval($req->is_active));

    $items = $items->
    get([
        'id',
        'is_active',
        'name',
        'code',
      ]);

    foreach ($items as $key => $item) {
      $item->key = $key;
      $item->uiid = Program::getUiid($item->id);
    }

    return $items;
  }

  static public function getItem($req, $id) {
    $item = Program::
    find($id, [
        'id',
        'is_active',
        'created_at',
        'updated_at',
        'created_by_id',
        'updated_by_id',
        'campus_id',
        'name',
        'code',
        'issued_at',
        'accreditation_id',
        'modality_id',
        'shift_id',
        'responsible_curp',
        'plan_year',
        'level_id',
        'term_id',
        'terms_count',
        'grade_min',
        'grade_max',
        'grade_pass',
      ]);

    if ($item) {
      $item->uiid = Program::getUiid($item->id);
      $item->created_by = User::find($item->created_by_id, ['email']);
      $item->updated_by = User::find($item->updated_by_id, ['email']);
      $item->accreditation = User::find($item->accreditation_id);
      $item->modality = User::find($item->modality_id);
      $item->shift = User::find($item->shift_id);
      $item->level = User::find($item->level_id);
      $item->term = User::find($item->term_id);
    }

    return $item;
  }
}
