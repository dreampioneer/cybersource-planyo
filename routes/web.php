<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->get('/checkout', ['as' => 'checkout', 'uses' => 'CheckOutController@checkOut']);
$router->post('/checkout', 'CheckOutController@checkOutP');
$router->post('/checkout/checkout_process', 'CheckOutController@checkOutProcess');
$router->post('/planyo/webhook','PlanyoController@webHook');