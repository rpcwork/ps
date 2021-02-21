<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
/*
$app->group(['prefix' => 'api/v1','namespace' => 'App\Http\Controllers'], function($app)
{
  $app->get('search','CountryController@search');

  //$app->post('add','CountryController@createCountry');

});
*/

$app->get('/', function () use ($app) {
    return 'default: '.$app->version();
});

$app->get('search', [
    'as' => 'search', 'uses' => 'CountryController@search'
]);
