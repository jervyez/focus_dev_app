<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('dashboard', ['namespace' => 'App\Modules\Dashboard\Controllers'], function($subroutes){

  /*** Routes for Dashboard Module ***/
  $subroutes->get(''                                                , 'Dashboard::index');
  $subroutes->get('dashboard'                                       , 'Dashboard::index');
  $subroutes->get('dashboard/(:any)'                                , 'Dashboard::progressBar/$1');


  $subroutes->get('users_availability'                              , 'Dashboard::users_availability');


  $subroutes->get('focus_company_sep_thermo/(:any)/(:any)'          , 'Dashboard::focus_company_sep_thermo/$1/$2');
  $subroutes->get('focus_company_sep_thermo/(:any)/(:any)/(:any)'   , 'Dashboard::focus_company_sep_thermo/$1/$2/$3');

  $subroutes->get('progressBar/(:any)/(:any)/(:any)'                , 'Dashboard::progressBar/$1/$2/$3');
  $subroutes->get('progressBar/(:any)/(:any)       '                , 'Dashboard::progressBar/$1/$2');
  $subroutes->get('progressBar/(:any)'                              , 'Dashboard::progressBar/$1');
  $subroutes->get('progressBar'                                     , 'Dashboard::progressBar');

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
  $subroutes->get('focus_get_po_widget/(:any)'                      , 'Dashboard::focus_get_po_widget/$1');


  $subroutes->get('focus_top_ten_clients'                           , 'Dashboard::focus_top_ten_clients');
  $subroutes->get('pm_estimates_widget'                             , 'Dashboard::pm_estimates_widget');
  $subroutes->get('pm_estimates_widget/(:any)'                      , 'Dashboard::pm_estimates_widget/$1');


  $subroutes->get('focus_top_ten_con_sup_mn'                        , 'Dashboard::focus_top_ten_con_sup_mn');
  $subroutes->get('focus_top_ten_con_sup_mn/(:any)'                 , 'Dashboard::focus_top_ten_con_sup_mn/$1');
  $subroutes->get('focus_top_ten_clients_mn'                        , 'Dashboard::focus_top_ten_clients_mn');
  $subroutes->get('focus_top_ten_clients_mn/(:any)'                 , 'Dashboard::focus_top_ten_clients_mn/$1');


  $subroutes->get('get_count_per_week'                              , 'Dashboard::get_count_per_week');

  $subroutes->get('get_count_per_week/(:any)/(:any)/(:any)'         , 'Dashboard::get_count_per_week/$1/$2/$3');

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


  $subroutes->get('focus_top_ten_con_sup_pm/(:any)/(:any)'          , 'Dashboard::focus_top_ten_con_sup_pm/$1/$2');

  $subroutes->get('focus_top_ten_clients_pm'                        , 'Dashboard::focus_top_ten_clients_pm');
  $subroutes->get('focus_top_ten_clients_pm/(:any)'                 , 'Dashboard::focus_top_ten_clients_pm/$1');
  $subroutes->get('focus_top_ten_clients_pm/(:any)/(:any)'          , 'Dashboard::focus_top_ten_clients_pm/$1/$2');
  $subroutes->get('focus_top_ten_clients_pm/(:any)/(:any)/(:any)'   , 'Dashboard::focus_top_ten_clients_pm/$1/$2/$3');

  $subroutes->get('focus_top_ten_clients_pm/(:any)/(:any)/(:any)/(:any)'   , 'Dashboard::focus_top_ten_clients_pm/$1/$2/$3/$3');


  $subroutes->get('focus_projects_by_type_widget_pm'                , 'Dashboard::focus_projects_by_type_widget_pm');
  $subroutes->get('focus_projects_by_type_widget_pm/(:any)'         , 'Dashboard::focus_projects_by_type_widget_pm/$1');
  $subroutes->get('focus_projects_by_type_widget_pm/(:any)/(:any)'  , 'Dashboard::focus_projects_by_type_widget_pm/$1/$2');

  $subroutes->get('maintanance_average_pm'                          , 'Dashboard::maintanance_average_pm');
  $subroutes->get('maintanance_average_pm/(:any)'                   , 'Dashboard::maintanance_average_pm/$1');
  
  $subroutes->get('pm_estimates_widget_pm'                          , 'Dashboard::pm_estimates_widget_pm');
  $subroutes->get('pm_estimates_widget_pm/(:any)'                   , 'Dashboard::pm_estimates_widget_pm/$1');
  
  $subroutes->get('focus_projects_count_widget_pm'                  , 'Dashboard::focus_projects_count_widget_pm');
  $subroutes->get('focus_projects_count_widget_pm/(:any)'           , 'Dashboard::focus_projects_count_widget_pm/$1');
  
  $subroutes->get('average_date_invoice_pm'                         , 'Dashboard::average_date_invoice_pm');
  $subroutes->get('average_date_invoice_pm/(:any)'                  , 'Dashboard::average_date_invoice_pm/$1');

  $subroutes->get('pm_sales_widget_pm'                              , 'Dashboard::pm_sales_widget_pm');
  $subroutes->get('pm_sales_widget_pm/(:any)'                       , 'Dashboard::pm_sales_widget_pm/$1');
  $subroutes->get('pm_sales_widget_pm/(:any)/(:any)'                , 'Dashboard::pm_sales_widget_pm/$1/$2');



  $subroutes->get('wip_widget_pm'                                   , 'Dashboard::wip_widget_pm');
  $subroutes->get('wip_widget_pm/(:any)'                            , 'Dashboard::wip_widget_pm/$1');

  $subroutes->get('outstanding_payments_widget_pm'                  , 'Dashboard::outstanding_payments_widget_pm');
  $subroutes->get('outstanding_payments_widget_pm/(:any)'           , 'Dashboard::outstanding_payments_widget_pm/$1');

  $subroutes->get('uninvoiced_widget_pm'                            , 'Dashboard::uninvoiced_widget_pm');
  $subroutes->get('uninvoiced_widget_pm/(:any)'                     , 'Dashboard::uninvoiced_widget_pm/$1');

  $subroutes->get('invoiced_pm'                                     , 'Dashboard::invoiced_pm');
  $subroutes->get('invoiced_pm/(:any)'                              , 'Dashboard::invoiced_pm/$1');

  $subroutes->get('focus_top_ten_clients_pm_donut'                  , 'Dashboard::focus_top_ten_clients_pm_donut');
  $subroutes->get('focus_top_ten_clients_pm_donut/(:any)'           , 'Dashboard::focus_top_ten_clients_pm_donut/$1');

  $subroutes->get('sales_forecast'                                  , 'Dashboard::sales_forecast');
  $subroutes->get('sales_forecast/(:any)'                           , 'Dashboard::sales_forecast/$1');
  $subroutes->post('sales_forecast/(:any)'                          , 'Dashboard::sales_forecast/$1');



  $subroutes->post('set_forecast_form'                              , 'Dashboard::set_forecast_form');
  $subroutes->get('delete_forecast/(:any)'                          , 'Dashboard::delete_forecast/$1');
  $subroutes->get('set_primary_forecast/(:any)'                     , 'Dashboard::set_primary_forecast/$1');
  $subroutes->get('management_report/(:any)'                        , 'Dashboard::management_report/$1');
  $subroutes->post('update_forecast_form'                           , 'Dashboard::update_forecast_form');




  

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