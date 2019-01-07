<?php

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', 'SamehadaController@index');
// Route::get('/chapterDetail', 'HtmlParseController@chapterDetail');
Route::get('/chapterDetail', 'MangakuController@chapterDetail');
Route::get('/getToken', 'TokenController@getToken');
Route::get('/samehadaku', 'SamehadaController@index');

Route::get('/mangaku/home', 'MangakuController@home');
Route::post('/mangaku/home', 'MangakuController@home');
Route::get('/mangaku/kategori', 'MangakuController@kategori');
Route::post('/mangaku/mangaDetail', 'MangakuController@mangaDetail');
Route::post('/mangaku/mangaDetailChapter', 'MangakuController@mangaDetailChapter');
Route::post('/mangaku/chapterDetail', 'MangakuController@chapterDetail');
Route::post('/mangaku/latestRelease', 'MangakuController@latestRelease');
Route::post('/mangaku/mangaList', 'MangakuController@mangaList');
Route::post('/mangaku/kategoriList', 'MangakuController@kategoriList');

Route::post('/Samehada/listDownload', 'SamehadaController@listDownload');
Route::post('/Samehada/download', 'SamehadaController@download');

//Clear Cache facade value:
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});