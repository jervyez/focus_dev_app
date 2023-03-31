<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('dev_notes', ['namespace' => 'App\Modules\Dev_notes\Controllers'], function($subroutes){

  /*** Routes for DevNotes Module ***/
  $subroutes->get(''                            , 'Dev_notes::index');
  $subroutes->get('dev_notes'                   , 'Dev_notes::index');

  $subroutes->post('add_section'                , 'Dev_notes::add_section');
  $subroutes->post('update_section'             , 'Dev_notes::update_section');

  $subroutes->get('delete_section/(:any)'       , 'Dev_notes::delete_section/$1');
  $subroutes->post('post'                       , 'Dev_notes::post');

  $subroutes->get('view_post/(:any)'            , 'Dev_notes::view_post/$1');
  $subroutes->post('update_post'                , 'Dev_notes::update_post');
  $subroutes->post('post_comment'               , 'Dev_notes::post_comment');

  $subroutes->get('delete_post/(:any)'          , 'Dev_notes::delete_post/$1');




});