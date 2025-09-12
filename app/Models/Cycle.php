<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Validator;

class Cycle extends Model
{
  protected function serializeDate(DateTimeInterface $date)
  {
    return Carbon::instance($date)->toISOString(true);
  }
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
    'updated_at' => 'datetime:Y-m-d H:i:s',
  ];

  public static function valid($data, $is_req = true)
  {
    $rules = [
      'code' => 'required|min:2|max:10',
      'term_id' => 'required|numeric',
      'ops_start_at' => 'required|date',
      'ops_end_at' => 'required|date',
      'term_start_at' => 'required|date',
      'term_end_at' => 'required|date',
    ];

    if (!$is_req) {
      array_push($rules, ['is_active' => 'required|in:true,false,1,0']);
    }

    $msgs = [];

    return Validator::make($data, $rules, $msgs);
  }

  public static function validCode($data, $id)
  {
    $rules = ['code' => 'unique:cycles,code,' . $id];

    $msgs = ['code.unique' => 'El código ya ha sido registrado'];

    return Validator::make($data, $rules, $msgs);
  }

  static public function getUiid($id)
  {
    return 'C-' . str_pad($id, 4, '0', STR_PAD_LEFT);
  }

  static public function getItems($req)
  {
    $items = Cycle::where('is_active', boolval($req->is_active));

    if ($req->user()->id !== 1) {
      $items = $items->where('created_by_id', $req->user()->id);
    }

    $items = $items->get([
      'id',
      'is_active',
      'code',
      'term_id',
      'ops_start_at',
      'ops_end_at',
      'term_start_at',
      'term_end_at',
    ]);

    foreach ($items as $key => $item) {
      $item->key = $key;
      $item->term = Term::find($item->term_id, 'name');
      $item->ops_at = $item->ops_start_at . " | " . $item->ops_end_at;
      $item->term_at = $item->term_start_at . " | " . $item->term_end_at;
    }

    return $items;
  }

  static public function getItem($req, $id)
  {
    $item = Cycle::find($id, [
      'id',
      'is_active',
      'created_at',
      'updated_at',
      'created_by_id',
      'updated_by_id',
      'code',
      'term_id',
      'ops_start_at',
      'ops_end_at',
      'term_start_at',
      'term_end_at',
    ]);

    if ($item) {
      $item->uiid = Cycle::getUiid($item->id);
      $item->created_by = User::find($item->created_by_id, ['email']);
      $item->updated_by = User::find($item->updated_by_id, ['email']);
      $item->term = Term::find($item->term_id, ['name']);
    }

    return $item;
  }
}
