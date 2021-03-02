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

Route::group(['prefix' => 'admin', 'middleware' => ['pc-config']], function () {
    Voyager::routes();
});

Route::group(['namespace' => 'Pages', 'middleware' => ['configuration']], function () {
    Route::get('/', 'MainController@index')->name("pages.home.index");
    Route::get('/your-pc', 'YourPcController@indexGet')->name("pages.your-pc.index-get");
    Route::post('/your-pc', 'YourPcController@indexPost')->name("pages.your-pc.index-post");

    Route::group(['prefix' => 'catalog'], function () {
        Route::get('/{slug}', 'CatalogController@index')->name("pages.catalog.index")->where('slug', '[a-z-]{2,255}');
    });

    Route::group(['prefix' => 'card'], function () {
//        Route::get('', 'BrandsController@index')->name('pages.brands.index');
    });
});

