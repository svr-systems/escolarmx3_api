<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Controllers\GenController;
use App\Http\Controllers\DocMgrController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;
use DateTimeInterface;
use Validator;

class User extends Authenticatable {
  use HasApiTokens, HasFactory, Notifiable;
  protected function serializeDate(DateTimeInterface $date) {
    return Carbon::instance($date)->toISOString(true);
  }
  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:i:s',
    'updated_at' => 'datetime:Y-m-d H:i:s',
    'email_verified_at' => 'datetime:Y-m-d H:i:s',
  ];

  public static function validEmail($data, $id) {
    $rules = ['email' => 'required|string|min:2|max:65|regex:/.+@.+\..+/|unique:users,email,' . $id];

    $msgs = ['email.unique' => 'El E-mail ya ha sido registrado'];

    return Validator::make($data, $rules, $msgs);
  }

  public static function valid($data, $is_req = true) {
    $rules = [
      'role_id' => 'required|numeric',
      'name' => 'required|min:2|max:50',
      'surname_p' => 'required|min:2|max:25',
      'surname_m' => 'nullable|min:2|max:25',
      'curp' => 'required|min:18|max:18',
      'phone' => 'nullable|min:10|max:10',
      'marital_status_id' => 'nullable|numeric',
      'contact_kinship_id' => 'nullable|numeric',
      'contact_name' => 'nullable|min:2|max:100',
      'contact_phone' => 'nullable|min:10|max:15',
    ];

    if (!$is_req) {
      array_push($rules, ['is_active' => 'required|in:true,false,1,0']);
    }

    $msgs = [];

    return Validator::make($data, $rules, $msgs);
  }

  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  static public function getUiid($id) {
    return 'U-' . str_pad($id, 4, '0', STR_PAD_LEFT);
  }

  static public function getItems($req) {
    $items = User::
      where('is_active', boolval($req->is_active));

    $items = $items->
      orderBy('name')->
      orderBy('surname_p')->
      orderBy('surname_m')->
      get([
        'id',
        'is_active',
        'role_id',
        'name',
        'surname_p',
        'surname_m',
        'email',
        'email_verified_at',
      ]);

    foreach ($items as $key => $item) {
      $item->uiid = User::getUiid($item->id);
      $item->key = $key;
      $item->full_name = GenController::getFullName($item);
      $item->role = Role::find($item->role_id, ['name']);
    }

    return $items;
  }

  static public function getItem($req, $id) {
    $item = User::
      find($id, [
        'id',
        'is_active',
        'created_at',
        'updated_at',
        'created_by_id',
        'updated_by_id',
        'email_verified_at',
        'role_id',
        'name',
        'surname_p',
        'surname_m',
        'curp',
        'email',
        'phone',
        'marital_status_id',
        'avatar_path',
        'curp_path',
        'birth_certificate_path',
        'ine_path',
        'contact_kinship_id',
        'contact_name',
        'contact_phone',
      ]);

    if ($item) {
      $item->uiid = User::getUiid($item->id);
      $item->created_by = User::find($item->created_by_id, ['email']);
      $item->updated_by = User::find($item->updated_by_id, ['email']);
      $item->full_name = GenController::getFullName($item);
      $item->role = Role::find($item->role_id, ['name']);
      $item->marital_status = MaritalStatus::find($item->marital_status_id, ['name']);
      $item->contact_kinship = Kinship::find($item->contact_kinship_id, ['name']);
      $item->avatar_b64 = DocMgrController::getB64($item->avatar_path, 'User');
      $item->avatar_doc = null;
      $item->avatar_dlt = false;
      $item->curp_b64 = DocMgrController::getB64($item->curp_path, 'User');
      $item->curp_doc = null;
      $item->curp_dlt = false;
      $item->birth_certificate_b64 = DocMgrController::getB64($item->birth_certificate_path, 'User');
      $item->birth_certificate_doc = null;
      $item->birth_certificate_dlt = false;
      $item->ine_b64 = DocMgrController::getB64($item->ine_path, 'User');
      $item->ine_doc = null;
      $item->ine_dlt = false;
      $item->user_campuses = UserCampus::where('is_active',true)->where('user_id',$item->id)->get();

      foreach ($item->user_campuses as $key => $user_campus) {
        $user_campus->campus = Campus::find($user_campus->campus_id, ['name','code']);
      }
    }

    return $item;
  }

  static public function getItemAuth($id) {
    $item = User::find($id, [
      'id',
      'name',
      'surname_p',
      'surname_m',
      'avatar_path',
      'email',
      'role_id',
    ]);

    $item->uiid = User::getUiid($item->id);
    $item->full_name = GenController::getFullName($item);
    $item->role = Role::find($item->role_id, ['name']);

    return $item;
  }
}