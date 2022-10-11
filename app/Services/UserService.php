<?php

namespace App\Services;

use Src\Http\Request;

class UserService
{
    public function __construct(private readonly Request $request)
    {}
    public function index(): string
    {
        return $this->request::method();
    }


}