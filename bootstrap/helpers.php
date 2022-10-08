<?php

use JetBrains\PhpStorm\NoReturn;
use Src\Bootstrap\AppContainer;
use Src\Http\Request;
use Src\Routing\Router;

function request() : Request
{
    return AppContainer::get('request');
}
function router () :Router
{
    return AppContainer::get('router');
}

function route(string $name, array $prams =[]) :string
{
    return AppContainer::get('router')->getByName($name,$prams);
}

 #[NoReturn] function dd(...$vars): void
{
    foreach ($vars as $var){
        echo '<pre>';
        if (gettype($var) == 'string'){
            echo $var ;
        }else{
            var_dump($var);
        }
        echo '</pre>';
    }
    die();
}