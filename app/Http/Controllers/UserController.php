<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Socialite;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Validator, Input, Redirect;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

class UserController extends Controller {
    protected $layout = "layouts.main";

    public function __construct() {
        $this->data = array();
    }

    public function getRegister(Request $request ) {

        if($request->ajax() == true) {
            if(\Auth::check())
                return response()->json(['message'=>'success','Youre already login','status'=>'success']);

            if(config('sximo.cnf_regist') =='false')
                return response()->json(['message'=>'Register temporary disabled !','status'=>'success']);

            $this->data['Socialite'] =  config('services');
            return View('user.register_modal',$this->data);

        }
        else {
            return abort(404,'Page not found!');
        }
        if(config('sximo.cnf_regist') =='false') :
            if(\Auth::check()):
                return redirect('')->with(['message'=>'Youre already login','status'=>'error']);
            else:
                return redirect('user/login');
            endif;
        else :
            $this->data['Socialite'] = config('services');
            return view('user.register', $this->data);
        endif ;
    }

    public function postCreate( Request $request) {

        $post = $request->all();
        if (!isset($post['commerce'])) $post['commerce'] = 0;
        $tmpArr = [' ', '-', '(', ')', '+','_'];
        foreach ($tmpArr as $a)
            $post['phone'] = trim(str_replace($a, '',$post['phone']));

        $rules = array(
            'phone'=>'required|unique:tb_users,phone|min:12|max:20',
            'password'=>'required|between:6,12|confirmed',
            'password_confirmation'=>'required|between:6,20',
            'resident'=>'required|integer|in:1,2',
            'firstname'=>'required|string|min:1|max:50',
            'surname'=>'required|string|min:1|max:50',
            'lastname'=>'max:50',
            "pspNumb"  => "required|string|min:3|max:20",
            "address"  => "required|string|min:5|max:255",
        );

        if ($post['resident'] == 1)
            $rules = array_merge($rules, array("commerce"  => "integer|in:1,0","id_region"  => "required|integer","id_district" => "required|integer"));

        /*if(config('sximo.cnf_recaptcha') =='true')
        {
            $return = $this->reCaptcha($post);
            if($return !== false)
            {
                if($return['success'] !='true')
                {
                    return response()->json(['status' => $return['success'], 'message' =>'Invalid reCpatcha']);
                }
            }
        }*/
        $validator = Validator::make($post, $rules);
        if ($validator->passes()) {
            $code = mt_rand(10000000, 99999999);
            $authen = new User;
            $authen->firstname = trim($post['firstname']);
            $authen->surname = trim($post['surname']);
            $authen->lastname = empty(trim($post['lastname'])) ? 'XXX' : trim($post['lastname']);
            $authen->phone = trim($post['phone']);
            $authen->activation = $code;
            $authen->resident = $post['resident']*1;
            $authen->pspNumb = trim($post['pspNumb']);
            $authen->address = trim($post['address']);
            if ($post['resident'] == 1) {
                $authen->id_region = $post['id_region']*1;
                $authen->id_district = $post['id_district']*1;
            }
            $authen->commerce = $post['commerce'] *1;

            $authen->group_id = $this->config['cnf_group'];
            $authen->password = \Hash::make($post['password']);

            if($this->config['cnf_activation'] == 'auto') {
                $authen->active = '1';
                $authen->status = 'Active';
            }
            else {
                $authen->active = '0';
                $authen->status = 'Unconfirmed';
            }
            $authen->save();
            return response()->json(['message'=> __('core.success_registerauto'),'status'=>'success','activated'=>1]);
        }
        else {
            return response()->json(['message'=> \Lang::get('core.unsuccess_registerauto'),'status'=>'error', 'errors'=>$validator->messages()->toJson()]);
        }
    }

    public function getActivation(Request $request) {
        $num = $request->input('code');
        if($num =='')
            return redirect('user/login')->with(['message'=>'Invalid Code Activation!','status'=>'error']);

        $user =  User::where('activation','=',$num)->get();
        if (count($user) >=1)
        {
            \DB::table('tb_users')->where('activation', $num )->update(array('active' => 1,'activation'=>''));
            return redirect('user/login')->with(['message'=>'Your account is active now!','status'=>'success']);
        } else {
            return redirect('user/login')->with(['message'=>'Invalid Code Activation!','status'=>'error']);
        }
    }

