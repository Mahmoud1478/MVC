<?php

namespace Src\Bootstrap;

use Src\Cookie\Cookie;
use Src\Exceptions\Whoops;
use Src\Http\Request;
use Src\Http\Server;
use Src\Routing\Router;
use Src\Session\Session;

class App
{

    private static  AppContainer $container;
    public Router $router;

    public function __construct(AppContainer $container)
    {
        static::$container = $container;
        $this->boot();
    }

    public function boot(): void
    {
        Whoops::handle();
        $this->bootstrapRouter();
    }



    public static function get(string $footprint)
    {
        return static::$container->get($footprint);
    }

    private function bootstrapRouter(): void
    {
        Router::init();
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