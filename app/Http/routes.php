<?php
use App\Employee;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|

*/



/*When not logged in*/

Route::get('/', 'SignUpController@main');

Route::get('/about', function () {
    return view('about');
});

Route::get('/signup', function () {
    return view('signup');
});

Route::get('/profile','SignUpController@getProfile');

Route::post('/signup', 'SignUpController@store');

Route::get('/login','SignUpController@getLogin');

Route::post('/login','SignUpController@log');

Route::get('/logout', 'SignUpController@getLogout');

/* Routes for the functions for the Health Assistant  */

Route::get('/registerpatient', 'HAController@getRegPatient');
Route::post('/registerpatient', 'HAController@postRegPatient');
Route::get('/updatepatient', 'HAController@getUpdatePatient');
Route::post('/updatepatient', 'HAController@postUpdatePatient');
Route::get('/updatepatient2', 'HAController@getUpdatePatient2');
Route::post('/updatepatient2', 'HAController@postUpdatePatient2');
