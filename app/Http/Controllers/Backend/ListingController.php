<?php

namespace App\Http\Controllers\Backend;

use App\Services\ClickhouseService;
use ClickHouseDB\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ListingController
{

    protected $path;

    public function __construct() {
        $this->path = 'backend.' . Str::singular(\Request::segment(2));
    }

    public function index()
    {
        return view($this->path . '.index');
    }

    public function getData()
    {
        return view($this->path . '.data');
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

    /**
     * Получаем общее количество записей для DataTables
     */

    public function create(Request $request)
    {
        if (isset($request->id) && $request->id)
            $data = \DB::table('tb_listings')->first();
//        else $data = tableName('tb_listings');
        $data = [];
        $data['cadastrs'] = \DB::table('tb_cadastrs')->where('id_user',auth()->id())->where('st','1')->get();
        return view('backend.listing.create', $data);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->except(['_token', 'images', 'data_id', 'price_type', 'price', 'cadastr']);
            $data['is_rent'] = in_array('rent', $request->input('price_type')) ? 1 : 0;
            $data['is_sell'] = in_array('sell', $request->input('price_type')) ? 1 : 0;
            $data['rent_type'] = in_array('rent', $request->input('price_type')) && isset($request->input('price')['rent_type']) ? $request->input('price')['rent_type'] : null;
            $data['rent_price'] = in_array('rent', $request->input('price_type')) && isset($request->input('price')['rent']) ? $request->input('price')['rent'] : null;
            $data['sell_type'] = in_array('sell', $request->input('price_type')) && isset($request->input('price')['sell_type']) ? $request->input('price')['sell_type'] : null;
            $data['sell_price'] = in_array('sell', $request->input('price_type')) && isset($request->input('price')['sell']) ? $request->input('price')['sell'] : null;

            $data['id_cad'] = null;
            if (isset($request->cadastr) && $request->cadastr) {
                $data['id_cad'] = $request->cadastr;
            }
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
                'type_id' => $data['type_id'],
                'category_id' => $data['category_id'],
                'main_pty' => $data['main_pty'] ?? null,
                'region_id' => $data['region_id'],
                'district_id' => $data['district_id'],
                'address' => $data['address'],
                'id_cad' => $data['id_cad'],
                'geolocation' => $data['geolocation'],
                'description' => $data['description']
            ]);
            if($listing_header) {
                $data['parent_id'] = $listing_header;
                \DB::table('tb_listings')->insertGetId($data);
            }

/*            $listings = \DB::table('tb_listings as l')
                ->join('object_category as oc', 'l.category_id', '=', 'oc.id')
                ->join('object_types as ot', 'l.type_id', '=', 'ot.id')
                ->join('regions as r', 'l.region_id', '=', 'r.id')
                ->join('districts as d', 'l.district_id', '=', 'd.id')
                ->where('l.id', $new_listing)->first();*/

//            ClickhouseService::listing($listings);

            return back()->withMessage('Объект успешно добавлен!')->withColor('success');
        } catch (\Exception $e) {
            return back()->withMessage('Error: ' . $e->getMessage())->withColor('danger');
        }
    }

    public function getObjectParams(): string
    {
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
                        // if the value is not digit then notify the user
                        if (!/^\d*\.?\d*$/.test(this.value)) {
                            // Filter non-digits from input value.
                            this.value = this.value.replace(/[^\d.]/g, '');
                        }
                    });
                </script>";

        foreach ($propertyTypes as $pt) {
            $html .= "<tr>";
            $html .= "<td><label for='name'>{$pt->name}</label></td>";
            $html .= "<td>";
            if ($pt->field_values) {
                $html .= "<div class='form-group'>";
                foreach (json_decode($pt->field_values) as $val => $txt) {
                    $html .= "<input type='radio' class='btn-check' name='{$pt->field_name}' id='object_pty_type_{$val}' autocomplete='off' value='{$val}'>";
                    $html .= "<label class='btn btn-outline-primary' for='object_pty_type_{$val}'>{$txt}</label>";
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

    public function postUploadImage(Request $request)
    {
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
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to decode Base64 data',
                ], 400);
            }

            // Generate a unique filename
            $fileName = 'image-' . Str::random(10) . '.' . $imageExtension;
            $filePath = 'images/tmp/' . $fileName;

            // Save the image to the public_folder('images/tmp') not storage
            $isImageSaved = file_put_contents(public_path($filePath), $imageData);
            if (!$isImageSaved) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to save image',
                ], 500);
            }

            $imageUrl = asset($filePath);
            return response()->json([
                'success' => true,
                'imageUrl' => $imageUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to save image: ' . $e->getMessage(),
            ], 500);
        }

    }

    public function postDeleteImage(Request $request)
    {
        $imageUrl = $request->input('image');
        $imagePath = public_path(str_replace(asset(''), '', $imageUrl));
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        return response()->json([
            'success' => true,
        ]);
    }

    public function getToClick() {
        $listings = \DB::table('tb_listings as l')
            ->join('object_category as oc', 'l.category_id', '=', 'oc.id')
            ->join('object_types as ot', 'l.type_id', '=', 'ot.id')
            ->join('regions as r', 'l.region_id', '=', 'r.id')
            ->join('districts as d', 'l.district_id', '=', 'd.id')
            ->select('l.*', 'oc.name as category_name', 'ot.name as type_name', 'r.name as region_name', 'd.name as district_name')
            ->get();

        return ClickhouseService::listing($listings);
    }

    public function getClTest() {
        $clickhouse = app(Client::class);
        // get all listings limit 100
        return $clickhouse->select('SELECT * FROM tb_listings LIMIT 1,3')->rows();
    }

}
