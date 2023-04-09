<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('reports', ['namespace' => 'App\Modules\Reports\Controllers'], function($subroutes){

  /*** Routes for reports Module ***/
  $subroutes->get(''                                , 'Reports::index');
  $subroutes->get('reports'                         , 'Reports::index');
  $subroutes->get('company_report'                  , 'Reports::company_report');
  $subroutes->post('company_report'                 , 'Reports::company_report');

  $subroutes->get('wip_report'                      , 'Reports::wip_report');
  $subroutes->post('wip_report'                     , 'Reports::wip_report');

  $subroutes->get('pdf/(:any)/(:any)/(:any)'        , 'Reports::pdf/$1/$2/$3');
  $subroutes->post('pdf'                            , 'Reports::pdf');

  $subroutes->get('purchase_order_report'           , 'Reports::purchase_order_report');
  $subroutes->post('purchase_order_report'          , 'Reports::purchase_order_report');

  $subroutes->get('invoice_report'                  , 'Reports::invoice_report');
  $subroutes->post('invoice_report'                 , 'Reports::invoice_report');

  $subroutes->get('client_supply_report'            , 'Reports::client_supply_report');
  $subroutes->post('client_supply_report'           , 'Reports::client_supply_report');


  $subroutes->get('contacts_gen/(:any)'             , 'Reports::contacts_gen/$1');
  $subroutes->get('myob_names'                      , 'Reports::myob_names');





});