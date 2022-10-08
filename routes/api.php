<?php

use App\Controllers\UserController;
use Src\Routing\Route;

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