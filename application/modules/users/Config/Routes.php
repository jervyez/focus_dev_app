<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('users', ['namespace' => 'App\Modules\Users\Controllers'], function($subroutes){

  /*** Routes for Users Module ***/
  $subroutes->get(''                            , 'Users::index');
  $subroutes->get('users'                       , 'Users::index');

  $subroutes->add('signin'                      , 'Users::signin');
  $subroutes->add('signin/(:any)'               , 'Users::signin/$1');

  $subroutes->add('logout'                      , 'Users::logout');
  $subroutes->get('test_partial'                , 'Users::test_partial');

  $subroutes->add('account'                     , 'Users::account');
  $subroutes->add('account/(:any)'              , 'Users::account/$1');

  $subroutes->add('company_matrix'              , 'Users::company_matrix');

  $subroutes->add('login_users'                 , 'Users::login_users');
  $subroutes->post('check_user_if_remembered'   , 'Users::check_user_if_remembered');



  $subroutes->add('add'                         , 'Users::add');

  $subroutes->add('set_user_log'                , 'Users::set_user_log');
  
  $subroutes->get('availability'                , 'Users::availability');


  $subroutes->post('update_leave_alloc'         , 'Users::update_leave_alloc');
  $subroutes->post('update_leave_alloc/(:any)'  , 'Users::update_leave_alloc/$1');

  $subroutes->get('delete_user'                 , 'Users::delete_user');
  $subroutes->get('delete_user/(:any)'          , 'Users::delete_user/$1');

  $subroutes->post('fetch_user_access'          , 'Users::fetch_user_access');
  $subroutes->get('fetch_user_access/(:any)'    , 'Users::fetch_user_access/$1');
  
  $subroutes->post('update_user_access'         , 'Users::update_user_access');
  
  $subroutes->post('update_projects_default_view', 'Users::update_projects_default_view');
  
  $subroutes->post('update_projects_pv'         , 'Users::update_projects_pv');
  $subroutes->post('update_menu_order'          , 'Users::update_menu_order');


  $subroutes->post('set_availability'          , 'Users::set_availability');
  $subroutes->get('set_availability/(:any)'    , 'Users::set_availability/$1');

  $subroutes->post('reset_availability'        , 'Users::reset_availability');
  $subroutes->get('reset_availability'        , 'Users::reset_availability');

 
  $subroutes->get('leave_details/(:any)'        , 'Users::leave_details/$1');
  $subroutes->get('leave_approvals/(:any)'      , 'Users::leave_approvals/$1');



  $subroutes->post('fetch_all_leave_dates'         , 'Users::fetch_all_leave_dates');
  $subroutes->post('check_pending_leave_count'         , 'Users::check_pending_leave_count');


  $subroutes->get('set_user_log_min'                , 'Users::set_user_log_min');
  $subroutes->post('set_user_log_min'                , 'Users::set_user_log_min');

  $subroutes->add('apply_leave'                , 'Users::apply_leave');
  $subroutes->add('apply_leave/(:any)'        , 'Users::apply_leave/$1');

  $subroutes->add('approve_leave'                , 'Users::approve_leave');
  $subroutes->add('approve_leave/(:any)'        , 'Users::approve_leave/$1');


  $subroutes->get('cancel_leave/(:any)/(:any)'   , 'Users::cancel_leave/$1/$2');
  
  $subroutes->post('delete_user_ava'         , 'Users::delete_user_ava');

  $subroutes->get('generate_leave_form/(:any)/(:any)'   , 'Users::generate_leave_form/$1/$2');


  $subroutes->add('disapproved_leave'         , 'Users::disapproved_leave');
  $subroutes->add('disapproved_leave/(:any)'        , 'Users::disapproved_leave/$1');


  $subroutes->post('set_availability_reoccur'         , 'Users::set_availability_reoccur');
  $subroutes->get('set_availability_reoccur'         , 'Users::set_availability_reoccur');


  $subroutes->post('delete_user_ava_rec'         , 'Users::delete_user_ava_rec');



  $subroutes->add('add_leave_alloc'                , 'Users::add_leave_alloc');
  $subroutes->add('add_leave_alloc/(:any)'        , 'Users::add_leave_alloc/$1');


  $subroutes->get('user_logs'   , 'Users::user_logs');



});