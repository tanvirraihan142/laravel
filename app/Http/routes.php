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

/* Routes for the functions for the Chief Health Officer  */
Route::get('/createCampaign', 'CHOController@getCreateCampaign');
Route::post('/createCampaign', 'CHOController@postCreateCampaign');

Route::get('/setCenter', 'CHOController@getSetCenter');
Route::get('/setCenter/{campaign_no}', 'CHOController@getSetCenter2');
Route::get('/setCenter2', 'CHOController@getSetCenter21');
Route::get('/setCenter2/add/{center_no}', 'CHOController@getSetCenter22');
Route::get('/setCenter2/del/{center_no}', 'CHOController@getSetCenter23');
Route::post('/setCenter2', 'CHOController@postSetCenter2');

Route::get('/assignHO', 'CHOController@getAssignHO');
Route::get('/assignHO/{campaign_no}', 'CHOController@getAssignHO2');

Route::get('/assignHO2', 'CHOController@getAssignHO3');
Route::get('/assignHO2/{center_no}', 'CHOController@getAssignHO4');\

Route::get('/assignHO3', 'CHOController@getAssignHO5');
Route::get('/assignHO3/{emp_id}', 'CHOController@getAssignHO6');
Route::post('/assignHO3', 'CHOController@postAssignHO3');

Route::get('/notify', 'CHOController@getNotifications');
Route::get('/notify/{campaign_no}', 'CHOController@getNotifications2');
Route::get('/notify2', 'CHOController@getNotifications3');
Route::post('/notify2', 'CHOController@getNotifications4');

/* Routes for the functions for the Health Officer  */
Route::get('/addCenter', 'HOController@getCenter');
Route::post('/addCenter', 'HOController@postCenter');
Route::get('/addVaccine', 'HOController@getVaccine');
Route::post('/addVaccine', 'HOController@postVaccine');

Route::get('/assignHA', 'HOController@getAssignHa');
Route::get('/assignHA/{cc}', 'HOController@getAssignHa2');
Route::get('/assignHA2', 'HOController@getAssignHa3');
Route::get('/assignHA2/add/{empno}', 'HOController@getAssignHa4');
Route::get('/assignHA2/del/{empno}', 'HOController@getAssignHa5');



