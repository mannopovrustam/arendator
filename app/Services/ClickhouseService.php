<?php

namespace App\Services;
use ClickHouseDB\Client;
use Illuminate\Support\Facades\Cache;

class ClickhouseService {
    public static function listing($listings){
        $clickhouse = app(Client::class);
        // max size of variables

        $version = time();
        $data = [];
        foreach ($listings as $listing) {
            $data[] = [
                'id' => $listing->id,
                'name' => $listing->name,
                'type_id' => $listing->type_id,
                'type_name' => $listing->type_name,
                'category_id' => $listing->category_id,
                'category_name' => $listing->category_name,
                'objs_properties' => $listing->objs_properties,
                'region_id' => $listing->region_id,
                'region_name' => $listing->region_name,
                'district_id' => $listing->district_id,
                'district_name' => $listing->district_name,
                'address' => $listing->address,
                'geolocation' => $listing->geolocation,
                'rooms_qty' => $listing->rooms_qty,
                'area' => $listing->area,
                'floor' => $listing->floor,
                'floors_qty' => $listing->floors_qty,
                'repairment' => $listing->repairment,
                'building_material' => $listing->building_material,
                'living_complex' => $listing->living_complex,
                'land_area' => $listing->land_area,
                'beds_qty' => $listing->beds_qty,
                'is_rent' => $listing->is_rent,
                'is_sell' => $listing->is_sell,
                'rent_type' => $listing->rent_type,
                'rent_price' => $listing->rent_price,
                'sell_type' => $listing->sell_type,
                'sell_price' => $listing->sell_price,
                'description' => $listing->description,
                'status' => $listing->status,
                'images' => $listing->images,
                'version' => $version,
                'created_dt' => $listing->created_dt,
                'created_at' => $listing->created_at,
            ];
        }

        \Log::info('Inserting data to clickhouse tb_listings table...');

//        return $data;

        $clickhouse->insert('tb_listings', $data);

        \Log::info('Data inserted to clickhouse tb_listings table!');
        return count($data);

    }

    public static function optimize($table,$partition = null) {
        $clickhouse = app(Client::class);
        $clickhouse->write("OPTIMIZE TABLE $table ".($partition ? "PARTITION '$partition' FINAL" : "FINAL"));
    }

}
