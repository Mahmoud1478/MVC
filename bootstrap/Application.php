<?php
define('ROOT',realpath(__DIR__.'/..'));
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

$app = new App();


$app->router->group([],function (Router $router){
    require ROOT.'/routes/web.php';
});

return $app;

