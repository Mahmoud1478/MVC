<?php


use Src\Cookie\Cookie;
use Src\Router\Route;
use App\Controllers\UserController;
use Src\Database\Connection;

Route::get('/',function (){
   $connection = new Connection();
   echo '<pre>';
   print_r($connection->all());
   echo '</pre>';


});

Route::prefix('/admin',function (){
    Route::get('/users','UserController@index');
    Route::prefix('user',function (){
        Route::get('/create',[UserController::class,"create"]);
        Route::get('/{id}/show','UserController@show');
        Route::post('/store','UserController@store');
        Route::get('/{id}/edit','UserController@edit');
        Route::delete('/delete/{id}','UserController@delete');
        Route::put('/{id}/update','UserController@update');
    });


});


