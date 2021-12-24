<?php

use Illuminate\Support\Facades\Route;

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
    return view('index');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


Route::get('/home', function() {
    dd('ログイン成功');
})->middleware('auth');

//投稿ページ
// Route::get('/power-supply', 'App\Http\Controllers\CafeInfoController@create')->name('cafe.create');
// Route::post('/dashboad', 'App\Http\Controllers\CafeInfoController@store')->name('cafe.store');
//退会ページ
Route::get('/delete-user', 'App\Http\Controllers\UserInfoController@destroy')->name('user.delete');

//カフェ投稿
// Route::get('posts', 'CafeInfoController');
