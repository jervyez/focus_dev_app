<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('induction_health_safety', ['namespace' => 'App\Modules\Induction_health_safety\Controllers'], function($subroutes){

  /*** Routes for Induction_health_safety Module ***/
  $subroutes->get(''                                  , 'Induction_health_safety::index');
  $subroutes->get('induction_health_safety'           , 'Induction_health_safety::index');
 
  $subroutes->add('fetch_user_licences_certificates'              , 'Induction_health_safety::fetch_user_licences_certificates' );
  $subroutes->add('fetch_induction_projects_list'                 , 'Induction_health_safety::fetch_induction_projects_list' );
  $subroutes->post('fetch_state'                                  , 'Induction_health_safety::fetch_state' );
  $subroutes->post('fetch_cont_sitestaff_emergency_contacts'      , 'Induction_health_safety::fetch_cont_sitestaff_emergency_contacts' );
 
 $subroutes->add('archive_documents'                       , 'Induction_health_safety::archive_documents' );

 $subroutes->add('view_uploaded_files_arch/(:any)'                       , 'Induction_health_safety::view_uploaded_files_arch/$1' );

 $subroutes->add('view_uploaded_files_arch/(:any)/(:any)'                       , 'Induction_health_safety::view_uploaded_files_arch/$1/$2' );

 $subroutes->add('remove_archive_doc/(:any)'                       , 'Induction_health_safety::remove_archive_doc/$1' );

 $subroutes->post('upload_docs_ind'                       , 'Induction_health_safety::upload_docs_ind' );

 $subroutes->post('view_focus_site_staff'                       , 'Induction_health_safety::view_focus_site_staff' );

 $subroutes->post('get_user_site_staff'                       , 'Induction_health_safety::get_user_site_staff' );

  $subroutes->post('fetch_user_emergency_contacts'                 , 'Induction_health_safety::fetch_user_emergency_contacts' );

  $subroutes->post('fetch_cont_sitestaff_licences_certificates'   , 'Induction_health_safety::fetch_cont_sitestaff_licences_certificates' );

  $subroutes->post('fetch_user_training_records'                   , 'Induction_health_safety::fetch_user_training_records' );

  $subroutes->post('fetch_cont_sitestaff_training_records'        , 'Induction_health_safety::fetch_cont_sitestaff_training_records' );

   $subroutes->post('fetch_license_cert_type'                       , 'Induction_health_safety::fetch_license_cert_type' );

   $subroutes->post('fetch_contractors'                       , 'Induction_health_safety::fetch_contractors' );

   $subroutes->post('fetch_contractors_with_sitestaff'                       , 'Induction_health_safety::fetch_contractors_with_sitestaff' );

   $subroutes->post('add_emergency_contact'                       , 'Induction_health_safety::add_emergency_contact' );

   $subroutes->post('update_emergency_contact'                       , 'Induction_health_safety::update_emergency_contact' );

   $subroutes->post('remove_emergency_contacts'                       , 'Induction_health_safety::remove_emergency_contacts' );
   
  $subroutes->post('add_licence_cert'                       , 'Induction_health_safety::add_licence_cert' );

  $subroutes->post('update_licence_cert'                       , 'Induction_health_safety::update_licence_cert' );

  $subroutes->post('remove_licence_cert'                       , 'Induction_health_safety::remove_licence_cert' );
  
  $subroutes->post('add_training'                       , 'Induction_health_safety::add_training' );

  $subroutes->post('update_training'                       , 'Induction_health_safety::update_training' );

  $subroutes->post('remove_training'                       , 'Induction_health_safety::remove_training' );

  $subroutes->post('insert_lc_type'                       , 'Induction_health_safety::insert_lc_type' );

  $subroutes->post('get_temporary_cont_site_staff'                       , 'Induction_health_safety::get_temporary_cont_site_staff' );
  $subroutes->post('fetch_cont_sitestaff_submitted'                       , 'Induction_health_safety::fetch_cont_sitestaff_submitted' );
  $subroutes->post('fetch_temp_sitestaff'                       , 'Induction_health_safety::fetch_temp_sitestaff' );
  $subroutes->post('fetch_temp_lc'                       , 'Induction_health_safety::fetch_temp_lc' );
  $subroutes->post('fetch_temp_training'                       , 'Induction_health_safety::fetch_temp_training' );
  $subroutes->post('fetch_temp_contractors'                       , 'Induction_health_safety::fetch_temp_contractors' );

  $subroutes->post('sending_email_default'                       , 'Induction_health_safety::sending_email_default' );

  $subroutes->post('send_email'                       , 'Induction_health_safety::send_email' );

  $subroutes->post('send_cont_site_staff_update'                       , 'Induction_health_safety::send_cont_site_staff_update' );

  $subroutes->post('approve_updates'                       , 'Induction_health_safety::approve_updates' );

   $subroutes->post('approve_updates_site_staff'                       , 'Induction_health_safety::approve_updates_site_staff' );

   $subroutes->add('induction_slide_editor_view'                       , 'Induction_health_safety::induction_slide_editor_view' );
   
   $subroutes->post('fetch_induction_projects_details'                       , 'Induction_health_safety::fetch_induction_projects_details' );

   $subroutes->post('fetch_induction_is_generated'                       , 'Induction_health_safety::fetch_induction_is_generated' );

   $subroutes->post('fetch_induction_slide_detials'                       , 'Induction_health_safety::fetch_induction_slide_detials' );

   $subroutes->post('fetch_induction_slide_project_details'                       , 'Induction_health_safety::fetch_induction_slide_project_details' );

   $subroutes->post('update_induction_slide_project_outline'                       , 'Induction_health_safety::update_induction_slide_project_outline' );

   $subroutes->post('update_induction_slide_site_hours'                       , 'Induction_health_safety::update_induction_slide_site_hours' );

   $subroutes->add('set_upload_options/(:any)'                       , 'Induction_health_safety::set_upload_options/$1' );

   $subroutes->add('set_upload_options_videos/(:any)'                       , 'Induction_health_safety::set_upload_options_videos/$1' );

   $subroutes->add('upload_videos'                       , 'Induction_health_safety::upload_videos' );

   $subroutes->add('upload_access'                       , 'Induction_health_safety::upload_access' );

   $subroutes->add('upload_amenities'                       , 'Induction_health_safety::upload_amenities' );

   $subroutes->post('update_induction_slide_emergency'                       , 'Induction_health_safety::update_induction_slide_emergency' );

   $subroutes->post('update_induction_slide_ppe'                       , 'Induction_health_safety::update_induction_slide_ppe' );

   $subroutes->add('generated_selected_pdf'                       , 'Induction_health_safety::generated_selected_pdf' );

   $subroutes->post('set_cleared_slides'                       , 'Induction_health_safety::set_cleared_slides' );



   $subroutes->post('upload_brand_logo'                       , 'Induction_health_safety::upload_brand_logo' );
   $subroutes->post('get_brand_logo'                          , 'Induction_health_safety::get_brand_logo' );
   




   
});