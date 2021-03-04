<?php

use Illuminate\Http\Request;

/**
 * @var $router \Illuminate\Routing\Router
 */
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

$router->group(['prefix' => 'v1'], static function () use ($router) {
    $router->post('transaction', 'TransactionController@retrieve');
});
