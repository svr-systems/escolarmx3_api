<?php

namespace App\Models;

use App\Http\Controllers\DocMgrController;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Validator;

class StudentDocument extends Model {
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
      'received_at' => 'required|date',
      'document_type_id' => 'required|numeric',
      'is_original_left' => 'required|in:true,false,1,0',
      'copies_count' => 'required|numeric',
    ];

    if (!$is_req) {
      array_push($rules, ['is_active' => 'required|in:true,false,1,0']);
    }

    $msgs = [];

    return Validator::make($data, $rules, $msgs);
  }

  static public function getUiid($id) {
    return 'AD-' . str_pad($id, 4, '0', STR_PAD_LEFT);
  }

  static public function getItems($req) {
    $items = StudentDocument::
      where('student_id', $req->student_id)->
      where('is_active', boolval($req->is_active));

    $items = $items->
    get([
        'id',
        'is_active',
        'received_at',
        'document_type_id',
        'is_original_left',
        'copies_count',
      ]);

    foreach ($items as $key => $item) {
      $item->key = $key;
      $item->uiid = StudentDocument::getUiid($item->id);
      $item->document_type = DocumentType::find($item->document_type_id);
    }

    return $items;
  }

  static public function getItem($req, $id) {
    $item = StudentDocument::
    find($id, [
        'id',
        'is_active',
        'created_at',
        'updated_at',
        'created_by_id',
        'updated_by_id',
        'received_at',
        'document_type_id',
        'is_original_left',
        'copies_count',
        'document_path',
      ]);

    if ($item) {
      $item->uiid = StudentDocument::getUiid($item->id);
      $item->created_by = User::find($item->created_by_id, ['email']);
      $item->updated_by = User::find($item->updated_by_id, ['email']);
      $item->document_type = DocumentType::find($item->document_type_id,['name']);
      $item->document_b64 = DocMgrController::getB64($item->document_path, 'StudentDocuments');
      $item->document_doc = null;
      $item->document_dlt = false;
    }

    return $item;
  }
}
