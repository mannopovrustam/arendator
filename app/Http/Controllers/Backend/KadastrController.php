<?php namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Library\CrudEngine;
use App\Library\SiteHelpers;
use App\Models\Cadastr;
use App\Models\Users;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Input;
use Redirect;
use Validator;


class KadastrController extends Controller {

    public function __construct(){
        //
    }

    public function index() {
        if (!\Auth::check()) return redirect('/')->with('status', 'error')->with('message', __('core.urnotlogin'));

        $lang = \Session::get('lang', 'ru');
        $grp_id = \Session::get('gid', \Auth::user()->group_id) * 1;
        $this->data['rhsearch'] = '';
        $this->data['tableTH'] = '<th>ID</th><th>НАЗВАНИЕ </th><th>КАДАСТР</th><th class="non_searchable">ТИП ЖИЛЬЯ</th><th>АДРЕС</th><th>КОММЕРЦ.</th><th>СТАТУС</th>';
        switch ($lang) {
            case 'en':
                $this->data['tableTH'] = '<th>ID</th><th>NAME </th><th>CADASTRE</th><th class="non_searchable">TYPE </th><th>ADDRESS</th><th>COMMERC.</th><th>STATUS</th>';
                break;
            case 'uz':
                $this->data['tableTH'] = '<th>ID</th><th>NOMI </th><th>KADASTR</th><th class="non_searchable">TURI </th><th>ADRES</th><th>TIJORAT.</th><th>STATUS</th>';
                break;
        }
        $this->data['columnsTH'] = "{data:'id',name:'id',sClass:'dt-center'},
            {data:'name',name:'name'},{data:'cad_number',name:'cad_number'},
            {data:'object_type',name:'object_type'},{data:'address',name:'address'},
            {data:'commerce',name:'commerce',sClass:'dt-center'},{data:'st',name:'st',sClass:'dt-center'}";

        $this->data['exportColumns'] = ',exportOptions:{columns: [0,1,2,3,4]}';
        return view('backend.kadastr.index', $this->data);
    }

    function getUpdate(Request $request, $id = '') {
        if (!$request->ajax()) return '<h3 class="text-red">Error: 404 <br>' . __('core.no_blanklink');
        if (!\Auth::check()) return '<h3 class="text-red">Error: 401 <br>' . __('core.urnotlogin');

        $config = $this->model->connector($this->module, 'id');
        $this->access = $this->model->getAccess($config['id'], session('gid'));
        $this->data = array(
            'pageModule' => $this->module,
            'pageTitle' => $config['title'],
            'pageNote' => $config['note'],
            'access' =>  $this->access,
            'pageUrl' => asset('cadastr/save')
        );

        if (!$id || !in_array('update', $this->access)) return '<h3 class="text-error">Error: 401 <br>' . __('core.no_access');
        $id = \SiteHelpers::encryptID($id, true);
        $row = Cadastr::find($id);
        if ($row) {
            //check for unauthorized access by id, check by entry_by here  ....
            if($row->id_user != session('uid',\Auth::user()->id)) {
                return '<h3 class="text-red">Error: 401 <br>' . __('core.no_access');
            }
        }
        else
            return '<h3 class="text-red">Error: 404 <br>' . __('core.find_guest_nofound');

        $this->data['row'] = $row;
        return view('backend.kadastr.form', $this->data);
    }

    function postSave(Request $request, $id = 0) {
        if (!\Auth::check()) return redirect('/')->with('status', 'error')->with('message',__('core.urnotlogin'));
        $config = $this->model->connector($this->module, 'id');
        $access = $this->model->getAccess($config['id'], session('gid'));

        if (\Session::get('lang', 'ru') == 'ru') $payBtn = 'Оплатить';
        if (\Session::get('lang','uz') == 'uz') $payBtn = 'To`lash';

        if (!in_array('update', $access)) return redirect('dashboard')->with('status', 'error')->with('message', __('core.no_access'));

        $fields = $request->all();
        $grp_id = \Session::get('gid', \Auth::user()->group_id) * 1;
        $usr_id = \Session::get('uid', \Auth::user()->id) * 1;

        if (!$fields['id']) return response()->json(array('message' => 'Wrong request parameter!', 'status' => 'warning'));
        if (!in_array($fields['commerce']*1,[0,1])) return response()->json(array('message' => 'Missing Commercial field!', 'status' => 'warning'));
        if (!$fields['name'] || strlen($fields['name'])>255) return response()->json(array('message' => 'Wrong Name field!', 'status' => 'warning'));
        //$id = \SiteHelpers::encryptID($fields['id'],true);
        $q = \DB::table('tb_cadastrs')->where('id',$fields['id']);

        if ($grp_id != 1) $q = $q->where('id_user', $usr_id);
        $q->update(['name'=>$fields['name'],'commerce'=>$fields['commerce']]);
        return response()->json(array('status' => 'success', 'message' => \Lang::get('core.note_success')));
    }

