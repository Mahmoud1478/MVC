<?php

namespace Src\Bootstrap;

use Src\Container\ServiceContainer;
use Src\Cookie\Cookie;
use Src\Exceptions\Whoops;
use Src\Http\Request;
use Src\Http\Server;
use Src\Routing\Router;
use Src\Session\Session;

class App extends ServiceContainer
{
    public Router $router;

    public function __construct()
    {
        $this->boot();
    }

    public function boot(): void
    {
        Whoops::handle();
        $this->bootstrapRouter();
    }



    private function bootstrapRouter(): void
    {
        Router::init($this);
    }

    public function run(): void
    {
        $value = Router::resolve();
        if ($value){
            if (gettype($value) == 'string' ){
                echo  $value;
            }else{
                print_r(json_encode($value,MYSQLI_TYPE_JSON) );
            }
        }
    }

}