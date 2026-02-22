<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\User;
use App\Repository\DataRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ru', 'uz'])) {
        app()->setLocale($locale);
        session()->put('locale', $locale);
    }
//    return app()->getLocale();
    return redirect()->back();
})->name('setLocale');

Route::get('/', function () {
    app()->setLocale(session()->get('locale', 'ru'));
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['backend'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::auto('profile', ProfileController::class);

require __DIR__ . '/auth.php';

Route::post('phone/auth', function () {
    $auth = request()->all();
    $unix = now()->unix();
    $user = User::where('phone', $auth['phone'])->first();
    if ($user->phone_sms != $auth['code']) return response()->json(['result' => 0, 'data' => null, 'comments' => 'Неверный код'], 200);
    if ($user) {
        Auth::login($user);
        $user->update(['auth_date' => $unix]);
    } else {
        $user = User::create([
            'name' => "User-" . $unix,
            'login' => $auth['username'] ?? '',
            'phone' => $auth['phone'],
            'auth_date' => $unix
        ]);
        Auth::login($user);
        \DB::table('model_has_roles')->where('model_id', $user->id)->delete();
        $user->assignRole(3);
    }

    return redirect('/dashboard');
});

Route::post('listing/phone-auth', function () {
    $data = request()->all();
    $data['phone'] = str_replace(['(', ')', '-', ' '], '', $data['phone']);

    $unix = now()->unix();
    $user = User::where('phone', $data['phone'])->first();

    if ($data['verify']) {
        if ($user->verify_code == $data['verify']) {
            $user->update(['phone_verified_at' => now(), 'auth_date' => $unix]);
            Auth::login($user);
            return response()->json(['status' => 1, 'user_id' => $user->id], 200);
        } else {
            return response()->json(['status' => 2], 200);
        }
    }

    if ($user) {
        if (!Hash::check($data['password'], $user->password)) return response()->json(['status' => 3], 200);

        if (!$user->phone_verified_at) return response()->json(['status' => 0], 200);

        Auth::login($user);
        $user->update(['auth_date' => $unix]);
        return response()->json(['status' => 1, 'user_id' => $user->id], 200);
    } else {
        $auth = Validator::make($data, [
            'phone' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'min:6'],
        ])->validate();

        User::create([
            'name' => "User-" . $unix,
            'phone' => $auth['phone'],
            'password' => Hash::make($auth['password']),
            'verify_code' => 123456,
            'auth_date' => $unix
        ]);
        return response()->json(['status' => 0], 200);
    }

});

Route::auto('data', DataRepository::class);
Route::get('data/search-hotels', [DataRepository::class, 'getSearchHotels']);
Route::auto('filters', FilterController::class);
Route::auto('listings', ListingController::class);
Route::auto('bookings', BookingController::class);
Route::resource('bookings', BookingController::class);
Route::resource('listings', ListingController::class);

Route::view('compare', 'compare');
Route::view('contact', 'contact');
Route::view('about', 'about');

Route::get('test-geo', function () {

// Example usage
    $lat1 = 40.72568130493164; // CAR latitude
    $lon1 = 72.65404510498047; // CAR longitude
    $lat2 = 40.2202927; // realize latitude
    $lon2 = 71.5580984; // realize longitude

    $distanceKm = haversineDistance($lat1, $lon1, $lat2, $lon2, 'km');

    echo "Distance between New York and Los Angeles:\n";
    echo "$distanceKm km\n";
});

function haversineDistance($lat1, $lon1, $lat2, $lon2, $unit = 'km')
{
    // Earth's radius in kilometers
    $earthRadius = 6371;

    // Convert latitude and longitude from degrees to radians
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    // Differences in coordinates
    $latDiff = $lat2 - $lat1;
    $lonDiff = $lon2 - $lon1;

    // Haversine formula
    $a = sin($latDiff / 2) * sin($latDiff / 2) +
        cos($lat1) * cos($lat2) *
        sin($lonDiff / 2) * sin($lonDiff / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;


    return round($distance, 2);
}


Route::get('/hikvision/plates', [\App\Http\Controllers\HikvisionController::class, 'getLicensePlates']);
Route::get('test', function (){
    return view('test');
    $data = \DB::table('tb_listings')->select('region_id','district_id')->get();
    //
    return array_values(array_unique($data->pluck('region_id')->all()));
});

Route::post('person-info', [\App\Services\PersonInfo::class, 'postPassportData']);

Route::view('quickview', 'quickview');
