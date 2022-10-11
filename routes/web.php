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

use App\Controllers\UserController;
use Src\Routing\Router;


Router::group(['middleware' => 'auth:admin|role:owner'], function () {
    Router::get('/', function () { return 'get' ;})->name('index');
    Router::post('/', function () {
        return  Router::getByNameWithBinding('index');
    });
});

Router::group(['prefix'=> 'users' ,'as'=> 'users' ],function (){
    Router::get('/',[UserController::class,'index'])->name('index');
    Router::post('/',[UserController::class,'store'])->name('store');
    Router::get('/{id}',[UserController::class,'show'])->name('show');
    Router::put('/{id}',[UserController::class,'update'])->name('update');
    Router::delete('/{id}',[UserController::class,'destroy'])->name('destroy');
    Router::get('/{id}/edit',[UserController::class,'edit'])->name('edit');
    Router::get('/create',[UserController::class,'create'])->name('create')
        ->middleware('working');

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



















































































































































