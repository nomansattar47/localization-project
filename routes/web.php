<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Analytics\Period;
use Spatie\Analytics\AnalyticsFacade as Analytics;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/git_redirect', function () {
    return Socialite::driver('github')->redirect();
});
 
Route::get('/auth/git_callback', function () {
    $githubUser = Socialite::driver('github')->user();
 
    // echo 'Github User Token: ' . $githubUser->token;

    $user = User::updateOrCreate([
        'github_id' => $githubUser->id,
    ], [
        'name' => $githubUser->name,
        'email' => $githubUser->email,
        'password' => Hash::make('abcd'),
        'github_token' => $githubUser->token,
        'github_refresh_token' => $githubUser->refreshToken,
    ]);
 
    Auth::login($user);
 
    return redirect('/dashboard');

});

Route::get('/auth/facebook_redirect', function () {
    return Socialite::driver('facebook')->redirect();
});

Route::get('/auth/facebook_callback', function () {
    
    try {

        $facebookUser = Socialite::driver('facebook')->user();

    } catch (\Throwable $th) {
        echo 'error :' . $th;
        throw $th;
    }

    // echo 'Facebook User : ' . $facebookUser->id;

    // $user = User::updateOrCreate([
    //     'fb_id' => $facebookUser->id,
    // ], [
    //     'name' => $facebookUser->name,
    //     'email' => $facebookUser->email,
    //     'password' => Hash::make('abcd'),
    // ]);
    
    // Auth::login($user);

    // return redirect('/dashboard');

});

Route::get('/auth/google_redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/google_callback', function () {
    
    try {
        $user = Socialite::driver('google')->user();
    } catch (\Exception $e) {
        dd($e);
    }
    
    // check if they're an existing user
    $existingUser = User::where('email', $user->email)->first();
    if($existingUser){
        // log them in
        auth()->login($existingUser, true);
    } else {
        // create a new user
        $newUser                  = new User;
        $newUser->name            = $user->name;
        $newUser->email           = $user->email;
        $newUser->google_id       = $user->id;
        $newUser->avatar          = $user->avatar;
        $newUser->avatar_original = $user->avatar_original;
        $newUser->save();
        auth()->login($newUser, true);
    }
    return redirect('/dashboard');

});

Route::get('/analyticsData', function () {
    // $analyticsData = Analytics::fetchMostVisitedPages(Period::days(7));
    // $analyticsData = Analytics::fetchVisitorsAndPageViews(Period::months(6));
    $analyticsData = Analytics::fetchVisitorsAndPageViews(Period::days(7));

    // dd($analyticsData);

    foreach ($analyticsData as $data) {
        echo 'visitors: '   . $data['visitors'] . '<br>';
        echo 'pageTitle: '  . $data['pageTitle'] . '<br>';
        echo 'pageViews: '  . $data['pageViews'] . '<br>';
        echo 'date: '       . $data['date'] . '<br>';
        echo '------------' . '<br><br>';
    }
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
