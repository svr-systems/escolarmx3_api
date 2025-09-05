<?php

namespace App\Models;

use App\Http\Controllers\DocMgrController;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Validator;

class Campus extends Model {
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
            'municipality_id' => 'required|numeric',
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
        $items = Campus::
            where('is_active', boolval($req->is_active));

        $items = $items->
        get([
                'id',
                'is_active',
                'name',
                'code',
                'municipality_id',
            ]);

        foreach ($items as $key => $item) {
            $item->key = $key;
            $item->uiid = Campus::getUiid($item->id);
            $item->municipality = Municipality::find($item->municipality_id, ['name', 'state_id']);
            $item->state = State::find($item->municipality->state_id, ['name']);
        }

        return $items;
    }

    static public function getItem($req, $id) {
        $item = Campus::
        find($id, [
                'id',
                'is_active',
                'created_at',
                'updated_at',
                'created_by_id',
                'updated_by_id',
                'name',
                'code',
                'municipality_id',
            ]);

        if ($item) {
            $item->uiid = Campus::getUiid($item->id);
            $item->created_by = User::find($item->created_by_id, ['email']);
            $item->updated_by = User::find($item->updated_by_id, ['email']);
            $item->municipality = Municipality::find($item->municipality_id, ['name', 'state_id']);
            $item->municipality->state = State::find($item->municipality->state_id, ['name']);
        }

        return $item;
    }
}