    public function getData(Request $request) {

        $d = \DB::table('tb_cadastrs')->where('id_user',\Auth::user()->id);

        return Datatables::of($d)
            ->rawColumns(['st','id', 'commerce'])
            ->setRowData(['data-id' => function($row) {return base64_encode($row->id);},'data-name'=>function($row) {return $row->name;},'data-cad'=>function($row) {return $row->cad_number;}])
            ->editColumn('st', function($row) {
                if ($row->st * 1 == 1) return '<span class="label label-success text-uppercase">ACTIVE </span>';
                return '<span class="label label-danger text-uppercase">DEACTIVE </span>';
            })
            ->editColumn('commerce', function($row) {
                if ($row->commerce * 1 != 0) return '<i class="fa fa-money" title="Yes"></i>';
                return '<i class="fa fa-times" title="No"></i>';
            })
            ->make(true);
    }

    public function getQuickView(Request $request, $id = null) {
        //region Check auth access
        if (!$request->ajax()) return '<div class="alert alert-info"><h3 class="text-center">' .__('core.no_blanklink') . '</h3></div>';

        if (empty($id)) return '<div class="alert alert-danger"><h3 class="text-center">' .__('core.no_access') . '</h3></div>';

        $config = $this->model->connector($this->module, 'id');
        $access = $this->model->getAccess($config['id'], session('gid'));

        if (!in_array('view', $access)) return '<div class="alert alert-danger"><h3 class="text-center">' .__('core.no_access') . '</h3></div>';
        //endregion
        $id = \SiteHelpers::encryptID($id, true);

        $row = Cadastr::find($id);
        if ($row) {
            $this->data['row'] = $row;
            $this->data['id'] = $id;
            $this->data['access'] = $access;
            return view('backend.kadastr.quick', $this->data);

        } else {
            return '<div class="alert alert-danger"><h3 class="text-center">' .__('core.note_error') . '</h3></div>';
        }
    }

    public function gotCadastres(Request $request) {
        if (!\Auth::check()) return redirect('/')->with('status', 'error')->with('message', __('core.urnotlogin'));

        $usr_id = \Session::get('uid', \Auth::user()->id) * 1;
        if (!$usr_id) return redirect('/')->with('status', 'error')->with('message', __('core.urnotlogin'));

        $user = \DB::table('tb_users')->select('id','pin')->where('id',$usr_id)->first();

        if (!$user || !$user->pin) return redirect('/')->with('status', 'error')->with('message', 'User not found or wrong PINFL');

        $ret = \SiteHelpers::kadastrListbyPINFL($user->pin);
        $affected = 0;
        if ($ret) {
            $arr = json_decode($ret, JSON_UNESCAPED_UNICODE);
            if (!isset($arr['cadastr_count'])) return response()->json(array('message' => 'На Вашем имени не зарегистрованы кадастры! Обратитесь в службу Кадастра!<br>You have not cadastras registered in Cadastre database! Contact to Cadastre service/support!', 'status' => 'info'));
            if ($arr['cadastr_count']*1>0) {
                foreach($arr['cadastr_list'] as $c) {
                    $raw = \SiteHelpers::kadastrListbyKadastr($c);
                    if ($raw) {
                        $cad = json_decode($raw, JSON_UNESCAPED_UNICODE);
                        if ($cad && isset($cad['address']) && isset($cad['cad_number']) && $cad['address'] && $cad['cad_number']) {

                            $affected += $this->updateCadastre($cad, $usr_id);
                        }
                    }
                }
                $this->deactivateCadastres($arr['cadastr_list'], $usr_id);
            }

        }
        return response()->json(array('message' => 'Updated cadastres: ' . $affected, 'status' => 'info'));
    }

    private function updateCadastre($cad, $user_id) {
        $data = [];
        if (isset($cad['district_id'])) {
            $d = \DB::table('tb_districts_coato')->where('cad_id_district', $cad['district_id'])->first();
            if ($d && $d->id) $data = [
                'id_region' => $d->id_region,
                'id_district' => $d->id,
                'coato_rgn' => $cad['region_id'],
                'coato_dst' => $cad['district_id'],
            ];
        }
        $data['address'] = $cad['address'];
        $data['st'] = '1';
        $data['object_type'] = ($cad['vid']*1 == 1 ? 'Жилой' : 'Нежилой');

        $row = \DB::table('tb_cadastrs')->select('id')->where('cad_number',$cad['cad_number'])->where('id_user',$user_id)->first();
        if ($row) {
            \DB::table('tb_cadastrs')->where('id',$row->id)->update($data);
            return 1;
        }
        $data['cad_number'] = $cad['cad_number'];
        $data['commerce'] = '0';
        $data['id_user'] = $user_id;
        $data['name'] = isset($cad['area_bino']) ? 'NoName-' . $cad['area_bino'] . 'kv/m' : 'No name';
        \DB::table('tb_cadastrs')->insert($data);
        return 1;
    }

    private function deactivateCadastres($cads, $user_id) {
        $arr = [];
        foreach($cads as $c)
            $arr[] = $c;

        \DB::table('tb_cadastrs')->whereNotIn('cad_number',$arr)
            ->where('id_user', $user_id)->update(['st'=>'0']);
    }
}
