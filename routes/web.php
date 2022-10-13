<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TwilioSMSController;

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
Route::get('sendSMS', [TwilioSMSController::class, 'index']);
Route::get('survey', [TwilioSMSController::class, 'responses']);

Route::get('/', function () {
    return view('welcome');
});