    public function getLogin(Request $request) {
        if(\Auth::check()) return redirect('/')->with(['message'=>'success','Youre already login','status'=>'success']);
        return $this->socialite('egov');
    }

    public function reCaptcha( $request)
    {
        if(!is_null($request['g-recaptcha-response']))
        {
            $api_url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . config('sximo.cnf_recaptchaprivatekey') . '&response='.$request['g-recaptcha-response'];
            $response = @file_get_contents($api_url);
            $data = json_decode($response, true);

            return $data;
        }
        else
        {
            return false ;
        }
    }

    public function postSignin( Request $request) {
        return abort(404,'Unallowed method!');
    }

    public function getProfile() {

        if(!\Auth::check()) return redirect('user/login');
        $info =	User::find(\Auth::user()->id);
        $this->data = array(
            'pageTitle'	=> 'My Profile',
            'pageNote'	=> 'View Detail My Info',
            'info'		=> $info,
        );
        return view('user.profile',$this->data);
    }

    public function postSaveprofile(Request $request)
    {
        if(!\Auth::check()) return redirect('/');
        $rules = array(
            'firstname'=>'required|string|min:1',
            'surname'=>'required|string|min:1',
            'lastname'=>'max:50',
            'address'=>'max:50',
        );

        if($request->input('phone') != \Session::get('phone',null))
            $rules['phone'] = 'required|unique:tb_users,phone|min:12|max:20';

        if(!is_null($request->file('avatar'))) $rules['avatar'] = 'mimes:jpg,jpeg,png,gif,bmp';
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            if(!is_null($request->file('avatar')))
            {
                $file = $request->file('avatar');
                $destinationPath = './uploads/users/';
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension(); //if you need extension of the file
                $newfilename = \Session::get('uid').'.'.$extension;
                $uploadSuccess = $request->file('avatar')->move($destinationPath, $newfilename);
                if( $uploadSuccess ) {
                    $data['avatar'] = $newfilename;
                }
                $orgFile = $destinationPath.'/'.$newfilename;
                \SiteHelpers::cropImage('80' , '80' , $orgFile ,  $extension,	 $orgFile)	;
            }

            $user = User::find(\Session::get('uid',0));
            $user->firstname 	= $request->input('firstname');
            $user->lastname 	= $request->input('lastname');
            $user->surname 	= $request->input('surname');
            $user->address 	= $request->input('address');
            $user->phone 		= $request->input('phone');
            if(isset( $data['avatar']))  $user->avatar  = $newfilename;
            $user->save();
            $newUser = User::find(\Session::get('uid',0));

            \Session::put('fid',$newUser->firstname);
            \Session::put('phone',$newUser->phone);

            return redirect('user/profile')->with('message','')->with('status','success');
        } else {
            return redirect('user/profile')->with('message','The following errors occurred')->with('status','error')
                ->withErrors($validator)->withInput();
        }
    }

    public function postSavepassword( Request $request)
    {
        $rules = array(
            'password'=>'required|between:6,12',
            'password_confirmation'=>'required|between:6,12'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            $user = User::find(\Session::get('uid',0));
            $user->password = \Hash::make($request->input('password'));
            $user->save();
            return redirect('user/profile')->with(['status' => 'success', 'message' => 'Password has been saved!'] );
        } else {
            return redirect('user/profile')->with(['status' => 'error', 'message' => 'The following errors occurred'])
                ->withErrors($validator)->withInput();
        }
    }

    public function getReminder()
    {
        return view('user.remind');
    }

    private function generateCaptcha() {
        $phraseBuilder = new PhraseBuilder(4, '0123456789');
        $__captcha = new CaptchaBuilder(null, $phraseBuilder);
        $__captcha->setDistortion(false);
        $this->data['_captcha'] = $__captcha->build()->inline();
        \Session::put('_phrase', $__captcha->getPhrase());
    }

    public function postRequest( Request $request)
    {
        $rules = array(
            'phone_number'=>'required|min:12|max:20',
        );
        $post = $request->all();
        if (!(isset($post['recaptcha2']) && $post['recaptcha2'] == \Session::get('_phrase'))) {
            $this->generateCaptcha();
            return response()->json(['status' => 'error', 'message' => 'Invalid Cpatcha!', 'captcha' => $this->data['_captcha']]);
        }
        $tmpArr = [' ', '-', '(', ')', '+','_'];
        foreach ($tmpArr as $a)
            $post['phone_number'] = trim(str_replace($a, '',$post['phone_number']));
        $gen_code = mt_rand(100000,999999);
        $validator = Validator::make($post, $rules);
        if ($validator->passes()) {

            //reset password
            if (isset($post['_code']) && !empty($post['_code'])) {
                $row = \DB::table('tb_users')->where('phone',$post['phone_number'])->where('active',1)->select('activation')->first();
                if ($row && $row->activation*1 == $post['_code']*1) {
                    $new_password = $this->rand_passwd(); //generate new password
                    $hash = \Hash::make($new_password);
                    if (\DB::table('tb_users')->where('phone', $post['phone_number'])->where('active', 1)->where('activation', $post['_code'])->update(['password' => $hash])) {
                        $this->sendSMS($post['phone_number'], 'Emehmon - new password: ' . $new_password);
                        //return redirect('/')->with(['message'=>'success','Youre password was sent to your phone!','status'=>'success']);
                        return response()->json(['status' => 'success', 'message' => 'Youre password was sent to your phone!', 'code' => 200, 'reload' => 1]);
                    }
                }
                else
                {
                    \Log::warning('Try to reset password for ' . $post['phone_number'] . ' from ' . $_SERVER['REMOTE_ADDR'] . ' (' . gethostname() . ')');
                    return response()->json(['status' => 'error', 'message' => 'Error: Wrong secury code! Please, check your phone!','code'=>200]);
                }
            }
            if (substr($post['phone_number'],0,3) != '998') return response()->json(['status' => 'error', 'message' => 'Error: SMS provider supports only Uzbekistan numbers!','code'=>200]);
            $user =  User::where('phone','=',$post['phone_number']);
            if($user->count() >=1)
            {
                if (\DB::table('tb_users')->where('phone',$post['phone_number'])->where('active',1)->update(['activation'=>$gen_code])) {
                    //send sms secure code ...
                    if ($this->sendSMS($post['phone_number'],'Emehmon - secure code: ' . $gen_code))
                        return response()->json(['status' => 'success', 'message' => 'Secure code was sent to your phone, enter the security code to reset password','code'=>200]);
                    else {
                        \Log::warning('Try to reset password for unsupported phone ' . $post['phone_number'] . ' from ' . $_SERVER['REMOTE_ADDR'] . ' (' . gethostname() . ')');
                        return response()->json(['status' => 'error', 'message' => 'Error: SMS provider not respond! Please, try later ...', 'code' => 200]);
                    }
                }
            }
        }
        $this->generateCaptcha();
        \Log::warning('Try to reset password for ' . $post['phone_number'] . ' from ' . $_SERVER['REMOTE_ADDR'] . ' (' . gethostname() . ')');
        return response()->json(['status' => 'error', 'message' => 'Cant find phone number','captcha' => $this->data['_captcha']]);
    }

    private function sendSMS($phone, $text) {
        try {
            $API_URL = 'https://sms.filecloud.uz/gateway/index';
            $API_KEY = 'NbZ99dItpZ*9d%S&w$xeoC=7E@3Db4-m2!O#WI8M';
            $API_SERVICE_ID = '77f4ac78efb53ce7f4784b0b0f487447';
            $salt = round(microtime(true) * 1000);
            $hash = sha1($API_KEY . $salt);
            $auth = $API_SERVICE_ID . '-' . $hash . '-' . $salt;
            $gen_id = mt_rand(1000000, 9999999);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $API_URL);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'method' => 'sendMes',
                'params' => array('phone' => str_replace('+','',$phone), 'text' => $text),
                'id' => $gen_id,
                'jsonrpc' => '2.0'
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json", 'Auth:' . $auth));

            $final_result = curl_exec($ch);
            //$text = json_encode($final_result, JSON_UNESCAPED_UNICODE);
            curl_close($ch);
            //return iconv(mb_detect_encoding($text, mb_detect_order(), true), "UTF-8", $text);
            $res = json_decode($final_result);
            return (isset($res->result->message) && ($res->result->message == 'Success' || $res->result->message == 'success')) ? true : false;
        }
        catch (\Exception $ex) {
            //dd($ex);
            return false;
        }
    }

    private function rand_passwd($length = 8, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' ) {
        return substr(str_shuffle($chars ), 0, $length);
    }

    public function getReset( $token = '')
    {
        if(\Auth::check()) return redirect('dashboard');

        $user = User::where('reminder','=',$token);;
        if($user->count() >=1)
        {
            $this->data['verCode']= $token;
            return view('user.remind',$this->data);

        } else {
            return redirect('user/login')->with(['message'=>'Cant find your reset code','status'=>'error']);
        }

    }

    public function postDoreset( Request $request , $token = '')
    {
        $rules = array(
            'password'=>'required|alpha_num|between:6,12|confirmed',
            'password_confirmation'=>'required|alpha_num|between:6,12'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {

            $user =  User::where('reminder','=',$token);
            if($user->count() >=1)
            {
                $data = $user->get();
                $user = User::find($data[0]->id);
                $user->reminder = '';
                $user->password = \Hash::make($request->input('password'));
                $user->save();
            }

            return redirect('user/login')->with(['message'=>'Password has been saved!','status'=>'success'] );
        } else {
            return redirect('user/reset/'.$token)->with(['message'=>'The following errors occurred','status'=>'error'])->withErrors($validator)->withInput();
        }
    }

    private function logout() {
        $ch = curl_init();
        $arg = [
            'grant_type' => 'one_log_out',
            'client_id' => 'e-gaz',
            'client_secret' => '5i8XBwh7ThLyGqTaKLYA7bzP',
            'access_token' => session('state'),
            'scope' => 'e-gaz'
        ];
        curl_setopt($ch, CURLOPT_URL,"https://sso2.egov.uz:8443/sso/oauth/Authorization.do");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arg);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        if(!$result || $result === false) {
            \Log::error('Cannon logout with access_token from oneid');
            return null;
        }
        $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

    }

    public function getLogout() {
        $this->logout();
        \Auth::logout();
        \Session::flush();
        return redirect('')->with(['message'=>'Your are now logged out!','status'=>'success']);
    }

    function socialite($social) {
        return Socialite::driver($social)->redirect();
    }

    function autosocialite($social) {
//        $user = Socialite::driver($social)->stateless()->user();
//        if (!$user) return redirect('/')->with(['message'=>'Session expired!','status'=>'info']);
        $socialite = '{"token":"37db9152-ec89-4f8b-bcc6-845c01a4111f","refreshToken":"39873552-294e-47fb-9f64-a75f10705679","expiresIn":1768123120604,"id":"33110950530015","nickname":"mannopovr","name":"RUSTAM","email":"mannopovr@gmail.com","avatar":null,"user":{"valid":"false","pin":"33110950530015","email":"mannopovr@gmail.com","valid_methods":[],"user_id":"mannopovr","phone":"998977820809","pspNumb":"AE7009090","surname":"MANNOPOV","firstname":"RUSTAM","lastname":"RAVSHAN O‘G‘LI","birth_date":"1995-10-31","birth_place":"TOSHKENT TUMANI","birth_place_id":"12","natn":"O‘ZBEK","nationality_id":"44","ctzn":"O‘ZBEKISTON","citizenship_id":"182","gd":"1","pport_issue_place":"XORIJGA CHIQISH-KELISH VA FUQAROLIKNI RASMIYLASHTIRISH BOSHQARMASI","pport_issue_date":"2025-10-04","pport_expr_date":"2035-09-04","address":"Ташкентская область, Ташкентский район, Гулистон КФЙ, Саноат МФЙ, Саноат, дом 71","user_type":"I","sess_id":"37db9152-ec89-4f8b-bcc6-845c01a4111f","ret_cd":"0","auth_method":"LOGINPASSMETHOD","legal_info":[]},"phone":"998977820809","pin":"33110950530015"}';

        $user = json_decode($socialite, true);

        $user = [
            'birth_date' => isset($user['user']['birth_date']) ? $user['user']['birth_date'] : '',
            'ctzn' => isset($user['user']['ctzn']) ? $user['user']['ctzn'] : '',
            'address' => isset($user['user']['address']) ? $user['user']['address'] : '',
            'pport_issue_place' => isset($user['user']['pport_issue_place']) ? $user['user']['pport_issue_place'] : '',
            'surname' => isset($user['user']['surname']) ? $user['user']['surname'] : '',
            'gd' => isset($user['user']['gd']) ? $user['user']['gd'] : '',
            'natn' => isset($user['user']['natn']) ? $user['user']['natn'] : '',
            'pport_issue_date' => isset($user['user']['pport_issue_date']) ? $user['user']['pport_issue_date'] : '',
            'pport_expr_date' => isset($user['user']['pport_expr_date']) ? $user['user']['pport_expr_date'] : '',
            'pspNumb' => isset($user['user']['pspNumb']) ? $user['user']['pspNumb'] : '',
            'pin' => isset($user['user']['pin']) ? $user['user']['pin'] : '',
            'phone' => isset($user['user']['phone']) ? $user['user']['phone'] : '',
            'user_id' => isset($user['user']['user_id']) ? $user['user']['user_id'] : '',
            'email' => isset($user['user']['email']) ? $user['user']['email'] : '',
            'birth_place' => isset($user['user']['birth_place']) ? $user['user']['birth_place'] : '',
            'lastname' => isset($user['user']['lastname']) ? $user['user']['lastname'] : '',
            'valid' => isset($user['user']['valid']) ? $user['user']['valid'] : '',
            'user_type' => isset($user['user']['user_type']) ? $user['user']['user_type'] : '',
            'sess_id' => isset($user['user']['sess_id']) ? $user['user']['sess_id'] : '',
            'ret_cd' => isset($user['user']['ret_cd']) ? $user['user']['ret_cd'] : '',
            'firstname' => isset($user['user']['firstname']) ? $user['user']['firstname'] : ''
        ];

        $row = \DB::table('tb_users')->select('id')->where('pin',$user['pin'])->first();
        if ($row && $row->id) {
            $user['auth_date'] = now()->unix();
            \DB::table('tb_users')->where('id',$row->id)->update($user);
            return self::autoSignin($row->id);
        }
        else {
            $arr = $user;
            $arr['resident'] = '1';
            $arr['commerce'] = 0;
            $arr['active'] = 1;
            $arr['auth_date'] = now()->unix();
            $arr['status'] = 'Active';



            $id = \DB::table('tb_users')->insertGetId($arr);
            return self::autoSignin($id);
        }
    }

    function autoSignin($id) {
        if(is_null($id))
            return $this->socialite('egov');
        else {
            \Auth::loginUsingId($id);
            if(\Auth::check()) {
                $row = User::find(\Auth::user()->id);
                if($row->active =='0') {
                    // inactive
                    \Auth::logout();
                    return redirect('/')->with(['message'=>'Your Account is not active','status'=>'error']);
                } else if($row->active=='2')
                {
                    // BLocked users
                    \Auth::logout();
                    return redirect('/')->with(['message'=>'Your Account is BLocked','status'=>'error']);
                } else {
                    $session = array(
                        'user' => $row,
                        'uid' => $row->id,
                        'phone' => $row->phone,
                        'll' => $row->lastlogin,
                        'fid' =>  $row->firstname.' '. $row->surname,
                        'username' => $row->user_id ,
                        'join'	=>  $row->created_at
                    );
                    session($session);
                    return redirect('dashboard');
                }
            }
        }
    }
}
