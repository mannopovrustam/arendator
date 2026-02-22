<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    public function index() {
        $data = [];
        $data['user'] = auth()->user();

        return view('listing.index',$data);
    }

    public function create(Request $request)
    {
        if (isset($request->id) && $request->id)
            $data = \DB::table('tb_listings')->first();
//        else $data = tableName('tb_listings');
        $data = null;
        return view('listing.create', ['data' => $data]);
    }

    public function getCreateHotels(Request $request) {
        $data['hotels'] = \DB::table('tb_hotels')->first();
        return view('listing.create-hotels', $data);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->except(['_token', 'images', 'data_id', 'price_type', 'price']);
            $data['is_rent'] = in_array('rent', $request->input('price_type')) ? 1 : 0;
            $data['is_sell'] = in_array('sell', $request->input('price_type')) ? 1 : 0;
            $data['rent_type'] = in_array('rent', $request->input('price_type')) && isset($request->input('price')['rent_type']) ? $request->input('price')['rent_type'] : null;
            $data['rent_price'] = in_array('rent', $request->input('price_type')) && isset($request->input('price')['rent']) ? $request->input('price')['rent'] : null;
            $data['sell_type'] = in_array('sell', $request->input('price_type')) && isset($request->input('price')['sell_type']) ? $request->input('price')['sell_type'] : null;
            $data['sell_price'] = in_array('sell', $request->input('price_type')) && isset($request->input('price')['sell']) ? $request->input('price')['sell'] : null;

            if ($request->images) {
                foreach (explode(',', $request->images) as $img) {
                    // Move the image from the temporary folder to the permanent folder
                    $img = str_replace(asset(''), '', $img);
                    $img = public_path($img);
                    $img_name = 'image-' . Str::random(10) . '.png';
                    $img_path = 'images/permanent/' . $img_name;
                    if (file_exists($img)) {
                        rename($img, public_path($img_path));
                    }
                    $images[] = $img_path;
                }
                $data['images'] = implode(",", $images);
            }
            $listing_header = \DB::table('tb_listings_parent')->insertGetId([
                'name' => $data['name'],
                'type_id' => $data['type_id'],
                'category_id' => $data['category_id'],
                'main_pty' => $data['main_pty'] ?? null,
                'region_id' => $data['region_id'],
                'district_id' => $data['district_id'],
                'address' => $data['address'],
                'geolocation' => $data['geolocation'],
                'description' => $data['description']
            ]);
            if($listing_header) {
                $data['parent_id'] = $listing_header;
                unset($data['name']);
                \DB::table('tb_listings')->insertGetId($data);
            }

//            ClickhouseService::listing($listings);

            return back()->withMessage('Объект успешно добавлен!')->withColor('success');
        } catch (\Exception $e) {
            return back()->withMessage('Error: ' . $e->getMessage())->withColor('danger');
        }
    }

    public function getData()
    {
        $filter = request()->all();
        $lang = session('local','ru');

        $q = \DB::table('tb_listings as l')
            ->join('regions as r', 'l.region_id', '=', 'r.id')
            ->join('districts as d', 'l.district_id', '=', 'd.id')
            ->select('l.id','l.region_id','l.district_id','l.address','l.images','l.name','l.type_id','l.sell_price','l.sell_type','l.rent_price','l.rent_type','l.area','l.floor','l.floors_qty','r.name as region_name', 'd.name as district_name');

        $filter_txt = [];
        if (isset($filter['deal_type']) && $deal_type = $filter['deal_type']) {
            if (isset($filter['price_type']) && $price_type = $filter['price_type']) $q->where('l.'.$deal_type.'_type', $price_type);

            $filter_txt['deal_type'] = trans('deal_type');
            $q->where(function($q) use ($deal_type){ $q->orWhere('l.is_'.$deal_type, 1);  });
            $q->where(function ($q) use ($filter,$deal_type){$q->orWhereBetween('l.'.$deal_type.'_price', [$filter['filter_price_min'] ?? 0, $filter['filter_price_max'] ?? 100000000000]);});
        } else {
            $q->where(function ($q) use ($filter) {foreach (['rent', 'sell'] as $item) $q->orWhereBetween('l.'.$item . '_price', [$filter['filter_price_min'] ?? 0, $filter['filter_price_max'] ?? 100000000000]);});
        }

        if (isset($filter['object_type'])) $q->where('l.type_id', $filter['object_type']);
        if (isset($filter['object_category'])) $q->where('l.category_id', $filter['object_category']);
        $q->whereBetween('l.area', [$filter['filter_area_min'] ?? 0, $filter['filter_area_max'] ?? 100000000000]);
        $q->whereBetween('l.floor', [$filter['filter_floor_min'] ?? 0, $filter['filter_floor_max'] ?? 100]);
        if (isset($filter['rooms_qty']) && $filter['rooms_qty']) $q->whereIn('l.rooms_qty', $filter['rooms_qty']);
        if (isset($filter['repairment']) && $filter['repairment']) $q->whereIn('l.repairment', $filter['repairment']);
        if (isset($filter['building_material']) && $filter['building_material']) $q->whereIn('l.building_material', $filter['building_material']);

        if (isset($filter['object_pty'])) {
            $objectPty = \DB::table('object_pty as op')->where('id', $filter['object_pty'])
                ->join('tb_listing_object_pty as lop', 'op.id', '=', 'lop.id_object_type')
                ->select('lop.id_listing')->get();
            $q->whereIn('l.id', $objectPty->pluck('id_listing')->toArray());
        }


//        return $this->toRawSql($q);
        $data = $q->limit(8)->get();
        return view('listing.data', ['data' => $data]);

    }

    function toRawSql($query)
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();

        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'{$binding}'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }

    public function getDataTable()
    {
        $request = request(); // Получаем параметры от DataTables
        // Получаем параметры пагинации
        $collection = $listings = \DB::table('tb_listings as l')
            ->join('object_category as oc', 'l.category_id', '=', 'oc.id')
            ->join('object_types as ot', 'l.type_id', '=', 'ot.id')
            ->join('regions as r', 'l.region_id', '=', 'r.id')
            ->join('districts as d', 'l.district_id', '=', 'd.id')
            ->select('l.id','l.name','l.address','l.rooms_qty','oc.name as category_name', 'ot.name as type_name', 'r.name as region_name', 'd.name as district_name');

        // Отдаем в DataTables
        return DataTables::of($collection)
            ->setRowAttr(['data-id' => function ($data) {return $data->id;}])
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getMobileProduct($id) {
        $listing = \DB::table('tb_listings as l')
            ->join('object_category as oc', 'l.category_id', '=', 'oc.id')
            ->join('object_types as ot', 'l.type_id', '=', 'ot.id')
            ->join('regions as r', 'l.region_id', '=', 'r.id')
            ->join('districts as d', 'l.district_id', '=', 'd.id')
            ->select('l.*','oc.name as category_name', 'ot.name as type_name', 'r.name as region_name', 'd.name as district_name')
            ->where('l.id', $id)->first();
        $listing->params = $this->objectParams($listing);
        return view('listing.single-mobile', ['listing' => $listing]);
    }

    public function getSingleProduct($id) {
        if (!$id) return redirect()->route('listing.index');
        app()->setLocale(session('locale', 'ru'));

        $listing = \DB::table('tb_listings as l')
            ->join('object_category as oc', 'l.category_id', '=', 'oc.id')
            ->join('object_types as ot', 'l.type_id', '=', 'ot.id')
            ->join('regions as r', 'l.region_id', '=', 'r.id')
            ->join('districts as d', 'l.district_id', '=', 'd.id')
            ->join('sp_currencies as c', 'c.id', '=', 'l.id_currency')
            ->select('l.*','oc.name as category_name', 'ot.name as type_name', 'r.name as region_name', 'd.name as district_name', 'c.name as currency_name')
            ->where('l.id', $id)->first();

        $listing->params = $this->objectParams($listing);

        $data = [];
        $data['user'] = auth()->user();
        $data['listing'] = $listing;

        return view('listing.quickview', $data);
    }

    public function objectParams($listing)
    {
        $type_id = $listing->type_id;
        $category_id = $listing->category_id;
        $propertyTypes = \DB::table('object_params')->where('type_id', $type_id)->where('category_id', $category_id)->get();
        if (count($propertyTypes) == 0) {
            $propertyTypes = \DB::table('object_params')->where('type_id', $type_id)->get();
        }
        $html = "";
        foreach ($propertyTypes as $k => $pt) {
            $field_name = $pt->field_name;
            $html .= '<div class="product-item__info" style="display: flex; padding: 1rem; align-items: end;'. ($k != 0 ? 'border-top:1px solid #ccc;' :'') .'">';
            $html .= '<div class="product-item__info-key" style="width: 50%; font-weight: 400; font-size:0.8rem">';
            $html .= $pt->name;
            $html .= '</div>';
            $html .= '<div class="product-item__info-value" style="width: 50%; font-weight: 600">';

            if ($pt->field_values) {
                $field_values = json_decode($pt->field_values, true);
                $html .= $field_values[$listing->$field_name];
            } else
                $html .= $listing->$field_name . ' ' . $pt->field_unit;
//            $html .= $listing->$field_name;
            $html .= '</div>';
            $html .= '</div>';
        }

        return $html;
    }

    public function getObjectParams() {
        $request = request();
        $type_id = $request->type_id;
        $category_id = $request->category_id;
        $propertyTypes = \DB::table('object_params')->where('type_id', $type_id)->where('category_id', $category_id)->get();
        if (count($propertyTypes) == 0) {
            $propertyTypes = \DB::table('object_params')->where('type_id', $type_id)->get();
        }
        $html = "<table class='table'><script>
                    $('.only_digit_int').on('input', function () {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    });
                    $('.only_digit').on('input', function () {
                        this.value = this.value.replace(/[^0-9.]/g, '');
                        if (!/^\d*\.?\d*$/.test(this.value)) {
                            this.value = this.value.replace(/[^\d.]/g, '');
                        }
                    });
                </script>";

        foreach ($propertyTypes as $pt) {
            $html .= "<tr>";
            $html .= "<td><label for='name'>{$pt->name}</label></td>";
            $html .= "<td>";
            if ($pt->field_values) {
                $html .= "<div class='form-group d-flex flex-wrap'>";
                foreach (json_decode($pt->field_values) as $val => $txt) {
                    $html .= "<label class='payment-methods__item-header btn-secondary mr-2 mb-1' for='object_pty_type_{$val}'><span class='payment-methods__item-radio input-radio'><span class='input-radio__body'><input class='input-radio__input' name='{$pt->field_name}' id='object_pty_type_{$val}' value='{$val}' type='radio'> <span class='input-radio__circle'></span> </span></span><span class='payment-methods__item-title'>".($pt->field_name == 'rooms_qty' ? __($txt) : $txt)."</span></label>";
                }
                $html .= "</div>";
            }
            if ($pt->field_values == null && $pt->field_type == 'int') {
                $html .= "<div class='form-group'>";
                $html .= "<input type='text' class='form-control only_digit_int' name='{$pt->field_name}' placeholder='{$pt->name}'>";
                $html .= "</div>";
            }
            if ($pt->field_values == null && $pt->field_type == 'float') {
                $html .= "<div class='form-group'>";
                $html .= "<input type='text' class='form-control only_digit' name='{$pt->field_name}' placeholder='{$pt->name}'>";
                $html .= "</div>";
            }
            $html .= "</td>";
        }
        $html .= "</table>";

        return $html;
    }

    public function postUploadImage(Request $request) {
        try {
            // Extract the Base64 data (remove the data URI prefix, e.g., "data:image/png;base64,")
            $base64Image = $request->input('image');
            if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid Base64 image data',
                ], 400);
            }

            $imageExtension = $matches[1]; // e.g., "png", "jpeg"
            $base64Data = substr($base64Image, strpos($base64Image, ',') + 1); // The actual Base64 string

            // Decode Base64 data
            $imageData = base64_decode($base64Data);
            if ($imageData === false) {
                return response()->json(['success' => false,'error' => 'Failed to decode Base64 data',], 400);
            }

            // Generate a unique filename
            $fileName = 'image-' . Str::random(10) . '.' . $imageExtension;
            $filePath = 'images/tmp/' . $fileName;

            // Save the image to the public_folder('images/tmp') not storage
            $isImageSaved = file_put_contents(public_path($filePath), $imageData);
            if (!$isImageSaved) {
                return response()->json(['success' => false,'error' => 'Failed to save image',], 500);
            }

            $imageUrl = asset($filePath);
            return response()->json(['success' => true,'imageUrl' => $imageUrl,]);
        } catch (\Exception $e) {
            return response()->json(['success' => false,'error' => 'Failed to save image: ' . $e->getMessage(),], 500);
        }

    }

    public function postDeleteImage(Request $request) {
        $imageUrl = $request->input('image');
        $imagePath = public_path(str_replace(asset(''), '', $imageUrl));
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        return response()->json(['success' => true]);
    }
}
