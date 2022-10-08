<?php
define('ROOT',realpath(__DIR__.'../'));
const DS = DIRECTORY_SEPARATOR;
require_once '../vendor/autoload.php';

/*
 *  1- register the autoloader
 *  2- init bootstrap the applications
 *  4- run the Application
 * */

use Src\Bootstrap\App;
use Src\Bootstrap\AppContainer;
use Src\Cookie\Cookie;
use Src\Http\Request;
use Src\Http\Server;
use Src\Routing\Router;
use Src\Session\Session;

$container = new AppContainer();
$classes = [
    'session' => Session::class,
    'cookie' => Cookie::class,
    'server' => Server::class,
    'request' => Request::class,
];
foreach ($classes as $footprint => $class) {
    $container->set($footprint, $class);
}


$app = new App($container);

Router::group([],function (){
    require __DIR__.'/../routes/web.php';
});

return $app;

