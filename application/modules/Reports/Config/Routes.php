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

  $subroutes->get('test'                            , 'Reports::test');


});