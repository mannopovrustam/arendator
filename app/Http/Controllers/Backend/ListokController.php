<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ListokController extends Controller
{
    public $path;

    public function __construct() {
        $this->path = 'backend.listok';
    }

    public function index()
    {
        return view($this->path.'.index');
    }

    public function getData()
    {
        $query = DB::table('tb_listok as l')->selectRaw("concat(l.surname,' ',l.firstname,' ',l.lastname) as fio,l.dtBirth,l.psp,l.dtVisitOn,l.dtVisitOff,c.cad_number")
            ->join('tb_cadastrs as c','c.id','=','l.id_cad');


        return DataTables::of($query)
            ->editColumn('dtBirth', fn($row) => date('d.m.Y', strtotime($row->dtBirth)))
            ->editColumn('dtVisitOn', fn($row) => date('d.m.Y', strtotime($row->dtVisitOn)))
            ->editColumn('dtVisitOff', fn($row) => date('d.m.Y', strtotime($row->dtVisitOff)))
            ->make(true);
    }

    public function create()
    {
        $data = ['data'];
        $data['cadastrs'] = \DB::table('tb_cadastrs')->where('id_user',auth()->id())->where('st','1')->get();
        return view('backend.listok.create',$data);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $values = $request->except(['_token', 'data_id']);
        $messages = [
            'pinfl.required' => 'Iltimos, PINFL topilmadi!',
            'surname.required' => 'Iltimos, Familiya topilmadi!',
            'firstname.required' => 'Iltimos, Ism topilmadi!',
            'lastname.required' => 'Iltimos, O\'tasining ismi topilmadi!',
            'dtBirth.required' => 'Iltimos, Tug‘ilgan sana topilmadi!',
            'sex.required' => 'Iltimos, Jinsi topilmadi!',
            'pspDate.required' => 'Iltimos, Pasport bеrilgan sana topilmadi!',
            'pspIssuedBy.required' => 'Iltimos, Pasport bеrilgan joy topilmadi!',
            'passport.required' => 'Iltimos, Pasport ma’lumotlari topilmadi!',
            'dtVisitOn.required' => 'Iltimos, Kеlish sanasini kiriting!',
            'dtVisitOff.required' => 'Iltimos, Kеtish sanasini kiriting!',
            'dtVisitOff.after_or_equal' => 'Kеtish sanasi kеlish sanasidan avval bo‘lishi lozim!',
            'dtVisitOn.after_or_equal' => 'Kеlish sanasi bugungi kundan avval bo‘lishi mumkin emas!',
            'cadastr.required' => 'Iltimos, Kadastrni tanlang!',
        ];

        if ($user->pin == $request->pinfl) return back()->withMessage('Siz o\'zingiz uchun shartnoma tuzishingiz mumkin emas!')->withColor('error');

        $validator = Validator::make($values, [
            'pinfl' => 'required|string|max:14|min:14',
            'surname' => 'required|string',
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'dtBirth' => 'required|date',
            'sex' => 'required|string',
            'pspDate' => 'required|date',
            'pspIssuedBy' => 'required|string',
            'passport' => 'required|string',
            'dtVisitOn' => 'required|date|after_or_equal:'.date('Y-m-d'),
            'dtVisitOff' => 'required|date|after_or_equal:dtVisitOn',
            'cadastr' => 'required|integer',
            'pinfl.unique' => 'user_pinfl|unique:tb_listok,pinfl',
        ], $messages);

        if ($validator->fails()) return back()->withErrors($validator)->withInput();

        try {
            $detail = [
                'id_citizen' => 173,
                'pinfl' => $request->pinfl,
                'surname' => $request->surname,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'dtBirth' => date('Y-m-d', strtotime($request->dtBirth)),
                'sex' => $request->sex,
                'pspDate' => date('Y-m-d', strtotime($request->pspDate)),
                'pspIssuedBy' => $request->pspIssuedBy,
                'psp' => $request->passport,
                'dtVisitOn' => $request->dtVisitOn,
                'dtVisitOff' => $request->dtVisitOff,
                'id_cad' => $request->cadastr,
                'entry_by' => auth()->id(),
                'wdays' => $request->wdays
            ];

            $listok = DB::table('tb_listok')->insertGetId($detail);

            return back()->withMessage($listok.' raqamli shartnoma muvaffaqiyatli yaratildi')->withColor('success');
        }catch (Exception $e){
            return back()->withMessage($e->getMessage())->withColor('error');
        }

    }

    public function update(Request $request, $id)
    {
        $base64Pdf = null;
        if (isset($request->file_name) && count($request->file_name) > 0){
            foreach ($request->file_name as $key => $item){
                $name = $request->file_name[$key].'_'.time().'.'.$request->file[$key]->getClientOriginalExtension();
                $request->file[$key]->move(public_path('uploads'), $name);
                $file = '/uploads/'.$name;

                ContractFile::create([
                    'contract_id' => $id,
                    'name' => $request->file_name[$key],
                    'file' => $file,
                ]);

                $pdfContent = file_get_contents(public_path($file));
                $base64Pdf = base64_encode($pdfContent);
            }
        }
        return back();
    }

    public function show($id)
    {
        $data = Contract::with(['client', 'category', 'files', 'petitions', 'hybrids', 'judge', 'mib', 'sms'])->find($id);

        if($data->client->type == 1) $type = 'legal_judge';
        else $type = 'phy_judge';

        $judge = DB::table($type)
            ->join('judges', $type.'.judge_id', '=', 'judges.id')
            ->where([[$type.'.region_id', $data->client->region_id], [$type.'.district_id', $data->client->district_id]])
            ->select('judges.*')
            ->first();
        $sms_templates = DB::table('sms_templates')->get();
        $data->auto = \DB::table('contract_autotransports')->where('contract_id', $id)->first();

        return view($this->path.'.show', [
            'data' => $data,
            'judge' => $judge,
            'sms_templates' => $sms_templates,
        ]);
    }

    public function edit($id) {
	ini_set('memory_limit', '1G');
        $data = Contract::with(['client', 'category', 'files'])->find($id);
        $clients = Client::all();
        return view($this->path.'.create', ['data' => $data, 'clients' => $clients]);
    }

    public function destroy($id)
    {
        $data = Contract::find($id);
        $data->files()->delete();
        $data->petitions()->delete();
        $data->payments()->delete();
        $data->hybrids()->delete();
        $data->judge()->delete();
        $data->mib()->delete();
        $data->sms()->delete();

        User::auditable('contracts', $id, json_encode($data), 'D');

        $data->delete();
        return response(['message' => 'Шартнома ўчирилди!', 'content'=>""]);
    }

}
