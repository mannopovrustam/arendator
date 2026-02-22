<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function getRent($url) {
        $data = \DB::table('tb_listings as l')
            ->join('regions as r', 'l.region_id', '=', 'r.id')
            ->join('districts as d', 'l.district_id', '=', 'd.id')
            ->select('l.id','l.region_id','l.district_id','l.address','l.images','l.name','l.sell_price','l.sell_type','l.rent_price','l.rent_type','l.area','l.floor','l.floors_qty','r.name as region_name', 'd.name as district_name')
            ->where('l.is_rent', '1')
            ->limit(8)->get();
        return view('listing.data', ['data' => $data]);
    }

    public function getSell($url) {

        $data = \DB::table('tb_listings as l')
            ->join('regions as r', 'l.region_id', '=', 'r.id')
            ->join('districts as d', 'l.district_id', '=', 'd.id')
            ->select('l.id','l.region_id','l.district_id','l.address','l.images','l.name','l.sell_price','l.sell_type','l.rent_price','l.rent_type','l.area','l.floor','l.floors_qty','r.name as region_name', 'd.name as district_name')
            ->where('l.is_sell', '1')
            ->limit(8)->get();
        return view('listing.data', ['data' => $data]);
    }
}
