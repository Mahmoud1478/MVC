<?php

namespace Src\Bootstrap;
use Exception;
use Src\Cookie\Cookie;
use Src\Exceptions\Whoops ;
use Src\Http\Request;
use Src\Http\Server;
use Src\Router\Route;
use Src\Session\Session;

class App
{
    private function __construct() {}


    public static function run(){
        /* register whoops*/
        Whoops::handle();

        /* start session */
        Session::start();

        /* start cokes */

        /* request handle */
        Request::handle();

        require_once '../routes/web.php';
        Route::prefix('api',function (){
            require_once '../routes/api.php';
        });
        /* handle route */

        /* handle response*/
        Route::resolve();



    }

}