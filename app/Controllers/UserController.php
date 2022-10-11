<?php

namespace App\Controllers;

use App\Services\UserService;
use Src\Cookie\Cookie;
use Src\Http\Request;
use Src\Routing\Router;

class UserController
{
    public function __construct(private readonly UserService $service)
    {
    }

    public function index()
    {
        return  $this->service->index();
    }
    public function create(){
        return 'create function';
    }
    public function edit($id){
        return 'edit function with pram: '.$id ;
    }
    public function store(){
        return 'store function with request: ' ;
    }
    public function show($id){
        return 'show function with pram: '.$id ;
    }
    public function destroy($id)
    {
        return 'delete function with pram: '.$id ;
    }
    public function update(int $id){
        return 'update function with pram: '.$id ;
    }


}