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
    public function store(Request $request){
        echo 'store function with request: ' ;
        echo '<pre>';
        print_r($request::all());
        echo '</pre>';

    }
    public function show($id){
        echo 'show function with pram: '.$id ;
    }
    public function delete($id){
        echo 'delete function with pram: '.$id ;
    }
    public function update(int $id,array $request){
        echo 'update function with pram: '.$id ;
        echo '<pre>';
        print_r($request);
        echo '</pre>';
    }


}