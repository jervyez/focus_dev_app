<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('client_supply', ['namespace' => 'App\Modules\Client_supply\Controllers'], function($subroutes){

  /*** Routes for Client Supply Module ***/
  $subroutes->get(''                            , 'Client_supply::index');
  $subroutes->get('client_supply'               , 'Client_supply::index');
  $subroutes->post('process_form_supply'        , 'Client_supply::process_form_supply');

  $subroutes->post('view_supply'        , 'Client_supply::view_supply');
  $subroutes->get('view_supply'        , 'Client_supply::view_supply');
  $subroutes->get('view_supply/(:any)'                       , 'Client_supply::view_supply/$1' );
  $subroutes->post('view_supply/(:any)'                       , 'Client_supply::view_supply/$1' );

  $subroutes->post('update_form_supply'        , 'Client_supply::update_form_supply');


  $subroutes->get('delete_supply/(:any)'                       , 'Client_supply::delete_supply/$1' );
  $subroutes->post('upload_photos'        , 'Client_supply::upload_photos');

  
  $subroutes->get('set_as_arrived/(:any)'                       , 'Client_supply::set_as_arrived/$1' );
  $subroutes->get('set_as_delivered/(:any)'                       , 'Client_supply::set_as_delivered/$1' );



});