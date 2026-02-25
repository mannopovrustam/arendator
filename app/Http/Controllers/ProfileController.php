<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    public function postPhoneAuth()
    {
        $data = request()->all();
        $data['phone'] = str_replace(['(', ')', '-', ' '], '', $data['phone']);

        // validate phone and password

        $unix = now()->unix();
        $user = \App\Models\User::where('phone', $data['phone'])->first();

        if ($data['verify']){
            if ($user->verify_code == $data['verify']) {
                $user->update(['phone_verified_at' => now(), 'auth_date' => $unix]);
                \Illuminate\Support\Facades\Auth::login($user);
                return response()->json(['status' => 1, 'user_id' => $user->id], 200);
            }else{
                return response()->json(['status' => 2], 200);
            }
        }

        if ($user) {
            if (!\Illuminate\Support\Facades\Hash::check($data['password'], $user->password)) return response()->json(['status' => 3], 200);

            if (!$user->phone_verified_at) return response()->json(['status' => 0], 200);

            \Illuminate\Support\Facades\Auth::login($user);
            $user->update(['auth_date' => $unix]);
            return response()->json(['status' => 1, 'user_id' => $user->id], 200);
        } else{
            $auth = \Illuminate\Support\Facades\Validator::make($data, [
                'phone' => ['required', 'string', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'string', 'min:6'],
            ])->validate();

            \App\Models\User::create([
                'name' => "User-".$unix,
                'phone' => $auth['phone'],
                'password' => \Illuminate\Support\Facades\Hash::make($auth['password']),
                'verify_code' => 123456,
                'auth_date' => $unix
            ]);
            return response()->json(['status' => 0], 200);
        }

        /*    \DB::table('model_has_roles')->where('model_id',$user->id)->delete();
            $user->assignRole(1);*/

    }

    public function getDashboard1(){
        // return redirect to /profile/dashboard with flash message with key "status" and value "Success!" and with key "message" and value "Welcome to your dashboard!"
        return redirect('/profile/dashboard')->with(['login' => 'Success', 'password' => 'Welcome to your dashboard!']);
    }
    public function getDashboard(){
        $data['user_id'] = auth()->id();
        $data['user'] = \DB::table('tb_users')->where('id', $data['user_id'])->first();
        // send flush message

        return view('profile.dashboard', $data);
    }
    public function getEdit(){
        $data['user_id'] = auth()->id();
        $data['user'] = \DB::table('tb_users')->where('id', $data['user_id'])->first();
        return view('profile.edit', $data);
    }

    public function postEdit(Request $request){
        $data = $request->except(['_token']);
        $user_id = auth()->id();
        if ($data['name'] == '') return back()->withErrors(['name' => 'FIO majburiy!']);
        // if has file avatar
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $data['avatar'] = $filename;
        }

        if(\DB::table('tb_users')->where('id', $user_id)->update($data))
            return back()->with(['status' => 1, 'message' => 'Muvaffaqiyatli yangilandi!']);

        return back()->withErrors(['name' => 'Yangilanishda xatolik! Administratorga murojaat qiling!']);
    }

    public function getMessages(){
        $data['user_id'] = auth()->id();
        $data['user'] = \DB::table('tb_users')->where('id', $data['user_id'])->first();
        return view('profile.messages');
    }
    public function getListings(){
        $data['user_id'] = auth()->id();
        $data['user'] = \DB::table('tb_users')->where('id', $data['user_id'])->first();
        $data['listings'] = \DB::table('tb_listings')
            ->select('tb_listings.*', 'sp_currencies.name as currency')
            ->join('sp_currencies', 'tb_listings.id_currency', '=', 'sp_currencies.id')
            ->where('entry_by', $data['user_id'])->get();
        return view('profile.listings',$data);
    }

    public function getListingDetail($id){
        $data['user_id'] = auth()->id();
        $data['user'] = \DB::table('tb_users')->where('id', $data['user_id'])->first();
        $data['listings'] = \DB::table('tb_listings')->where('entry_by', $data['user_id'])->where('id', $id)->get();
        $data['listings_parent'] = \DB::table('tb_listings_parent')->where('id', $data['listings']->parent_id)->get();
        return view('profile.listings');
    }

    public function getChangePassword(){
        $data['user_id'] = auth()->id();
        $data['user'] = \DB::table('tb_users')->where('id', $data['user_id'])->first();
        return view('profile.change-password');
    }

    public function postChangePassword(Request $request) {
        $data = $request->except(['_token']);
        $user_id = auth()->id();
        if ($data['password'] == '') return back()->withErrors(['password' => 'Parol majburiy!']);
        if ($data['password'] != $data['password_confirmation']) return back()->withErrors(['password_confirmation' => 'Parol tasdiqlanmadi!']);

        if(\DB::table('tb_users')->where('id', $user_id)->update(['password' => \Illuminate\Support\Facades\Hash::make($data['password'])]))
            return back()->with(['status' => 1, 'message' => 'Muvaffaqiyatli yangilandi!']);

        return back()->withErrors(['name' => 'Yangilanishda xatolik! Administratorga murojaat qiling!']);
    }

}
