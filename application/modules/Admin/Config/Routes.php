<?php

if(!isset($routes)){ 
    $routes = \Config\Services::routes(true);
}

$routes->group('admin', ['namespace' => 'App\Modules\Admin\Controllers'], function($subroutes){

  /*** Routes for Admin Module ***/
  $subroutes->get(''                                          , 'Admin::index');
  $subroutes->get('admin'                                     , 'Admin::index');
  $subroutes->add('admin/(:any)'                              , 'Admin::index/$1');
  $subroutes->post('matrix'                                   , 'Admin::matrix');
  $subroutes->post('labour_cost_matrix'                       , 'Admin::labour_cost_matrix');
  $subroutes->post('joinery_selected_user'                    , 'Admin::joinery_selected_user');
  $subroutes->post('fetch_users_list_table'                   , 'Admin::fetch_users_list_table');
  $subroutes->post('display_license_cert'                     , 'Admin::display_license_cert');
  $subroutes->post('display_required_license_cert'            , 'Admin::display_required_license_cert');


  $subroutes->post('fetch_exempted_project_list'              , 'Admin::fetch_exempted_project_list');
  $subroutes->post('fetch_exempted_postcode'                  , 'Admin::fetch_exempted_postcode');
  $subroutes->post('fetch_all_roles'                          , 'Admin::fetch_all_roles');
  $subroutes->post('fetch_default_doc_storage'                , 'Admin::fetch_default_doc_storage');
  $subroutes->post('list_doc_type'                            , 'Admin::list_doc_type');
  
  $subroutes->post('fetch_doc_storage_required_notification'  , 'Admin::fetch_doc_storage_required_notification');
  $subroutes->post('project_mark_up'                          , 'Admin::project_mark_up');
  $subroutes->post('defaults'                                 , 'Admin::defaults');
  $subroutes->post('warranty_categories'                      , 'Admin::warranty_categories');
  $subroutes->post('warranty_setup'                           , 'Admin::warranty_setup');
  $subroutes->post('default_unaccepted_projects'              , 'Admin::default_unaccepted_projects');
  $subroutes->post('default_progress_report'                  , 'Admin::default_progress_report');
  $subroutes->post('default_labour_schedule'                  , 'Admin::default_labour_schedule');
  $subroutes->post('update_cat_pcc'                           , 'Admin::update_cat_pcc');
  $subroutes->post('default_induction_project'                , 'Admin::default_induction_project');
  $subroutes->post('new_arc_doc_type'                         , 'Admin::new_arc_doc_type');
  $subroutes->post('archive_documents_settings'               , 'Admin::archive_documents_settings');
  $subroutes->post('set_assignmnt_doc_type'                   , 'Admin::set_assignmnt_doc_type');
  $subroutes->post('update_arc_doc_type'                      , 'Admin::update_arc_doc_type');


  $subroutes->get('delete_archive_type/(:any)'                , 'Admin::delete_archive_type/$1');
  $subroutes->post('update_archive_details'                   , 'Admin::update_archive_details');



  $subroutes->get('del_arch_det/(:any)'                       , 'Admin::del_arch_det/$1');
  $subroutes->post('update_emp_supply_remnd'                  , 'Admin::update_emp_supply_remnd');
  $subroutes->post('update_lead_days'                         , 'Admin::update_lead_days');
  $subroutes->post('update_weeks_delivery'                    , 'Admin::update_weeks_delivery');
  $subroutes->post('set_company_warehouse'                    , 'Admin::set_company_warehouse');
  $subroutes->post('add_feedback'                             , 'Admin::add_feedback');
  $subroutes->post('edit_feedback'                            , 'Admin::edit_feedback');
  $subroutes->get('del_feedback/(:any)'                       , 'Admin::del_feedback/$1');


  $subroutes->post('update_fbck_success_email'                , 'Admin::update_fbck_success_email');
  $subroutes->post('update_fbck_unsuccessful_email'           , 'Admin::update_fbck_unsuccessful_email');
  $subroutes->post('update_fbck_unaccepted_s_email'           , 'Admin::update_fbck_unaccepted_s_email');
  $subroutes->post('update_fbck_unaccepted_us_email'          , 'Admin::update_fbck_unaccepted_us_email');


  $subroutes->post('invoice_email'                            , 'Admin::invoice_email');
  $subroutes->post('default_notes'                            , 'Admin::default_notes');
  $subroutes->post('update_cqr_template'                      , 'Admin::update_cqr_template');
  $subroutes->post('update_reminder_settings'                 , 'Admin::update_reminder_settings');
  $subroutes->post('update_cc_emails'                         , 'Admin::update_cc_emails');


  
  $subroutes->get('cc_static_delete_email/(:any)/(:any)'      , 'Admin::cc_static_delete_email/$1/$2');
  $subroutes->post('upload_email_banner'                      , 'Admin::upload_email_banner');
  $subroutes->post('default_email_message_induction'          , 'Admin::default_email_message_induction');
  $subroutes->post('default_email_message'                    , 'Admin::default_email_message');
  $subroutes->post('default_email_message_onboarding_clients' , 'Admin::default_email_message_onboarding_clients');
  $subroutes->post('default_email_message_onboarding'         , 'Admin::default_email_message_onboarding');
  $subroutes->post('default_email_message_onboarding_bank'    , 'Admin::default_email_message_onboarding_bank');
  $subroutes->post('pdf_do_upload'                            , 'Admin::pdf_do_upload');
  $subroutes->post('onboarding_contractor_msg'                , 'Admin::onboarding_contractor_msg');
  $subroutes->post('onboarding_email'                         , 'Admin::onboarding_email');
  $subroutes->post('default_email_message_onboarding_notif'   , 'Admin::default_email_message_onboarding_notif');
  $subroutes->post('onboarding_general_msg'                   , 'Admin::onboarding_general_msg');
  $subroutes->post('update_allowed_user_rempend_comp'         , 'Admin::update_allowed_user_rempend_comp');
  $subroutes->get('rem_allowed_id/(:any)'                     , 'Admin::rem_allowed_id/$1');



  $subroutes->post('onboarding_workplace_health_safety'       , 'Admin::onboarding_workplace_health_safety');
  $subroutes->post('onboarding_swms'                          , 'Admin::onboarding_swms');
  $subroutes->post('onboarding_jsa'                           , 'Admin::onboarding_jsa');
  $subroutes->post('onboarding_reviewed_swms'                 , 'Admin::onboarding_reviewed_swms');
  $subroutes->post('onboarding_safety_related_convictions'    , 'Admin::onboarding_safety_related_convictions');



  $subroutes->get('company'                                   , 'Admin::company');

  $subroutes->post('user_settings'                            , 'Admin::user_settings');
  $subroutes->post('salaried_rates'                           , 'Admin::salaried_rates');
  $subroutes->post('wages_rates'                              , 'Admin::wages_rates');
  $subroutes->post('manila_rates'                             , 'Admin::manila_rates');
  $subroutes->post('update_leave_notice'                      , 'Admin::update_leave_notice');
  $subroutes->post('update_leave_emails'                      , 'Admin::update_leave_emails');
  $subroutes->post('po_rev_settings'                          , 'Admin::po_rev_settings');
  $subroutes->post('insert_rate_set'                          , 'Admin::insert_rate_set');
  $subroutes->post('insert_employee_rate'                     , 'Admin::insert_employee_rate');
  $subroutes->post('fetch_selected_rate_set'                  , 'Admin::fetch_selected_rate_set');
//  $subroutes->get('fetch_selected_rate_set/(:any)'            , 'Admin::fetch_selected_rate_set/$1');


  $subroutes->post('update_selected_rate_set'                 , 'Admin::update_selected_rate_set');
  $subroutes->post('remove_selected_rate_set'                 , 'Admin::remove_selected_rate_set');
  $subroutes->post('fetch_assigned_employee_rate'             , 'Admin::fetch_assigned_employee_rate');
  $subroutes->post('update_employee_rate'                     , 'Admin::update_employee_rate');
  $subroutes->post('remove_employee_rate'                     , 'Admin::remove_employee_rate');
  $subroutes->post('location_assignments'                     , 'Admin::location_assignments');
  $subroutes->post('user_location'                            , 'Admin::user_location');
  $subroutes->post('update_closing_settings'                  , 'Admin::update_closing_settings');
  $subroutes->post('new_season'                               , 'Admin::new_season');


  $subroutes->get('delete_bg/(:any)'                          , 'Admin::delete_bg/$1');
  $subroutes->get('set_background/(:any)'                     , 'Admin::set_background/$1');
  $subroutes->post('update_bg_login'                          , 'Admin::update_bg_login');
  $subroutes->post('upload_signin_bg'                         , 'Admin::upload_signin_bg');
  $subroutes->post('add_exempted_project'                     , 'Admin::add_exempted_project');


  $subroutes->get('admin_company/(:any)'                      , 'Admin::admin_company/$1');
  $subroutes->get('admin_company'                             , 'Admin::admin_company');
  $subroutes->post('admin_company'                            , 'Admin::admin_company');
  $subroutes->post('admin_company/(:any)'                     , 'Admin::admin_company/$1');
  $subroutes->post('update_abn_acn_jurisdiction'              , 'Admin::update_abn_acn_jurisdiction');
  $subroutes->get('delete_company/(:any)'                     , 'Admin::delete_company/$1');




  $subroutes->post('add'                                      , 'Admin::add');
  $subroutes->get('add'                                       , 'Admin::add');



  $subroutes->post('default_email_message_onboarding_approved_clients'   , 'Admin::default_email_message_onboarding_approved_clients');
  $subroutes->post('default_email_message_onboarding_approved'           , 'Admin::default_email_message_onboarding_approved');
  $subroutes->post('default_email_message_onboarding_declined_clients'   , 'Admin::default_email_message_onboarding_declined_clients');
  $subroutes->post('default_email_message_onboarding_declined'           , 'Admin::default_email_message_onboarding_declined');
  $subroutes->post('onboarding_confirm_licences_certifications'          , 'Admin::onboarding_confirm_licences_certifications');
  $subroutes->get('display_all_job_category_type'                        , 'Admin::display_all_job_category_type');



 

});