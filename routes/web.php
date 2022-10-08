<?php

/** @var Src\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all  the routes for an application.
| It is a breeze. Simply tell Framework the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Src\Routing\Router;


Router::group(['middleware' => 'auth:admin|role:owner'], function () {
    Router::get('/', function () { return 'get' ;})->name('index');
    Router::post('/', function () {
        return [
            'url' => Router::getByNameWithBinding('index'),
            'test' => true,
        ];
    });
});



//$router->get('/testing/{id}/{name}/{role}/{permissions}',function () use ($router){
//    dd($router->list()['GET'][0]);
//   dd($router->getByName('testing',[
//       'id' => '5',
//       'name'=> 'mahmoud',
//       'role' => 'admin',
//       'permissions' => 'create_admin'
//   ]));
//    dd($router->namedRoute);
//})->name('testing');


