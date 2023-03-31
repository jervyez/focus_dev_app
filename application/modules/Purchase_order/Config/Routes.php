<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('purchase_order', ['namespace' => 'App\Modules\Purchase_order\Controllers'], function($subroutes){

  /*** Routes for Purchase_order Module ***/
  $subroutes->get(''                                       , 'Purchase_order::index');
  $subroutes->get('purchase_order'                         , 'Purchase_order::index');

  $subroutes->post('po_review_process'                      , 'Purchase_order::po_review_process');
  $subroutes->post('po_history'                      , 'Purchase_order::po_history');
  $subroutes->post('no_insurance_send_email'                      , 'Purchase_order::no_insurance_send_email');
  $subroutes->post('insert_work_invoice'                      , 'Purchase_order::insert_work_invoice');
  $subroutes->post('remove_last_trans'                      , 'Purchase_order::remove_last_trans');
  $subroutes->post('get_reconciled_result'                      , 'Purchase_order::get_reconciled_result');
  $subroutes->post('purchase_order_filtered'                      , 'Purchase_order::purchase_order_filtered');
  $subroutes->get('purchase_order_filtered'                      , 'Purchase_order::purchase_order_filtered');


  
});