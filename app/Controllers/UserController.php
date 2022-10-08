<?php

namespace App\Controllers;

use Src\Cookie\Cookie;
use Src\Http\Request;

class UserController
{

    public function index(){

        echo 'index function';
    }
    public function create(){
        echo 'create function';
    }
    public function edit($id){
        echo 'edit function with pram: '.$id ;
    }
    public function store(){
        echo 'store function with request: ' ;
    }
    public function show($id){
        echo 'show function with pram: '.$id ;
    }
    public function destroy($id)
    {
        echo 'delete function with pram: '.$id ;
    }
    public function update(int $id){
        echo 'update function with pram: '.$id ;
    }


}