<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BookingController
{

    // booking status enum('new', 'registered', 'rejected', 'closed', 'accepted')
    CONST BOOKING_STATUS = [
        'new' => 'Yangi',
        'registered' => 'Ro‘yxatga olingan',
        'rejected' => 'Rad etilgan',
        'closed' => 'Yopilgan',
        'accepted' => 'Qabul qilingan'
    ];

    public function __construct() {

    }

    public function index() {
        if (!\Auth::check()) return redirect('/')->with('status', 'error')->with('message', __('core.urnotlogin'));

        $lang = \Session::get('lang', 'ru');
        $grp_id = \Session::get('gid', \Auth::user()->group_id) * 1;
        $this->data['rhsearch'] = '';
        $this->data['tableTH'] = '<th>Booking ID</th><th>E\'LON </th><th>FIO</th><th>MUDDAT </th><th>KELIB TUSHGAN SANA</th><th>STATUS</th>';

        $this->data['columnsTH'] = "{data:'id',name:'b.id',sClass:'dt-center'},{data:'listing_name',name:'l.name'},{data:'staffname',name:'b.staffname'},{data:'period'},{data:'created_at',name:'b.created_at'},{data:'status',sClass:'dt-center'}";

        $this->data['exportColumns'] = ',exportOptions:{columns: [0,1,2,3,4]}';
        return view('booking.index', $this->data);
    }

    public function getData(Request $request) {

        $d = \DB::table('tb_bookings as b')
            ->join('tb_listings as l', 'b.listing_id', '=', 'l.id')
            ->select('b.id', 'b.staffname', 'b.date_from', 'b.date_to', 'b.created_at', 'b.status', 'b.one_id', 'l.id_cad', 'l.address as listing_name')
            ->orderBy('b.id', 'desc');

        return DataTables::of($d)
            ->rawColumns(['status','id'])
            ->setRowData(['data-id' => function($row) {return base64_encode($row->id);},'data-name'=>function($row) {return $row->staffname;},'date-one_id'=>function($row) {return $row->one_id ?? '';}])
            ->editColumn('id', function($row) {
                return $row->id . ($row->one_id && $row->id_cad ? '<img src="/img/oneid.svg" alt="link" style="width:20px; height:20px; margin-left:5px; cursor:pointer">':'');
            })
            ->editColumn('status', function($row) {
                $statusLabel = self::BOOKING_STATUS[$row->status] ?? $row->status;
                switch ($row->status) {
                    case 'new':
                        $class = 'badge bg-primary';
                        break;
                    case 'registered':
                        $class = 'badge bg-info';
                        break;
                    case 'accepted':
                        $class = 'badge bg-success';
                        break;
                    case 'rejected':
                        $class = 'badge bg-danger';
                        break;
                    case 'closed':
                        $class = 'badge bg-secondary';
                        break;
                    default:
                        $class = 'badge bg-light text-dark';
                }
                return '<span class="'.$class.'">'.$statusLabel.'</span>';
            })
            ->editColumn('period', function($row) {
                return date('d.m.Y', strtotime($row->date_from)).' - '.date('d.m.Y', strtotime($row->date_to));
            })
            ->editColumn('created_at', function($row) {
                return date('d.m.Y H:i', strtotime($row->created_at));
            })
            ->make(true);
    }

    public function store(Request $request) {


        $message = [
            'doc_number.required' => 'Hujjat raqami talab qilinadi.',
            'pinfl.required' => 'PINFL talab qilinadi.',
            'dtb.required' => 'Tug‘ilgan sana talab qilinadi.',
            'staffname.required' => 'To‘liq ism-sharif talab qilinadi.',
            'date_from.required' => 'Boshlanish sanasi talab qilinadi.',
            'date_to.required' => 'Tugash sanasi talab qilinadi.',
            'contact_phone.required' => 'Aloqa telefon raqami talab qilinadi.',
            'comments.min' => 'Izohlar kamida 10 ta belgidan iborat bo‘lishi kerak.',
            'listing_id.required' => 'Ro‘yxat identifikatori talab qilinadi.',
            'listing_id.exists' => 'Tanlangan ro‘yxat identifikatori yaroqsiz.',
        ];
        $request->validate([
            'date_from' => 'required|date|after_or_equal:today',
            'date_to' => 'required|date|after:date_from',
            'contact_phone' => 'required|string',
            'comments' => 'nullable|string|min:10',
            'listing_id' => 'required|integer|exists:tb_listings,id',
        ], $message);

        $data = $request->except('_token');
        $user = auth()->user() ?? null;
        $data['entry_by'] = $user ? $user->id : null;
        $data['one_id'] = $user ? $user->one_id : null;
        
        if($id = \DB::table('tb_bookings')->insertGetId($data)) return response()->json(['status'=> 'success', 'message' => 'Bron qilish muvaffaqiyatli amalga oshirildi. Bron raqamingiz: '.$id], 200);
        else return response()->json(['status'=> 'error', 'message' => 'Bron qilish amalga oshmadi.'], 500);


    }

    public function getSetPropiska() {
        $request = request();
        $message = [
            'booking_id.required' => 'Bron identifikatori talab qilinadi.',
            'booking_id.exists' => 'Tanlangan bron identifikatori yaroqsiz.',
            'date_from.required' => 'Boshlanish sanasi talab qilinadi.',
            'date_to.required' => 'Tugash sanasi talab qilinadi.',
        ];
        $request->validate([
            'booking_id' => 'required|exists:tb_bookings,id',
            'date_from' => 'required|date|after_or_equal:'.date('Y-m-d'),
            'date_to' => 'required|date|after_or_equal:dtVisitOn',
        ], $message);

        $booking = \DB::table('tb_bookings as b')
            ->join('tb_listings as l', 'b.listing_id', '=', 'l.id')
            ->select('b.*', 'l.id_cad as cadastr')
            ->where('b.id',$request->booking_id)->first();
        if(!$booking->cadastr) return response()->json(['status'=> 'error', 'message' => 'Bu bron uchun kadastr raqami mavjud emas. Iltimos, avval kadastr raqamini kiriting.'], 500);

        $client = \DB::table('tb_users')->where('id',$booking->entry_by)->whereNotNull('one_id')->first();
        if (!$client) return response()->json(['status'=> 'error', 'message' => 'Mijoz OneID orqali ro‘yxatdan o‘tmagan.'], 500);

        try {
            $detail = [
                'id_citizen' => 173,
                'pinfl' => $client->pin,
                'surname' => $client->surname,
                'firstname' => $client->firstname,
                'lastname' => $client->lastname,
                'dtBirth' => date('Y-m-d', strtotime($client->birth_date)),
                'sex' => $client->gd == 1 ? 'M' : 'W',
                'pspDate' => date('Y-m-d', strtotime($client->pport_expr_date)),
                'pspIssuedBy' => $client->pport_issue_place,
                'psp' => $client->pspNumb,
                'dtVisitOn' => $request->date_from,
                'dtVisitOff' => $request->date_to,
                'id_cad' => $booking->cadastr,
                'id_user' => $booking->entry_by,
                'entry_by' => auth()->id(),
                'comment' => $request->comment ?? '',
                'wdays' => (strtotime($request->date_to) - strtotime($request->date_from)) / (60 * 60 * 24),
            ];

            if (\DB::table('tb_listok')->where('id_cad',$booking->cadastr)->where('pinfl',$client->pin)->exists())
                return response()->json(['status'=> 'error', 'message' => 'Ushbu Shaxs tanlangan kadastr bo‘yicha ro‘yxatga olingan.'], 500);

            $listok = \DB::table('tb_listok')->insertGetId($detail);

            return response()->json(['status'=> 'success', 'message' => 'Ro‘yxatga olish muvaffaqiyatli amalga oshirildi. Listok raqami: '.$listok], 200);
        }catch (\Exception $e){
            return response()->json(['status'=> 'error', 'message' => $e->getMessage().'; line: '. $e->getLine()], 500);
        }



        $update = \DB::table('tb_bookings')->where('id', $data['booking_id'])->update(['propiska_code' => $data['propiska_code']]);
        if($update) return response()->json(['status'=> 'success', 'message' => 'Ro‘yxatga olish muvaffaqiyatli amalga oshirildi.'], 200);
        else return response()->json(['status'=> 'error', 'message' => 'Ro‘yxatga olish amalga oshmadi.'], 500);


    }

}
