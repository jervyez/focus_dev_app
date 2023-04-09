<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('projects', ['namespace' => 'App\Modules\Projects\Controllers'], function($subroutes){

  /*** Routes for Projects Module ***/
  $subroutes->get(''                                        , 'Projects::index');
  $subroutes->get('projects'                                , 'Projects::index');
  $subroutes->add('getProject_PR_images'                    , 'Projects::getProject_PR_images' );
  $subroutes->get('set_warranty'                            , 'Projects::set_warranty' );
  $subroutes->get('read_csv_logs'                           , 'Projects::read_csv_logs' );
  $subroutes->get('list_project_comments'                   , 'Projects::list_project_comments' );
  $subroutes->get('list_project_comments/(:any)'            , 'Projects::list_project_comments/$1' );
  $subroutes->post('list_project_comments'                  , 'Projects::list_project_comments' );
  $subroutes->post('add_project_comment'                    , 'Projects::add_project_comment' );
  $subroutes->get('rem_commnt/(:any)'                       , 'Projects::rem_commnt/$1' );
  $subroutes->post('project_comments_deleted'               , 'Projects::project_comments_deleted' );
  $subroutes->post('add_brand'                              , 'Projects::add_brand' );
  $subroutes->post('delete_brand'                           , 'Projects::delete_brand' );
  $subroutes->post('update_brand'                           , 'Projects::update_brand' );
  $subroutes->get('projects_wip_review'                     , 'Projects::projects_wip_review' );
  $subroutes->post('set_date_review'                        , 'Projects::set_date_review' );
  $subroutes->post('fetch_mark_up_by'                       , 'Projects::fetch_mark_up_by' );
  $subroutes->get('fetch_mark_up_by'                        , 'Projects::fetch_mark_up_by' );
  $subroutes->get('document_storage'                        , 'Projects::document_storage' );
  $subroutes->get('document_storage/(:any)'                 , 'Projects::document_storage/$1' );
  $subroutes->get('client_file_storage/(:any)'              , 'Projects::client_file_storage/$1' );
  $subroutes->get('client_file_storage'                     , 'Projects::client_file_storage' );

  $subroutes->get('list_projects_by_job_date/(:any)'        , 'Projects::list_projects_by_job_date/$1' );
  $subroutes->post('add_doc_type'                           , 'Projects::add_doc_type' );
  $subroutes->get('delete_doc_type/(:any)'                  , 'Projects::delete_doc_type/$1' );
  $subroutes->post('update_doc_type'                        , 'Projects::update_doc_type' );

  $subroutes->get('view/(:any)'                             , 'Projects::view/$1' );
  $subroutes->get('view/(:any)/(:any)'                      , 'Projects::view/$1/$2');
  $subroutes->get('view/(:any)/(:any)/(:any)'               , 'Projects::view/$1/$2/$3');
  $subroutes->post('quick_update'                           , 'Projects::quick_update' );
  $subroutes->post('list_uploaded_files'                    , 'Projects::list_uploaded_files' );

  $subroutes->post('fetch_project_required_doc_type_file'   , 'Projects::fetch_project_required_doc_type_file' );
  $subroutes->post('check_doc_type_is_required'             , 'Projects::check_doc_type_is_required' );

  $subroutes->get('process_upload_file_storage'             , 'Projects::process_upload_file_storage' );
  $subroutes->post('process_upload_file_storage'            , 'Projects::process_upload_file_storage' );
  $subroutes->post('save_invoice_comments'                  , 'Projects::save_invoice_comments' );

  $subroutes->get('add'                                     , 'Projects::add' );
  $subroutes->post('add'                                    , 'Projects::add' );
  
  $subroutes->post('set_pa'                                 , 'Projects::set_pa' );
  $subroutes->post('set_jurisdiction'                       , 'Projects::set_jurisdiction' );
  $subroutes->get('set_jurisdiction/(:any)'                 , 'Projects::set_jurisdiction/$1' );
  $subroutes->get('set_jurisdiction'                        , 'Projects::set_jurisdiction' );

  $subroutes->get('find_contact_person'                     , 'Projects::find_contact_person' );
  $subroutes->get('find_contact_person/(:any)'              , 'Projects::find_contact_person/$1' );
  $subroutes->post('find_contact_person'                    , 'Projects::find_contact_person' );

  $subroutes->post('fetch_address_company_invoice'          , 'Projects::fetch_address_company_invoice' );

  $subroutes->get('set_jurisdiction_shoping_center'         , 'Projects::set_jurisdiction_shoping_center' );
  $subroutes->get('set_jurisdiction_shoping_center/(:any)'  , 'Projects::set_jurisdiction_shoping_center/$1' );
  $subroutes->post('set_jurisdiction_shoping_center'        , 'Projects::set_jurisdiction_shoping_center' );

  $subroutes->get('add_company_project'                     , 'Projects::add_company_project' );
  $subroutes->post('add_company_project'                    , 'Projects::add_company_project' );

  $subroutes->get('update_project_details'                  , 'Projects::update_project_details' );
  $subroutes->get('update_project_details/(:any)'           , 'Projects::update_project_details/$1' );
  $subroutes->post('update_project_details'                 , 'Projects::update_project_details' );
  $subroutes->post('update_project_details/(:any)'          , 'Projects::update_project_details/$1' );


  $subroutes->post('update_feedback'                        , 'Projects::update_feedback' );
  $subroutes->get('delete_project/(:any)'                   , 'Projects::delete_project/$1' );
  $subroutes->post('fetch_project_total_values'             , 'Projects::fetch_project_total_values' );

  $subroutes->post('get_work_list'                          , 'Projects::get_work_list' );
  $subroutes->post('get_project_site_labour_cost'           , 'Projects::get_project_site_labour_cost' );




});