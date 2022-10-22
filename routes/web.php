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

$router->get('/' ,function (\Src\Http\Request $request){
    return $request->uri() .  '====> home page';
});

$router->group(['prefix'=> 'users' ,'as'=> 'users' ,'namespace'=> 'App\Controllers'],function (Router $router){
    $router->get('/',[UserController::class,'index'])->name('index');
    $router->post('/',[UserController::class,'store'])->name('store');
    $router->get('/create',[UserController::class,'create'])->name('create')
        ->middleware('working');
    $router->get('/{name:[A-Za-z_-]}','UserController@show')->name('show');
    $router->put('/{id}',[UserController::class,'update'])->name('update');
    $router->delete('/{id}',[UserController::class,'destroy'])->name('destroy');
    $router->get('/{id}/edit',[UserController::class,'edit'])->name('edit');
});




















































































































































