<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
  use HasFactory;
  public $timestamps = false;

  static public function getItems($req) {
    $items = Term::
      where('is_active', true)->
      get();

    return $items;
  }
}
