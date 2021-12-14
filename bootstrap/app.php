<?php

use Src\Bootstrap\App;

class Application{

    private function __construct() {}

    public static function run(){
        define('ROOT',realpath(__DIR__.'../'));
        define('DS',DIRECTORY_SEPARATOR);
        App::run();
    }
}
