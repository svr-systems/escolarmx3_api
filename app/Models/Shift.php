<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
  use HasFactory;
  public $timestamps = false;

  static public function getItems($req) {
    $items = Shift::
      where('is_active', true)->
      get();

    return $items;
  }
}
