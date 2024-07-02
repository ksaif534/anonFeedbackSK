<?php

spl_autoload_register(function($className){
    $baseDir = __DIR__ . '/' .'classes/';
    require_once $baseDir . $className . '.php';
});

?>