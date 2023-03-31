<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('invoice', ['namespace' => 'App\Modules\Invoice\Controllers'], function($subroutes){

  /*** Routes for Users Module ***/
  $subroutes->get(''                                    , 'Invoice::index');
  $subroutes->get('invoice'                             , 'Invoice::index');
  $subroutes->get('get_project_invoiced'                , 'Invoice::get_project_invoiced');
  $subroutes->get('project_invoice/(:any)'              , 'Invoice::project_invoice/$1');
  $subroutes->get('job_book/(:any)'                     , 'Invoice::job_book/$1');
  $subroutes->post('delete_all_invoices'                , 'Invoice::delete_all_invoices');
  $subroutes->get('delete_all_invoices'                 , 'Invoice::delete_all_invoices');
  $subroutes->post('insert_invoice_progress'            , 'Invoice::insert_invoice_progress');
  $subroutes->post('set_invoice_progress'               , 'Invoice::set_invoice_progress');
  $subroutes->post('un_invoice_item'                    , 'Invoice::un_invoice_item');




  $subroutes->get('set_invoice_progress'               , 'Invoice::set_invoice_progress');
  $subroutes->post('progress_payment'                    , 'Invoice::progress_payment');
  $subroutes->post('get_paid_result'                    , 'Invoice::get_paid_result');
  $subroutes->post('list_payment_history'                    , 'Invoice::list_payment_history');
  $subroutes->post('remove_recent_payment'                    , 'Invoice::remove_recent_payment');

  $subroutes->add('set_invoice_vr/(:any)/(:any)'                     , 'Invoice::set_invoice_vr/$1/$2');

  


});