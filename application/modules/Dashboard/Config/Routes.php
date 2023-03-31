<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('dashboard', ['namespace' => 'App\Modules\Dashboard\Controllers'], function($subroutes){

  /*** Routes for Dashboard Module ***/
  $subroutes->get(''                                                , 'Dashboard::index');
  $subroutes->get('dashboard'                                       , 'Dashboard::index');
  $subroutes->get('users_availability'                              , 'Dashboard::users_availability');


  $subroutes->get('focus_company_sep_thermo/(:any)/(:any)'          , 'Dashboard::focus_company_sep_thermo/$1/$2');
  $subroutes->get('focus_company_sep_thermo/(:any)/(:any)/(:any)'   , 'Dashboard::focus_company_sep_thermo/$1/$2/$3');

  $subroutes->get('progressBar/(:any)/(:any)/(:any)'                , 'Dashboard::progressBar/$1/$2/$3');
  $subroutes->get('progressBar/(:any)'                              , 'Dashboard::progressBar/$1');
  $subroutes->get('sales_widget'                                    , 'Dashboard::sales_widget');
  $subroutes->get('uninvoiced_widget'                               , 'Dashboard::uninvoiced_widget');
  $subroutes->get('outstanding_payments_widget'                     , 'Dashboard::outstanding_payments_widget');
  $subroutes->get('wip_widget'                                      , 'Dashboard::wip_widget');
  $subroutes->get('wid_quoted'                                      , 'Dashboard::wid_quoted');
  $subroutes->get('wid_quoted/(:any)'                               , 'Dashboard::wid_quoted/$1');
  $subroutes->get('wid_site_labour_hrs'                             , 'Dashboard::wid_site_labour_hrs');
  $subroutes->get('focus_projects_by_type_widget'                   , 'Dashboard::focus_projects_by_type_widget');

  $subroutes->get('focus_top_ten_con_sup/(:any)'                    , 'Dashboard::focus_top_ten_con_sup/$1');
  $subroutes->get('pm_sales_widget/(:any)'                          , 'Dashboard::pm_sales_widget/$1');
  $subroutes->get('pm_sales_widget'                                 , 'Dashboard::pm_sales_widget');

  $subroutes->get('maintanance_average'                             , 'Dashboard::maintanance_average');


  $subroutes->get('average_date_invoice/(:any)/(:any)/(:any)'       , 'Dashboard::average_date_invoice/$1/$2/$3');
  $subroutes->get('average_date_invoice'                            , 'Dashboard::average_date_invoice');

  $subroutes->get('focus_projects_count_widget/(:any)'              , 'Dashboard::focus_projects_count_widget/$1');
  $subroutes->get('focus_projects_count_widget'                     , 'Dashboard::focus_projects_count_widget');
  $subroutes->get('focus_get_po_widget'                             , 'Dashboard::focus_get_po_widget');


  $subroutes->get('focus_top_ten_clients'                           , 'Dashboard::focus_top_ten_clients');
  $subroutes->get('pm_estimates_widget'                             , 'Dashboard::pm_estimates_widget');
  $subroutes->get('pm_estimates_widget/(:any)'                      , 'Dashboard::pm_estimates_widget/$1');


  $subroutes->get('focus_top_ten_con_sup_mn'                        , 'Dashboard::focus_top_ten_con_sup_mn');
  $subroutes->get('focus_top_ten_con_sup_mn/(:any)'                 , 'Dashboard::focus_top_ten_con_sup_mn/$1');
  $subroutes->get('focus_top_ten_clients_mn'                        , 'Dashboard::focus_top_ten_clients_mn');
  $subroutes->get('focus_top_ten_clients_mn/(:any)'                 , 'Dashboard::focus_top_ten_clients_mn/$1');


  $subroutes->get('get_count_per_week'                              , 'Dashboard::get_count_per_week');

  $subroutes->get('invoiced_pa'                                     , 'Dashboard::invoiced_pa');
  $subroutes->get('outstanding_payments_widget_pa'                  , 'Dashboard::outstanding_payments_widget_pa');
  $subroutes->get('uninvoiced_widget_pa'                            , 'Dashboard::uninvoiced_widget_pa');
  $subroutes->get('pm_sales_widget_pa/(:any)'                       , 'Dashboard::pm_sales_widget_pa/$1');
  $subroutes->get('pm_sales_widget_pa'                              , 'Dashboard::pm_sales_widget_pa');
  $subroutes->get('wip_widget_pa'                                   , 'Dashboard::wip_widget_pa');
  $subroutes->get('focus_get_po_widget_pa'                          , 'Dashboard::focus_get_po_widget_pa');
  $subroutes->get('pm_estimates_widget_pa'                          , 'Dashboard::pm_estimates_widget_pa');
  $subroutes->get('focus_projects_count_widget_pa'                  , 'Dashboard::focus_projects_count_widget_pa');
  $subroutes->get('average_date_invoice_pa'                         , 'Dashboard::average_date_invoice_pa');





  $subroutes->get('estimators'                                      , 'Estimators::index');

  $subroutes->get('estimators/up_coming_deadline'                   , 'Estimators::up_coming_deadline');
  $subroutes->get('estimators/up_coming_deadline/(:any)'            , 'Estimators::up_coming_deadline/$1');
  $subroutes->get('estimators/up_coming_deadline/(:any)/(:any)'     , 'Estimators::up_coming_deadline/$1/$2');

  $subroutes->get('estimators/estimators_wip'                       , 'Estimators::estimators_wip');
  $subroutes->get('estimators/estimators_wip/(:any)'                , 'Estimators::estimators_wip/$1');


  $subroutes->get('estimators/completed_prjs'                       , 'Estimators::completed_prjs');
  $subroutes->get('estimators/completed_prjs/(:any)'                , 'Estimators::completed_prjs/$1');
  $subroutes->get('estimators/estimators_quotes_completed'          , 'Estimators::estimators_quotes_completed');
  $subroutes->get('estimators/estimators_quotes_completed/(:any)'   , 'Estimators::estimators_quotes_completed/$1');




});