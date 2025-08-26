<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\DocMgrController;
use Carbon\Carbon;
use DateTimeInterface;
use Validator;

class Institution extends Model {
    protected function serializeDate(DateTimeInterface $date) {
        return Carbon::instance($date)->toISOString(true);
    }
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public static function valid($data, $is_req = true) {
        $rules = [
            'name' => 'required|min:2|max:100',
            'code' => 'required|min:2|max:10',
            'cct' => 'required|min:2|max:10',
        ];

        if (!$is_req) {
            array_push($rules, ['is_active' => 'required|in:true,false,1,0']);
        }

        $msgs = [];

        return Validator::make($data, $rules, $msgs);
    }

    static public function getUiid($id) {
        return 'E-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }

    static public function getItems($req) {
        $items = Institution::
            where('is_active', boolval($req->is_active));

        $items = $items->
            get([
                'id',
                'is_active',
                'name',
                'code',
                'cct',
            ]);

        foreach ($items as $key => $item) {
            $item->key = $key;
            $item->uiid = Institution::getUiid($item->id);
        }

        return $items;
    }

    static public function getItem($req, $id) {
        $item = Institution::
            find($id, [
                'id',
                'is_active',
                'created_at',
                'updated_at',
                'created_by_id',
                'updated_by_id',
                'name',
                'code',
                'cct',
                'logo',
            ]);

        if ($item) {
            $item->uiid = Institution::getUiid($item->id);
            $item->created_by = User::find($item->created_by_id, ['email']);
            $item->updated_by = User::find($item->updated_by_id, ['email']);
            $item->logo_b64 = DocMgrController::getB64($item->logo, 'Institution');
            $item->logo_doc = null;
            $item->logo_dlt = false;
        }

        return $item;
    }
}
