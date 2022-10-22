<?php

namespace App\Controllers;

use App\Services\UserService;
use Exception;
use Src\Http\Request;
use Src\Http\Server;
use Src\Routing\Router;

class UserController
{
    public function __construct(private readonly UserService $service){}

    public function index()
    {
        return $this->service->index();
    }

    public function create()
    {
        return Router::getByNameWithBinding('users.index');
    }

    public function edit($id)
    {
        return 'edit function with pram: ' . $id;
    }

    public function store()
    {
        return 'store function with request: ';
    }

    public function show($name,Request $request)
    {
        return 'show function with pram: ' . $name ." with uri {$request->uri()} " ;
    }

    public function destroy($id)
    {
        return 'delete function with pram: ' . $id;
    }

    public function update(int $id)
    {
        return 'update function with pram: ' . $id;
    }


}