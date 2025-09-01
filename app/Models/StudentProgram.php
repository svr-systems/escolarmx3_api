<?php

namespace App\Models;

use App\Http\Controllers\DocMgrController;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Validator;

class StudentProgram extends Model {
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
      'program_id' => 'required|numeric',
      'cycle_entry_id' => 'required|numeric',
      'is_equivalency' => 'required|boolean',
      'cycle_dropout_id' => 'nullable|numeric',
      'cycle_reentry_id' => 'nullable|numeric',
      'cycle_graduated_id' => 'nullable|numeric',
    ];

    if (!$is_req) {
      array_push($rules, ['is_active' => 'required|in:true,false,1,0']);
    }

    $msgs = [];

    return Validator::make($data, $rules, $msgs);
  }

  static public function getUiid($id) {
    return 'AP-' . str_pad($id, 4, '0', STR_PAD_LEFT);
  }

  static public function getItems($req) {
    $items = StudentProgram::
      where('student_id', $req->student_id)->
      where('is_active', boolval($req->is_active));

    $items = $items->
    get([
        'id',
        'is_active',
        'is_equivalency',
      ]);

    foreach ($items as $key => $item) {
      $item->key = $key;
      $item->uiid = StudentProgram::getUiid($item->id);
      $item->document_type = DocumentType::find($item->document_type_id);
    }

    return $items;
  }

  static public function getItem($req, $id) {
    $item = StudentProgram::
    find($id, [
        'id',
        'is_active',
        'created_at',
        'updated_at',
        'created_by_id',
        'updated_by_id',
        'program_id',
        'cycle_entry_id',
        'is_equivalency',
        'equivalency_path',
        'cycle_dropout_id',
        'cycle_reentry_id',
        'cycle_graduated_id',
      ]);

    if ($item) {
      $item->uiid = StudentProgram::getUiid($item->id);
      $item->created_by = User::find($item->created_by_id, ['email']);
      $item->updated_by = User::find($item->updated_by_id, ['email']);
      $item->program = Program::find($item->program_id);
      $item->cycle_entry = Cycle::find($item->cycle_entry_id);
      $item->cycle_dropout = Cycle::find($item->cycle_dropout_id);
      $item->cycle_reentry = Cycle::find($item->cycle_reentry_id);
      $item->cycle_graduated = Cycle::find($item->cycle_graduated_id);
      $item->equivalency_b64 = DocMgrController::getB64($item->equivalency_path, 'StudentEquivalencies');
      $item->equivalency_doc = null;
      $item->equivalency_dlt = false;
    }

    return $item;
  }
}
