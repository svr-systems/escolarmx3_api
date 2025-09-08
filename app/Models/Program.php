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
      'code' => 'required|min:2|max:15',
      'issued_at' => 'required|date',
      'accreditation_id' => 'required|numeric',
      'modality_id' => 'required|numeric',
      'shift_id' => 'required|numeric',
      'responsible_curp' => 'nullable|min:2|max:18',
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
  public static function validCode($data, $id) {
    $rules = ['code' => 'unique:programs,code,' . $id];

    $msgs = ['code.unique' => 'El RVOE ya ha sido registrado'];

    return Validator::make($data, $rules, $msgs);
  }

  static public function getUiid($id) {
    return 'C-' . str_pad($id, 4, '0', STR_PAD_LEFT);
  }

  static public function getItems($req) {
    $items = Program::
      // where('campus_id', $req->campus_id)->   AJUSTAR DESPUES
      where('is_active', boolval($req->is_active));

    $items = $items->
      orderBy('campus_id')->
      orderBy('name')->
      get([
        'id',
        'is_active',
        'name',
        'code',
        'plan_year',
        'campus_id' //QUITAR DESPUES
      ]);

    foreach ($items as $key => $item) {
      $item->key = $key;
      // $item->uiid = Program::getUiid($item->id);
      $item->campus = Campus::find($item->campus_id, ['name']); //QUITAR DESPUES
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
      $item->accreditation = Accreditation::find($item->accreditation_id,['name']);
      $item->modality = Modality::find($item->modality_id,['name']);
      $item->shift = Shift::find($item->shift_id,['name']);
      $item->level = Level::find($item->level_id,['name']);
      $item->term = Term::find($item->term_id,['name']);
      
      $item->campus = Campus::find($item->campus_id, ['name']); //QUITAR DESPUES
    }

    return $item;
  }
}
