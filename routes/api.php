<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('getSessionConfiguration', function () {
    $configuration = Cookie::get('pc-constructor');

    if($configuration) {
        return response()->json($configuration, 200);
    } else {
        return response()->json('', 200);
    }
});


Route::group(['namespace'=>'Api'], function () {
    Route::group(['prefix' => 'feature-types'], function ($route) {
        $route->post('get-value-feature-type', 'FeatureTypesController@getValueFeatureType')->name('feature-types.get-value');
    });

    Route::group(['prefix' => 'components'], function ($route) {
        $route->post('get-features-component', 'ComponentsController@getFeaturesComponent')->name('components.get-features');
        $route->post('get-component', 'ComponentsController@getComponent')->name('components.get-component');
    });

    Route::group(['prefix' => 'relations'], function ($route) {
        $route->post('get-feature-types', 'RelationsController@getFeatureTypes')->name('relations.get-feature-types');
        $route->post('get-features', 'RelationsController@getFeatures')->name('relations.get-features');
        $route->post('save-relation', 'RelationsController@saveRelation')->name('relations.save-relation');
        $route->post('edit-relation', 'RelationsController@editRelation')->name('relations.edit-relation');
        $route->post('remove-relations', 'RelationsController@removeRelations')->name('relations.remove-relations');
    });

    Route::group(['prefix' => 'catalog'], function ($route) {
        $route->post('get-components-by-filter', 'CatalogController@getComponentsByFilter')->name('catalog.get-components-by-filter');
    });

    Route::group(['prefix' => 'parser'], function ($route) {
        $route->get('get', 'ParserController@get');
        $route->post('update-catalog', 'ParserController@updateCatalog');
    });
});

