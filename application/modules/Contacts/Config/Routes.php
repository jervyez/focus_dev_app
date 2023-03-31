<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('contacts', ['namespace' => 'App\Modules\Contacts\Controllers'], function($subroutes){

  /*** Routes for Users Module ***/
  $subroutes->get(''                                 , 'Contacts::index');
  $subroutes->get('contacts'                         , 'Contacts::index');

//  $subroutes->add('sum_total_wip_cost'          , 'Client_supply::sum_total_wip_cost');




});