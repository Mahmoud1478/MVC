<?php

namespace Src\Bootstrap;

use ReflectionException;
use Src\Container\Exceptions\ContainerException;
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
        $this->router = new Router($this);
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function run(): void
    {
        $value = $this->router->resolve();
        if ($value){
            if (gettype($value) == 'string' ){
                echo  $value;
            }else{
                print_r(json_encode($value,MYSQLI_TYPE_JSON) );
            }
        }
    }

}