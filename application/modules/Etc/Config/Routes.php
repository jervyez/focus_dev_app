<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('etc', ['namespace' => 'App\Modules\Etc\Controllers'], function($subroutes){

  /*** Routes for Etc Module ***/
  $subroutes->get(''                            , 'Etc::index');
  $subroutes->get('etc'                         , 'Etc::index');

});