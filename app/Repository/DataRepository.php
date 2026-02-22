<?php

namespace App\Repository;

use Illuminate\Http\Request;

class DataRepository {
    public function getRegions(Request $request)
    {
        $regions = \DB::table('regions')->get();
        $html = '';
        foreach ($regions as $region) {
            $html .= "<option value='{$region->id}'". ($region->id == $request->selected ? ' selected':'') .">{$region->name}</option>";
        }
        return $html;
    }

    public function getDistricts(Request $request)
    {
        $districts = \DB::table('districts')->where('region_id', $request->region_id)->get();
        $html = '';
        foreach ($districts as $district) {
            $html .= "<option value='{$district->id}'". ($district->id == $request->selected ? ' selected':'') .">{$district->name}</option>";
        }
        return $html;
    }

    public function getObjectCategory(Request $request) {
        $name = 'name_'.session('locale', 'ru');
        return json_decode(json_encode(\DB::table('object_category')->select('id', "$name as name")->where('id_object_type', $request->object_type)->get()),true);
    }

    public function getObjectParams(Request $request) {
        $no_filter = ['area','floors_qty','living_complex','land_area','beds_qty'];
        $lang = session('locale', 'ru');
        $name = 'name_'.$lang;
        $params = \DB::table('object_params')->select("$name as name", 'field_name', 'field_values')->where('type_id', $request->object_type)->where('category_id', $request->object_cat)->whereNotIn('field_name', $no_filter)->get();
        foreach ($params as $param) {
            $param->field_values = json_decode($param->field_values,true);
            if($param->field_values) {
                foreach ($param->field_values as $k => $field_value) {
                    $param->field_values[$k] = trans($field_value,[],$lang);
                }
            }
        }
        return json_decode(json_encode($params),true);
    }

    public function getProperty(Request $request)
    {

        $property = explode(',',\DB::table('object_category')->where('id', $request->obj)->first()->property);
        $object_pty = \DB::table('object_pty')->whereIn('id', $property)->get();
        $html = '<select class="form-select select2" name="main_pty" style="width: 100%;"><option value="">*** Выбрать ***</option>';
        foreach ($object_pty as $o) {
            $html .= "<option value='{$o->id}'". ($o->id == $request->selected ? ' selected':'') .">{$o->name}</option>";
        }
        $html .= '</select>';
        return $html;
    }

    public function postSortFilter(Request $request) {
        dd($request->all());
    }

    public function getFilterBar(Request $request) {

//        if($request->object_pty != '') return;

        $lang = session('locale', 'ru');
        $name = 'name_'.$lang;

        if(!in_array($request->object_pty, ['','undefined','rooms_qty','repairment'])) return;

        if($request->object_category) {
            $pty = \DB::table('object_pty')
                ->selectRaw("id, '{$request->type}' as type, '{$request->object_type}' as object_type, '{$request->object_category}' as object_category, id as object_pty, name")->whereRaw("FIND_IN_SET(id, (SELECT property FROM object_category WHERE id = {$request->object_category}))")->get();
            if(count($pty)>0) return $pty;

            $no_filter = ['area','floors_qty','living_complex','land_area','beds_qty'];

            $params = \DB::table('object_params')->select("$name as name", 'field_name', 'field_values')->where('type_id', $request->object_type)->where('category_id', $request->object_category)->whereNotIn('field_name', $no_filter)->get();
//            $params = \DB::table('object_params')->selectRaw("id, '{$request->type}' as type, '{$request->object_type}' as object_type, '{$request->object_category}' as object_category, id as object_pty, $name as name")->where('type_id', $request->object_type)->where('category_id', $request->object_category)->whereNotIn('field_name', $no_filter)->get();
//            return $params;

            foreach ($params as $param) {
                $param->field_values = json_decode($param->field_values,true);
                if($param->field_values) {
                    foreach ($param->field_values as $k => $field_value) {
                        $param->field_values[$k] = $field_value;
                    }
                }
            }
            $step = 0;
            switch($request->object_pty){
                case 'rooms_qty': $step = 2;
                break;
                case 'repairment': $step = 3;
                break;
                case 'building_material': $step = 4;
                break;
            }

            $q = [];
            if(!isset($params[$step]->field_values)) return;
            foreach ($params[$step]->field_values as $k => $p) {
                $q[] = [
                    "id" => $k,
                    "type" => $request->type,
                    "object_type" => $request->object_type,
                    "object_category" => $request->object_category,
                    "object_pty" => $params[$step]->field_name,
                    "name" => trans($p,[],$lang)
                ];
            }
            return $q;

/*
            {
                "id": 1,
                "type": "sell",
                "object_type": "1",
                "object_category": "1",
                "object_pty": 1,
                "name": "Количество комнат"
            }
*/

        }
        if($request->object_type){
            return \DB::table('object_category')
                ->selectRaw("id,'{$request->type}' as type,'{$request->object_type}' as object_type,id as object_category,'' as object_pty, name")
                ->where('id_object_type',$request->object_type)->get();
        }

        $filters = \DB::table('filters')->selectRaw("id, type, IFNULL(object_type,'') as object_type, IFNULL(object_category,'') as object_category, IFNULL(object_pty,'') as object_pty, CONCAT(icon,' ',$name) as name")->whereNull('parent_id')->get();

        return json_decode(json_encode($filters),true);

    }

    public function getSearchHotels() {
        $query = request()->get('term');
        $data = \DB::table('tb_hotels')->where('name', 'like', '%'.$query.'%')->limit(15)->get();
        return response()->json($data);
    }

}
