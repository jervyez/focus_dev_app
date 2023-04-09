<?php
// module created by Jervy 23-9-2022
namespace App\Modules\Induction_health_safety\Controllers;

use App\Controllers\BaseController;

use App\Modules\Induction_health_safety\Models\Induction_health_safety_m;

use App\Modules\Admin\Controllers\Admin;
use App\Modules\Admin\Models\Admin_m;

use App\Modules\Company\Models\Company_m;

class Induction_health_safety extends BaseController {

  private $induction_health_safety_m;

  function __construct(){

    $this->induction_health_safety_m = new Induction_health_safety_m();

  }

  public function index() {
    $this->admin_m = new Admin_m();
    if( $this->session->get('is_admin') ==  1 || $this->session->get('user_id') == 6 || $this->session->get('user_id') == 28  ){
      
    }elseif( $this->session->get('induction_archive_upload') == 1){

       return redirect()->to('/induction_health_safety/archive_documents');
    }
    else{
       return redirect()->to('/projects');
    }

    $fetch_archive_types = $this->admin_m->get_archive_types();
    $data['archive_types'] = $fetch_archive_types->getResult();

    $data['tab'] = $this->session->get('tab');
    $data['page_title'] = 'Induction Health and Safety';
    // $data['main_content'] = 'inductions_v';
 
    $data['screen'] = 'Induction, Health and Safety';
    // $this->load->view('page', $data);

    $data['main_content'] = 'App\Modules\Induction_health_safety\Views\inductions_v';
    return view('App\Views\page',$data);
    
  }


  public function archive_documents(){
    $this->admin_m = new Admin_m();

    if( $this->session->get('is_admin') ==  1 || $this->session->get('user_id') == 6 || $this->session->get('user_id') == 32    || $this->session->get('induction_archive_upload') == 1){
      
    }else{
       return redirect()->to('/projects');    // use this just to controll access
    }

    $user_id = $this->session->get('user_id');

    if($this->session->get('is_admin') == 1 ){
      $fetch_archive_types = $this->admin_m->get_archive_types();
      $data['archive_types_for_upload'] = $fetch_archive_types->getResult();
    }else{
      $fetch_fetch_archive_assigned_to_emp = $this->admin_m->fetch_archive_assigned_to_emp($user_id);
      $data['archive_types_for_upload'] = $fetch_fetch_archive_assigned_to_emp->getResult();
    }

    $data['tab'] = $this->session->get('tab');
    // $data['main_content'] = 'inductions_archives';
    $data['screen'] = 'Induction, Health and Safety';
    $data['main_content'] = 'App\Modules\Induction_health_safety\Views\inductions_archives';
    return view('App\Views\page',$data);
    //$this->load->view('page', $data);
  }

  public function view_uploaded_files_arch($type_id,$view_old=0){
    $this_year = date("Y");
    $last_year = $this_year - 1;

    if($view_old == 1){
      $set_year = $last_year;
    }else{
      $set_year = $this_year;
    }



    $fetch_archive_files = $this->induction_health_safety_m->list_uploaded_files_arch($type_id,$set_year);
    $archive_files = $fetch_archive_files->getResult();

    foreach($archive_files as $key => $archive_data){
      echo '<p>
      <a href="'.base_url().'/docs/doc_archives/'.$archive_data->file_name.'" target="_blank"><i class="fa fa-chevron-circle-right fa-lg"></i> &nbsp'.$archive_data->file_name.'</a>
      <span class="pull-right"><i class="fa fa-info-circle fa-lg tooltip-enabled pointer" title="" data-html="true" data-placement="top" data-original-title="Uploaded Date '.$archive_data->date.'"></i></span>
       &nbsp; <a href="'.base_url().'/induction_health_safety/remove_archive_doc/'.$archive_data->archive_documents_id.'" class="for_admin" id=""><i class="fa fa-times-circle fa-lg" style="color:red;"></i></a>
      </p>';
    }

  }

  public function remove_archive_doc($doc_id){
    $this->induction_health_safety_m->delete_archive($doc_id);
    return redirect()->to('/induction_health_safety/archive_documents');
  }

  public function upload_brand_logo(){
    $brand_id = $this->request->getPost('brand_id');

    if ($this->request->getFileMultiple('userfile')){
      foreach($this->request->getFileMultiple('userfile') as $file){ // loop through files

        $file_name      = $file->getName();
        $file_name_arr  = explode('.',$file_name);
        $file_name_raw  = $file_name_arr[0];
        $file_ext       = $file_name_arr[1];

        
        $data_file_name = $brand_id.'.'.$file_ext;
        $path = "./uploads/brand_logo/";
        $set_file = $path.$data_file_name;

        if(file_exists($set_file)){   
          unlink($set_file);
        }

        $file_name_set = str_replace(' ', '_', $data_file_name);
        $file_name_set_final = str_replace("'", '`', $file_name_set);
        $file_name_amp = str_replace('&', '_and_', $file_name_set_final);

        if ($file->isValid() && !$file->hasMoved()) {
          $file->move(ROOTPATH . $path.'.', $data_file_name);
          $this->induction_health_safety_m->update_brand($brand_id);
        }
      } // loop through files
    }


    return redirect()->to('/projects');

    
  }

  public function get_brand_logo(){
    $brand_id = $this->request->getPost('brand_id');
    $query = $this->induction_health_safety_m->get_brand_logo($brand_id);
    echo $query;
  }

  public function upload_docs_ind(){
    $date = date("d/m/Y");    
    $time = time();
    $counter = 1;

    $archive_registry_types = $_POST['archive_registry_types'];
    $archive_registry_name =    substr( strtolower(  str_replace(' ','_',  $_POST['archive_registry_name'] )) ,  0, 5);
    $user_id = $this->session->get('user_id');

    $path = "./docs/doc_archives";
    if(!is_dir($path)){
      mkdir($path, 0755, true);
    }







    if ($this->request->getFileMultiple('archive_files')){
      foreach($this->request->getFileMultiple('archive_files') as $file){ // loop through files

        $file_name      = $file->getName();
        $file_name_arr  = explode('.',$file_name);
        $file_name_raw  = $file_name_arr[0];
        $file_ext       = $file_name_arr[1];

        // $file_name_set = str_replace(' ', '_', $data_file_name);
        // $file_name_set_final = str_replace("'", '`', $file_name_set);
        // $file_name_amp = str_replace('&', '_and_', $file_name_set_final);

        $data_file_name = $archive_registry_name.'_'.$time.'_'.$counter.'.'.$file_ext;

        if ($file->isValid() && !$file->hasMoved()) {
          $file->move(ROOTPATH . $path.'/', $data_file_name);
          $this->induction_health_safety_m->insert_uploaded_file($archive_registry_types,$user_id,$date,$data_file_name);
          $counter++;

        }else{
          $upload_error = $file->getError();
          exit;
        }

      } // loop through files
      
      $this->induction_health_safety_m->update_archive_expiry($user_id, $archive_registry_types);
    }

/*

    $files = $_FILES;
    $cpt = count($_FILES['archive_files']['name']);
    for($i=0; $i<$cpt; $i++){   

      $file_name = $files['archive_files']['name'][$i];
      $path_parts = pathinfo($file_name);
      $extension = strtolower($path_parts['extension']);


      $data_file_name = $archive_registry_name.'_'.$time.'_'.$i.'.'.$extension;
      $_FILES['archive_files']['name']= $data_file_name;//$files['archive_files']['name'][$i];
      $_FILES['archive_files']['type']= $files['archive_files']['type'][$i];
      $_FILES['archive_files']['tmp_name']= $files['archive_files']['tmp_name'][$i];
      $_FILES['archive_files']['error']= $files['archive_files']['error'][$i];
      $_FILES['archive_files']['size']= $files['archive_files']['size'][$i];    



    }
*/
    //  $new_expiry_date = date('d/m/Y', strtotime('+12 months'));


    return redirect()->to('/induction_health_safety/archive_documents');
  }

  public function view_focus_site_staff(){
    $query = $this->user_model->get_users_sitestaff();
    echo json_encode($query->getResult());
  }

  public function get_user_site_staff(){
    $project_id = $_POST['project_id'];
    $query = $this->induction_health_safety_m->get_user_site_staff($project_id);
    echo json_encode($query->getResult());
  }

  public function fetch_user_emergency_contacts(){
    $query = $this->induction_health_safety_m->fetch_user_emergency_contacts();

    echo json_encode($query->getResult());
  }

  public function fetch_user_licences_certificates(){
    $query = $this->induction_health_safety_m->fetch_user_licences_certificates();
    echo json_encode($query->getResult());
  }

  public function fetch_cont_sitestaff_licences_certificates(){
    $query = $this->induction_health_safety_m->fetch_cont_sitestaff_licences_certificates();
    echo json_encode($query->getResult());
  }

  public function fetch_user_training_records(){
    $query = $this->induction_health_safety_m->fetch_user_training_records();
    echo json_encode($query->getResult());
  }

  public function fetch_cont_sitestaff_training_records(){
    $query = $this->induction_health_safety_m->fetch_cont_sitestaff_training_records();
    echo json_encode($query->getResult());
  }

  public function fetch_license_cert_type(){
    $query = $this->induction_health_safety_m->fetch_license_cert_type();
    echo json_encode($query->getResult());
  }

  public function fetch_contractors(){
    $query = $this->company_m->fetch_all_company_details_active();
    echo json_encode($query->getResult());
  }

  public function fetch_contractors_with_sitestaff(){
    $company_id = $_POST['company_id'];
    $query = $this->company_m->fetch_contractors_with_sitestaff($company_id);
    echo json_encode($query->getResult());
  }

  public function add_emergency_contact(){
    $is_contractors = $_POST['is_contractors'];
    $user_id = $_POST['user_id'];
    $ecFName = $_POST['ecFName'];
    $ecSName = $_POST['ecSName'];
    $ecRelation = $_POST['ecRelation'];
    $ecContacts = $_POST['ecContacts'];

    $query = $this->induction_health_safety_m->add_emergency_contact($user_id,$ecFName,$ecSName,$ecRelation,$ecContacts,$is_contractors);

    if($is_contractors == '0'){
      $query = $this->induction_health_safety_m->fetch_user_emergency_contacts();
    }else{
      $query = $this->induction_health_safety_m->fetch_cont_sitestaff_emergency_contacts();
    }

    echo json_encode($query->getResult());
    //redirect('/induction_health_safety');
  }

  public function update_emergency_contact(){
    $sitestaff_emergency_contacts_id = $_POST['sitestaff_emergency_contacts_id'];
    $ecFName = $_POST['ecFName'];
    $ecSName = $_POST['ecSName'];
    $ecRelation = $_POST['ecRelation'];
    $ecContacts = $_POST['ecContacts'];
    $is_contractors = $_POST['is_contractors'];
    
    $query = $this->induction_health_safety_m->update_emergency_contact($sitestaff_emergency_contacts_id,$ecFName,$ecSName,$ecRelation,$ecContacts);
    
    if($is_contractors == '0'){
      $query = $this->induction_health_safety_m->fetch_user_emergency_contacts();
    }else{
      $query = $this->induction_health_safety_m->fetch_cont_sitestaff_emergency_contacts();
    }

    echo json_encode($query->getResult());

    //redirect('/induction_health_safety');
  }

  public function remove_emergency_contacts(){
    $sitestaff_emergency_contacts_id = $_POST['sitestaff_emergency_contacts_id'];
    $is_contractors = $_POST['is_contractors'];

    $query = $this->induction_health_safety_m->remove_emergency_contact($sitestaff_emergency_contacts_id);

    if($is_contractors == '0'){
      $query = $this->induction_health_safety_m->fetch_user_emergency_contacts();
    }else{
      $query = $this->induction_health_safety_m->fetch_cont_sitestaff_emergency_contacts();
    }

    echo json_encode($query->getResult());
  }

  public function add_licence_cert(){
    $is_contractors = $_POST['is_contractors'];
    $user_id = $_POST['user_id'];
    $LCtype = $_POST['LCtype'];
    $LCName = $_POST['LCName'];
    $lcNumber = $_POST['lcNumber'];
    $has_expiration = $_POST['has_expiration'];
    $expirationDate = $_POST['expirationDate'];
    $first_dose = $_POST['first_dose'];
    $second_dose = $_POST['second_dose'];

    $query = $this->induction_health_safety_m->add_licence_cert($user_id,$LCtype,$LCName,$lcNumber,$expirationDate,$is_contractors,$has_expiration,$first_dose,$second_dose);


    if($is_contractors == '0'){
      $query = $this->induction_health_safety_m->fetch_user_licences_certificates();
    }else{
      $query = $this->induction_health_safety_m->fetch_cont_sitestaff_licences_certificates();
    }

    echo json_encode($query->getResult());
  }

  public function update_licence_cert(){
    $is_contractors = $_POST['is_contractors'];
    $user_license_certificates_id = $_POST['user_license_certificates_id'];
    $LCtype = $_POST['LCtype'];
    $LCName = $_POST['LCName'];
    $lcNumber = $_POST['lcNumber'];
    $has_expiration = $_POST['has_expiration'];
    $expirationDate = $_POST['expirationDate'];
    $first_dose = $_POST['first_dose'];
    $second_dose = $_POST['second_dose'];

    $query = $this->induction_health_safety_m->update_licence_cert($user_license_certificates_id,$LCtype,$LCName,$lcNumber,$expirationDate,$has_expiration,$first_dose,$second_dose);
    if($is_contractors == '0'){
      $query = $this->induction_health_safety_m->fetch_user_licences_certificates();
    }else{
      $query = $this->induction_health_safety_m->fetch_cont_sitestaff_licences_certificates();
    }

    echo json_encode($query->getResult());
  }

  public function remove_licence_cert(){
    $is_contractors = $_POST['is_contractors'];
    $user_license_certificates_id = $_POST['user_license_certificates_id'];
    $query = $this->induction_health_safety_m->remove_licence_cert($user_license_certificates_id);
    if($is_contractors == '0'){
      $query = $this->induction_health_safety_m->fetch_user_licences_certificates();
    }else{
      $query = $this->induction_health_safety_m->fetch_cont_sitestaff_licences_certificates();
    }

    echo json_encode($query->getResult());
  }

  public function add_training(){
    $is_contractors = $_POST['is_contractors'];
    $user_id = $_POST['user_id'];
    $trainingName = $_POST['trainingName'];
    $trainingDate = $_POST['trainingDate'];
    $trainingLoc = $_POST['trainingLoc'];

    $query = $this->induction_health_safety_m->add_training($user_id,$trainingName,$trainingDate,$trainingLoc,$is_contractors);

    if($is_contractors == '0'){
      $query = $this->induction_health_safety_m->fetch_user_training_records();
    }else{
      $query = $this->induction_health_safety_m->fetch_cont_sitestaff_training_records();
    }
    
    echo json_encode($query->getResult());

  }

  public function update_training(){
    $is_contractors = $_POST['is_contractors'];
    $training_records_id = $_POST['training_records_id'];
    $trainingName = $_POST['trainingName'];
    $trainingDate = $_POST['trainingDate'];
    $trainingLoc = $_POST['trainingLoc'];

    $query = $this->induction_health_safety_m->update_training($training_records_id,$trainingName,$trainingDate,$trainingLoc);

    if($is_contractors == '0'){
      $query = $this->induction_health_safety_m->fetch_user_training_records();
    }else{
      $query = $this->induction_health_safety_m->fetch_cont_sitestaff_training_records();
    }
    
    echo json_encode($query->getResult());
  }

  public function remove_training(){
    $is_contractors = $_POST['is_contractors'];
    $training_records_id = $_POST['training_records_id'];

    $query = $this->induction_health_safety_m->remove_training($training_records_id);

    if($is_contractors == '0'){
      $query = $this->induction_health_safety_m->fetch_user_training_records();
    }else{
      $query = $this->induction_health_safety_m->fetch_cont_sitestaff_training_records();
    }
    
    echo json_encode($query->getResult());
  }

  public function insert_lc_type(){
    $lctypename = $_POST['lctypename'];

    $query = $this->induction_health_safety_m->add_lc_type($lctypename);

    $query = $this->induction_health_safety_m->fetch_license_cert_type();
    
    echo json_encode($query->getResult());

  }

  public function fetch_cont_sitestaff_emergency_contacts(){
    $query = $this->induction_health_safety_m->fetch_cont_sitestaff_emergency_contacts();

    echo json_encode($query->getResult());
  }
  
  public function get_temporary_cont_site_staff(){
    $query = $this->induction_health_safety_m->temp_cont_site_staff_submitted();
    echo $query->num_rows;
  }

  public function fetch_cont_sitestaff_submitted(){
    $query = $this->induction_health_safety_m->temp_cont_site_staff();
    echo json_encode($query->getResult());
  }

  public function fetch_temp_sitestaff(){
    $query = $this->induction_health_safety_m->fetch_temp_sitestaff();
    echo json_encode($query->getResult());
  }

  public function fetch_temp_lc(){
    $query = $this->induction_health_safety_m->fetch_temp_lc();
    echo json_encode($query->getResult());
  }

  public function fetch_temp_training(){
    $query = $this->induction_health_safety_m->fetch_temp_training();
    echo json_encode($query->getResult());
  }

  public function fetch_temp_contractors(){
    $query = $this->induction_health_safety_m->fetch_temp_contractors();
    echo json_encode($query->getResult());
  }

  public function sending_email_default(){
    $this->admin_m = new Admin_m();
    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('induction-new');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      
      $induction_sender_name = $row['sender_name'];
      $induction_sender_email = $row['sender_email'];
      $induction_bcc_email = $row['bcc_email'];
      $induction_subject = $row['subject'];
      $induction_message_content = $row['message_content'];
      $induction_assigned_user = $row['user_id'];
      
    }

    $q_admin_default_email_message = $this->admin_m->fetch_admin_default_email_message('induction-update');
    foreach ($q_admin_default_email_message->getResultArray() as $row){
      $induction_message_content_update = $row['message_content'];
      
    }

    echo $induction_sender_name."|".$induction_sender_email."|".$induction_bcc_email."|".$induction_subject."|".$induction_message_content."|".$induction_assigned_user."|".$induction_message_content_update;  
  }

  public function send_email(){
    require_once('PHPMailer/class.phpmailer.php');
    require_once('PHPMailer/PHPMailerAutoload.php');

    $chk_sel_cont_sending = $_POST['chk_sel_cont_sending'];
    $cc = $_POST['cc'];
    $bcc = $_POST['bcc'];
    $subject = $_POST['subject'];
    $message = nl2br($_POST['message']);
    $message = str_replace('  ', ' &nbsp;', $message);
    $message = str_replace('•', '*', $message);

    $sender_name = $_POST['sender_name'];
    $sender_email = $_POST['sender_email'];
    $assigned_user_id = $_POST['assigned_user_id'];

    $email_sent = 0;
    $email_unsent = 0;
    foreach ($chk_sel_cont_sending as &$value) {
        $company_id = $value;
        $comp_q = $this->company_m->fetch_contact_details_primary($company_id);
      foreach ($comp_q->getResultArray() as $row){
        $company_email = $row['general_email'];

        $mail = new phpmailer(true);
        $mail->host = "sojourn-focusshopfit-com-au.mail.protection.outlook.com";
        $mail->port = 587;

        $mail->setFrom($sender_email, $sender_name);
        
        // $addr = explode(',',$email_to);
        // $count_addr = count($addr);
        // if($count_addr > 1){
        //  foreach ($addr as $ad) {
        //    $mail->addAddress( trim($ad) );  
        //  }
        // }else{
          // $mail->addAddress($email_to);
        // }
        
              // Add a recipient
        $mail->addAddress($company_email);               // Name is optional
        //$mail->addAddress('mark.obis2012@gmail.com');
        $mail->addReplyTo($sender_email);
        
        if($cc !== ''){
          $mail->addCC($cc);
        }
        //$mail->addCC($cc);
        // $addr = explode(',',$email_cc);
        // $count_addr = count($addr);
        // if($count_addr > 1){
        //  foreach ($addr as $ad) {
        //    $mail->addCC( trim($ad) );  
        //  }
        // }else{
        //  $mail->addCC($email_cc);
        // }
        

        $email_bcc_arr =  explode(',', $bcc);
        $no_arr = count($email_bcc_arr);
        $x = 0;
        while($x < $no_arr){
          $email_bcc = $email_bcc_arr[$x];
          $mail->addBCC($email_bcc);
          $x++;
        }
        $mail->addBCC('mark.obis2012@gmail.com');
        $mail->addBCC('safety@focusshopfit.com.au');
        //$mail->addBCC($email_bcc);
          

        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = $subject;
        $data['company_id'] = $company_id;
        $data['baseurl'] = base_url();
        $data['message'] = $message;
        $data['sender'] = $sender_name;
        $data['send_email'] = $sender_email;
       
        $message_body = $this->load->view('message_v',$data,TRUE);


        $mail->Body    = $message_body;

        if(!$mail->send()) {
          $email_unsent++;
          //echo 'Message could not be sent.'.' Mailer Error: ' . $mail->ErrorInfo;
        } else {
          $email_sent++;
          
          $this->induction_health_safety_m->update_induction_email_sent($company_id);
          
          
        }
      }

      
    }
    
    echo $email_sent." Email Sent Successfully.".$email_unsent." Emails Not Sent" ;

  }

  public function send_cont_site_staff_update(){
    require_once('PHPMailer/class.phpmailer.php');
    require_once('PHPMailer/PHPMailerAutoload.php');

    $data = json_decode(file_get_contents("php://input"), true);
    $company_id = $data['company_id'];
    
    $subject = "Update Site Staff Details";

    $sender_name = "Sojourn Admin";
    $sender_email = 'admin@focusshopfit.com.au';

    $email_sent = 0;
    $email_unsent = 0;
      $comp_q = $this->company_m->fetch_contact_details_primary($company_id);
    foreach ($comp_q->getResultArray() as $row){
      $company_email = $row['general_email'];

      $mail = new phpmailer(true);
      $mail->host = "sojourn-focusshopfit-com-au.mail.protection.outlook.com";
      $mail->port = 587;

      $mail->setFrom($sender_email, $sender_name);
        
              // Add a recipient
      $mail->addAddress($company_email);               // Name is optional
      //$mail->addAddress('ian@focusshopfit.com.au');
      $mail->addReplyTo($sender_email);
        
      
      $mail->addBCC('mark.obis2012@gmail.com');
      $mail->addBCC('safety@focusshopfit.com.au');
      //$mail->addBCC($email_bcc);
          

      $mail->isHTML(true);                                  // Set email format to HTML

      $mail->Subject = $subject;
      $data['company_id'] = $company_id;
      $data['baseurl'] = base_url();
      $data['message'] = "You have received this email because you are a valued CONTRACTOR to Focus Shopfit, and it’s been a year since you've updated your site staff details, OR some of your site staff licences have expired and require updating.  Please note that if any of this information becomes outdated our system prevents the issuing of a Contract Purchase Order until such time as it has been rectified.<br><br>

As a part of our Health and Safety program, it is a requirement that all people who come onto one of the Focus Shopfit construction sites has undertaken the Company Induction and all site staff details such as license and certificates are up to date. By accepting works on any company Construction Site you take responsibility to ensure all your staff, contractors and personnel have been made familiar with the Safe Construction Management Plan, have completed all inductions and agree to comply with all aspects of this plan. <br><br>

* Introduction to our Safe Construction Management Plan (Blue Book)<br>
* A video, Understanding Safety Awareness<br>
* A short questionnaire<br><br>

It will be your responsibility to monitor and ensure that none of your staff attend any Focus Shopfit site unless they have undertaken this induction. To get this process started, please follow the link and ADD or UPDATE all of your staff to the system. BEFORE commencing, please ensure that you have the following details for all staff:<br><br>

Name<br>
Mobile<br>
Email (you can set this to be a company contact if you like)<br>
Position<br>
Emergency contact: Name, mobile, relationship<br>
Licence details<br>
Training details<br><br>

Once all these are loaded, we will review, and an email invitation will be set through our service provider Safety Care with a link, username & password to undertake the company induction. When this has been completed we will register the user into our system, SOJOURN.<br><br>

Many thanks for your assistance, if you have any questions, please call the office and we can talk you through the process.<br><br>
";
      $data['sender'] = $sender_name;
      $data['send_email'] = $sender_email;
       
      $message_body = "";//$this->load->view('message_v',$data,TRUE);

      $mail->Body    = $message_body;

      if(!$mail->send()) {
        $email_unsent++;  
      } else {
        $email_sent++;
          
        $this->induction_health_safety_m->update_induction_email_sent($company_id);   
      }
    }
    echo $email_sent." Email Sent Successfully.".$email_unsent." Emails Not Sent" ;
  }

  public function approve_updates(){
    $this->company_m = new Company_m();

    $comp_id_arr = $_POST['comp_id'];

    foreach ($comp_id_arr as &$value) {
        $company_id = $value;
        $date_updated = date('Y-m-d');
        
        $query = $this->induction_health_safety_m->fetch_temp_cont_sitestaff($company_id);
      foreach ($query->getResultArray() as $row){
        $temp_contractors_staff_id = $row['temp_contractors_staff_id'];
        $staff_fname = $row['staff_fname'];
        $staff_sname = $row['staff_sname'];
        $position = $row['position'];
        $mobile_number = $row['mobile_number'];
        $email = $row['email'];
        $emergency_contact_fname = $row['emergency_contact_fname'];
        $emergency_contact_sname = $row['emergency_contact_sname'];
        $relation = $row['relation'];
        $emergency_contact_number = $row['emergency_contact_number'];
        $is_apprentice = $row['is_apprentice'];

        $contractor_site_staff_id = 0;
        $cont_query = $this->company_m->fetch_site_staff($company_id);
        foreach ($cont_query->getResultArray() as $cont_row){
          $cont_temp_contractors_staff_id = $cont_row['temp_contractors_staff_id'];
          if($cont_temp_contractors_staff_id == $temp_contractors_staff_id){
            $contractor_site_staff_id = $cont_row['contractor_site_staff_id'];
          }
        }

      
        if($contractor_site_staff_id == 0){
          $contractor_site_staff_id = $this->company_m->add_site_staff($company_id,$staff_fname,$staff_sname,$mobile_number,$position,$email,$is_apprentice,$temp_contractors_staff_id);
        }else{
          $this->induction_health_safety_m->update_site_staff($contractor_site_staff_id,$staff_fname,$staff_sname,$position,$mobile_number,$email,$company_id,$is_apprentice,$temp_contractors_staff_id);
        }

        $this->induction_health_safety_m->remove_site_staff_emergency_contact($contractor_site_staff_id);
        $this->induction_health_safety_m->add_emergency_contact($contractor_site_staff_id,$emergency_contact_fname,$emergency_contact_sname,$relation,$emergency_contact_number,1);
        
      // License and Cert start ====================
        $tlc_query = $this->induction_health_safety_m->fetch_temp_license_cert($temp_contractors_staff_id);
        foreach ($tlc_query->getResultArray() as $tlc_row){
          $tlc_type = $tlc_row['lc_type'];
          $is_license = $tlc_row['is_license'];
          $lc_number = $tlc_row['lc_number'];
          $lc_expiration_date = $tlc_row['lc_expiration_date'];
          $has_expiration = $tlc_row['has_expiration'];

          $lc_type = "";
          $user_license_certificates_id=0;
          $lc_query = $this->induction_health_safety_m->fetch_sitestaff_licences_certificates($contractor_site_staff_id);
          foreach ($lc_query->getResultArray() as $lc_row){
            $lc_type = $lc_row['type'];
            
            if($tlc_type == $lc_type){
              $user_license_certificates_id = $lc_row['user_license_certificates_id'];
            }
          }

          if($user_license_certificates_id == 0){
            $this->induction_health_safety_m->add_licence_cert($contractor_site_staff_id,$is_license,$tlc_type,$lc_number,$lc_expiration_date,1,$has_expiration);
          }else{
            $this->induction_health_safety_m->update_licence_cert($user_license_certificates_id,$is_license,$tlc_type,$lc_number,$lc_expiration_date,$has_expiration);
          }
          
          
        }

      // License and Cert end ====================

      //Trainings Start ============================
        $ttraining_query = $this->induction_health_safety_m->fetch_temp_trainings($temp_contractors_staff_id);
        foreach ($ttraining_query->getResultArray() as $ttraining_row){
          $temp_training = $ttraining_row['training'];
          $date_undertaken = $ttraining_row['date_undertaken'];
          $location = $ttraining_row['location'];

          $training_records_id = 0;
          $training_query = $this->induction_health_safety_m->fetch_sitestaff_training($contractor_site_staff_id);
          foreach ($training_query->getResultArray() as $training_row){
            $training_type = $training_row['training_type'];
            
            if($temp_training == $training_type){
              $training_records_id = $training_row['training_records_id'];
            }
          }

          if($training_records_id == 0){
            $this->induction_health_safety_m->add_training($contractor_site_staff_id,$temp_training,$date_undertaken,$location,1);
          }else{
            $this->induction_health_safety_m->update_training($training_records_id,$temp_training,$date_undertaken,$location);
          }

        }
      //Trainings End ==============================
        $this->induction_health_safety_m->update_induction_date_updated($company_id,$date_updated);
        $this->induction_health_safety_m->set_temp_data_approve($temp_contractors_staff_id);
      }
      }
  }

  public function approve_updates_site_staff(){
    $this->company_m = new Company_m();

    $site_staff_id_arr = $_POST['site_staff_id'];
    foreach ($site_staff_id_arr as &$value) {
        $temp_contractors_staff_id = $value;
        $query = $this->induction_health_safety_m->fetch_selected_temp_cont_sitestaff($temp_contractors_staff_id);
      foreach ($query->getResultArray() as $row){
        $company_id = $row['company_id'];
        $date_updated = date("Y-m-d");
        $this->induction_health_safety_m->update_induction_date_updated($company_id,$date_updated);
        $staff_fname = $row['staff_fname'];
        $staff_sname = $row['staff_sname'];
        $position = $row['position'];
        $mobile_number = $row['mobile_number'];
        $email = $row['email'];
        $emergency_contact_fname = $row['emergency_contact_fname'];
        $emergency_contact_sname = $row['emergency_contact_sname'];
        $relation = $row['relation'];
        $emergency_contact_number = $row['emergency_contact_number'];
        $is_apprentice = $row['is_apprentice'];

        $contractor_site_staff_id = 0;
        $cont_query = $this->company_m->fetch_site_staff($company_id);
        foreach ($cont_query->getResultArray() as $cont_row){
          $cont_temp_contractors_staff_id = $cont_row['temp_contractors_staff_id'];
          if($cont_temp_contractors_staff_id == $temp_contractors_staff_id){
            $contractor_site_staff_id = $cont_row['contractor_site_staff_id'];
          }
        }

      
        if($contractor_site_staff_id == 0){
          $contractor_site_staff_id = $this->company_m->add_site_staff($company_id,$staff_fname,$staff_sname,$mobile_number,$position,$email,$is_apprentice,$temp_contractors_staff_id);
        }else{
          $this->induction_health_safety_m->update_site_staff($contractor_site_staff_id,$staff_fname,$staff_sname,$position,$mobile_number,$email,$company_id,$is_apprentice,$temp_contractors_staff_id);
        }

        $this->induction_health_safety_m->temp_site_staff_unupdated($temp_contractors_staff_id);

        $this->induction_health_safety_m->remove_site_staff_emergency_contact($contractor_site_staff_id);
        $this->induction_health_safety_m->add_emergency_contact($contractor_site_staff_id,$emergency_contact_fname,$emergency_contact_sname,$relation,$emergency_contact_number,1);
        
      // License and Cert start ====================
        $tlc_query = $this->induction_health_safety_m->fetch_temp_license_cert($temp_contractors_staff_id);
        foreach ($tlc_query->getResultArray() as $tlc_row){
          $tlc_type = $tlc_row['lc_type'];
          $is_license = $tlc_row['is_license'];
          $lc_number = $tlc_row['lc_number'];
          $lc_expiration_date = $tlc_row['lc_expiration_date'];
          $has_expiration = $tlc_row['has_expiration'];
          $first_dose = $tlc_row['first_dose_date'];
          $second_dose = $tlc_row['sec_dose_date'];

          $lc_type = "";
          $user_license_certificates_id=0;
          $lc_query = $this->induction_health_safety_m->fetch_sitestaff_licences_certificates($contractor_site_staff_id);
          foreach ($lc_query->getResultArray() as $lc_row){
            $lc_type = $lc_row['type'];
            
            if($tlc_type == $lc_type){
              $user_license_certificates_id = $lc_row['user_license_certificates_id'];
            }
          }

          if($user_license_certificates_id == 0){
            $this->induction_health_safety_m->add_licence_cert($contractor_site_staff_id,$is_license,$tlc_type,$lc_number,$lc_expiration_date,1,$has_expiration,$first_dose,$second_dose);
          }else{
            $this->induction_health_safety_m->update_licence_cert($user_license_certificates_id,$is_license,$tlc_type,$lc_number,$lc_expiration_date,$has_expiration,$first_dose,$second_dose);
          }
          
          
        }

        $this->induction_health_safety_m->approve_certificate_file($temp_contractors_staff_id,$contractor_site_staff_id);

      // License and Cert end ====================

      //Trainings Start ============================
        $ttraining_query = $this->induction_health_safety_m->fetch_temp_trainings($temp_contractors_staff_id);
        foreach ($ttraining_query->getResultArray() as $ttraining_row){
          $temp_training = $ttraining_row['training'];
          $date_undertaken = $ttraining_row['date_undertaken'];
          $location = $ttraining_row['location'];

          $training_records_id = 0;
          $training_query = $this->induction_health_safety_m->fetch_sitestaff_training($contractor_site_staff_id);
          foreach ($training_query->getResultArray() as $training_row){
            $training_type = $training_row['training_type'];
            
            if($temp_training == $training_type){
              $training_records_id = $training_row['training_records_id'];
            }
          }

          if($training_records_id == 0){
            $this->induction_health_safety_m->add_training($contractor_site_staff_id,$temp_training,$date_undertaken,$location,1);
          }else{
            $this->induction_health_safety_m->update_training($training_records_id,$temp_training,$date_undertaken,$location);
          }

        }
      //Trainings End ==============================

        $this->induction_health_safety_m->set_temp_data_approve($temp_contractors_staff_id);
      }
    }
  }

  public function induction_slide_editor_view(){
    $this->admin_m = new Admin_m();

    if(isset($_GET['project_id'])){
      $project_id = $_GET['project_id'];
    }else{
      $project_id = "";
    }

    $q_admin_defaults_notes = $this->admin_m->fetch_default_notes();
    $arr = $q_admin_defaults_notes->getResultArray();
    $default_notes = array_shift($arr);

    $q_admin_defaults = $this->admin_m->fetch_admin_defaults();
    $arr = $q_admin_defaults->getResultArray();
    $data_b = array_shift($arr);
    
    $data = array_merge($data_b,$default_notes);

    $data['project_id'] = $project_id;

    // $data['main_content'] = 'slide_editor_v';
    
    $data['screen'] = 'Induction, Health and Safety';

    $data['main_content'] = 'App\Modules\Induction_health_safety\Views\slide_editor_v';
    return view('App\Views\page',$data);
    //$this->load->view('page', $data);
  }

  public function fetch_induction_projects_list(){

    $this->admin_m = new Admin_m();

    $q_admin_defaults = $this->admin_m->fetch_admin_defaults();
    foreach ($q_admin_defaults->getResultArray() as $row){
      $induction_categories = $row['induction_categories'];
      $induction_project_value = $row['induction_project_value'];
    }

    $ic_arr = explode(',',$induction_categories);
    $no_arr = count($ic_arr);
    $x = 0;
    while($x < $no_arr){
      if($x == 1){
        $induction_categories = "'".$ic_arr[$x]."'";
      }else{
        if($x > 1){
          $induction_categories = $induction_categories.",'".$ic_arr[$x]."'";
        }
      }
      
      $x++;
    }

    $query = $this->induction_health_safety_m->fetch_induction_projects_list($induction_categories,$induction_project_value);
    echo json_encode($query->getResult());
  }

  public function fetch_induction_projects_details(){
    $project_id = $_POST['project_id'];
    $query = $this->induction_health_safety_m->fetch_induction_projects_details($project_id);
    echo json_encode($query->getResult());
  }

  public function fetch_induction_is_generated(){
    $project_id = $_POST['project_id'];
    $is_saved = 0;
    $query = $this->induction_health_safety_m->fetch_induction_projects_details($project_id);
    foreach ($query->getResultArray() as $row){
      $is_saved = $row['is_saved'];
    }

    echo $is_saved;
  }

  public function fetch_induction_slide_detials(){
    $project_id = $_POST['project_id'];
    $query = $this->induction_health_safety_m->fetch_induction_slide_detials($project_id);
    echo json_encode($query->getResult());
  }

  public function fetch_induction_slide_project_details(){
    $project_id = $_POST['project_id'];
    $query = $this->induction_health_safety_m->fetch_induction_slide_detials($project_id);
    $project_ouline_text = "";
    foreach ($query->getResultArray() as $row){
      $project_ouline_text = $row['project_ouline_text'];
    }

    echo $project_ouline_text;
  }

  public function update_induction_slide_project_outline(){
    $project_id = $_POST['project_id'];
    $project_outline = $_POST['project_outline'];
    $project_outline = str_replace("'", "''", $project_outline);

    $slide_no = 2;
    $this->induction_health_safety_m->set_cleared_slides($slide_no,$project_id);

    $this->induction_health_safety_m->update_induction_slide_project_outline($project_id,$project_outline);
    $project_ouline_text = "";
    $query = $this->induction_health_safety_m->fetch_induction_slide_detials($project_id);
    foreach ($query->getResultArray() as $row){
      $project_ouline_text = $row['project_ouline_text'];
    }

    $data['project_id'] = $project_id;
    $data['slide_no'] = $slide_no;

    $induction_slide_q = $this->induction_health_safety_m->fetch_induction_projects_details($project_id);

    $data['induction_slide_q'] = $induction_slide_q;

    return view('App\Modules\Induction_health_safety\Views\induction_slide_pdf',$data);
    //$this->load->view('induction_slide_pdf', $data);

    echo $project_ouline_text;
    // $query = $this->induction_health_safety_m->fetch_induction_slide_detials($project_id);
    // echo json_encode($query->result());
  }

  public function update_induction_slide_site_hours(){
    $project_id = $_POST['project_id'];
    $generalSiteHours = $_POST['generalSiteHours'];
    $noisySiteHours = $_POST['noisySiteHours'];
    $otherSiteHours = $_POST['otherSiteHours'];

    $slide_no = 3;
    $this->induction_health_safety_m->set_cleared_slides($slide_no,$project_id);

    $this->induction_health_safety_m->update_induction_slide_site_hours($project_id,$generalSiteHours,$noisySiteHours,$otherSiteHours);

    $data['project_id'] = $project_id;
    $data['slide_no'] = $slide_no;

    $induction_slide_q = $this->induction_health_safety_m->fetch_induction_projects_details($project_id);

    $data['induction_slide_q'] = $induction_slide_q;

    return view('App\Modules\Induction_health_safety\Views\induction_slide_pdf',$data);
    //$this->load->view('induction_slide_pdf', $data);

    $query = $this->induction_health_safety_m->fetch_induction_projects_details($project_id);
    echo json_encode($query->getResult());
  }

  private function set_upload_options($proj_id)
  {   
    $path = "./uploads/project_inductions_images/".$proj_id;
    if(!is_dir($path)){
      mkdir($path, 0755, true);
    }
      $config = array();
      $config['upload_path'] = $path."/";
      $config['allowed_types'] = '*';
      $config['max_size']      = '0';
      $config['overwrite']     = FALSE;


      return $config;
  }

  private function set_upload_options_videos($proj_id)
  {   
    $path = "./uploads/induction_videos/".$proj_id;
    if(!is_dir($path)){
      mkdir($path, 0755, true);
    }
      $config = array();
      $config['upload_path'] = $path."/";
      $config['allowed_types'] = '*';
      $config['max_size']      = '0';
      $config['overwrite']     = FALSE;


      return $config;
  }

  function upload_videos(){
    $project_id = $_GET['project_id'];
    $this->load->library('upload');

    $files = $_FILES;
    $cpt = count($_FILES['userfile']['name']);
    for($i=0; $i<$cpt; $i++)
    {
        $file_name =  $files['userfile']['name'][$i];
        $file_name = str_replace(' ', '_', $file_name);

        $path_parts = pathinfo($file_name);
      $filename = $project_id.'_access';
      $extension = strtolower($path_parts['extension']);

      $file_name = "inductioncomp.".$extension;

      $_FILES['userfile']['name']= $file_name;
      $_FILES['userfile']['type']= $files['userfile']['type'][$i];
      $_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
      $_FILES['userfile']['error']= $files['userfile']['error'][$i];
      $_FILES['userfile']['size']= $files['userfile']['size'][$i];    

      $path = "./uploads/induction_videos/".$project_id."/";
      $file = $path.$file_name;
      if(file_exists($file)){   
        unlink($file);
      }
    
      $this->upload->initialize($this->set_upload_options_videos($project_id));
      if ( !$this->upload->do_upload()) {
          echo $this->upload->display_errors();
      }else{
          $this->induction_health_safety_m->update_induction_videos($project_id);

      }

      return redirect()->to("/induction_health_safety/inductions_videos");

    }
  }

  function upload_access()
  {
    $project_id = $_GET['project_id'];
    $this->load->library('upload');

    $files = $_FILES;
    $cpt = count($_FILES['userfile']['name']);
    for($i=0; $i<$cpt; $i++)
    {
        $file_name =  $files['userfile']['name'][$i];
        $file_name = str_replace(' ', '_', $file_name);

        $path_parts = pathinfo($file_name);
      $filename = $project_id.'_access';
      $extension = strtolower($path_parts['extension']);

      $file_name = $filename.".".$extension;

      $_FILES['userfile']['name']= $file_name;
      $_FILES['userfile']['type']= $files['userfile']['type'][$i];
      $_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
      $_FILES['userfile']['error']= $files['userfile']['error'][$i];
      $_FILES['userfile']['size']= $files['userfile']['size'][$i];    

      $path = "./uploads/project_inductions_images/".$project_id."/";
      $file = $path.$file_name;
      if(file_exists($file)){   
        unlink($file);
      }else{

      }
    
      $this->upload->initialize($this->set_upload_options($project_id));
      if ( !$this->upload->do_upload()) {
          echo $this->upload->display_errors();
      }else{
          $this->induction_health_safety_m->update_induction_slide_access($project_id,$file_name);

      }

      return redirect()->to("/induction_health_safety/induction_slide_editor_view?project_id=".$project_id);

    }
  }

  function upload_amenities()
  {
    $project_id = $_GET['project_id'];
    $this->load->library('upload');

    $files = $_FILES;
    $cpt = count($_FILES['userfile']['name']);
    for($i=0; $i<$cpt; $i++)
    {
        $file_name =  $files['userfile']['name'][$i];
        $file_name = str_replace(' ', '_', $file_name);

      //    // create Imagick object
       // $imagick = new Imagick();
       // // Reads image from PDF
       // $imagick->readImage($file_name);
       // // Writes an image or image sequence Example- converted-0.jpg, converted-1.jpg
       // $imagick->writeImages('converted.jpg', false);


        $path_parts = pathinfo($file_name);
      $filename = $project_id.'_amenities';
      $extension = strtolower($path_parts['extension']);

      $file_name = $filename.".".$extension;

      $_FILES['userfile']['name']= $file_name;
      $_FILES['userfile']['type']= $files['userfile']['type'][$i];
      $_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
      $_FILES['userfile']['error']= $files['userfile']['error'][$i];
      $_FILES['userfile']['size']= $files['userfile']['size'][$i];  

      $path = "./uploads/project_inductions_images/".$project_id."/";
      $file = $path.$file_name;
      if(file_exists($file)){   
        unlink($file);
      }  

      $this->upload->initialize($this->set_upload_options($project_id));
      if ( !$this->upload->do_upload()) {
          echo $this->upload->display_errors();
      }else{
          $this->induction_health_safety_m->update_induction_slide_amenities($project_id,$file_name);

      }

      return redirect()->to("induction_health_safety/induction_slide_editor_view?project_id=".$project_id);

    }
  }

  function update_induction_slide_emergency(){

    $project_id = $_POST['project_id'];
    $epr_medical_name = $_POST['medical_name'];
    $epr_medical_contact = $_POST['medical_phone_number'];
    $epr_medical_address = $_POST['medical_address'];
    $epr_emergency_name = $_POST['emergency_name'];
    $epr_emergency_contacts = $_POST['emergency_phone_number'];
    $epr_emergency_address = $_POST['emergency_address'];

    $med_add_unit_level = $_POST['med_add_unit_level'];
    $med_add_number = $_POST['med_add_number'];
    $med_add_street = $_POST['med_add_street'];
    $med_state_name = $_POST['med_state_name'];
    $med_add_suburb = $_POST['med_add_suburb'];
    $med_add_postcode = $_POST['med_add_postcode'];

    $emer_add_unit_level = $_POST['emer_add_unit_level'];
    $emer_add_number = $_POST['emer_add_number'];
    $emer_add_street = $_POST['emer_add_street'];
    $emer_state_name = $_POST['emer_state_name'];
    $emer_add_suburb = $_POST['emer_add_suburb'];
    $emer_add_postcode = $_POST['emer_add_postcode'];

    $slide_no = 5;
    $this->induction_health_safety_m->set_cleared_slides($slide_no,$project_id);

    $this->induction_health_safety_m->update_induction_slide_emergency($project_id,$epr_medical_name,$epr_medical_contact,$epr_medical_address,$epr_emergency_name,$epr_emergency_contacts,$epr_emergency_address,$med_add_unit_level,$med_add_number,$med_add_street,$med_state_name,$med_add_suburb,$med_add_postcode,$emer_add_unit_level,$emer_add_number,$emer_add_street,$emer_state_name,$emer_add_suburb,$emer_add_postcode
    );
  
    $data['project_id'] = $project_id;
    $data['slide_no'] = $slide_no;

    $induction_slide_q = $this->induction_health_safety_m->fetch_induction_projects_details($project_id);

    $data['induction_slide_q'] = $induction_slide_q;

    // $this->load->view('induction_slide_pdf', $data);

    return view('App\Modules\Induction_health_safety\Views\induction_slide_pdf',$data);
    // $query = $this->induction_health_safety_m->fetch_induction_slide_detials($project_id);
    // echo json_encode($query->result());
  }

  function update_induction_slide_ppe(){
    $project_id = $_POST['project_id'];
    $ppe_selected = $_POST['ppe_selected'];
    $ppe_selected = json_encode($ppe_selected);

    $slide_no = 6;
    $this->induction_health_safety_m->set_cleared_slides($slide_no,$project_id);

    $this->induction_health_safety_m->update_induction_slide_ppe($project_id,$ppe_selected);

    $data['project_id'] = $project_id;
    $data['slide_no'] = $slide_no;

    $induction_slide_q = $this->induction_health_safety_m->fetch_induction_projects_details($project_id);

    $data['induction_slide_q'] = $induction_slide_q;

    return view('App\Modules\Induction_health_safety\Views\induction_slide_pdf',$data);

    //$this->load->view('induction_slide_pdf', $data);

    // $query = $this->induction_health_safety_m->fetch_induction_slide_detials($project_id);
    // echo json_encode($query->result());

  }

  function generated_selected_pdf(){
    $project_id = $_GET['project_id'];
    $slide_no = $_GET['slide_no'];
    $data['project_id'] = $project_id;
    $data['slide_no'] = $slide_no;

    $induction_slide_q = $this->induction_health_safety_m->fetch_induction_projects_details($project_id);

    $data['induction_slide_q'] = $induction_slide_q;

    return view('App\Modules\Induction_health_safety\Views\induction_slide_pdf',$data);

    //$this->load->view('induction_slide_pdf', $data);
  }

  function set_cleared_slides(){
    $slide_no = $_POST['slide_no'];
    $project_id = $_POST['project_id'];
    $query = $this->induction_health_safety_m->set_cleared_slides($slide_no,$project_id);

    $data['project_id'] = $project_id;
    $data['slide_no'] = $slide_no;

    $induction_slide_q = $this->induction_health_safety_m->fetch_induction_projects_details($project_id);

    $data['induction_slide_q'] = $induction_slide_q;

    return view('App\Modules\Induction_health_safety\Views\induction_slide_pdf',$data);
    //$this->load->view('induction_slide_pdf', $data);
  }

  function set_inductions_as_saved(){
    $project_id = $_POST['project_id'];

    $user_id = $this->session->get('user_id');
    $users_q = $this->user_model->fetch_user($user_id);
    foreach ($users_q->getResultArray() as $users_row){
      $user_name = $users_row['user_first_name']." ".$users_row['user_last_name'];
      $user_email_id = $users_row['user_email_id'];
      $email_q = $this->company_m->fetch_email($user_email_id);
      foreach ($email_q->getResultArray() as $email_row){
        $sender_user_email = $email_row['general_email'];
      }
    }

    $this->induction_health_safety_m->set_inductions_as_saved($project_id);
    $sender_name = $user_name;
    $email_from = $sender_user_email;
    $email_to = "marko@focusshopfit.com.au";
    $email_cc = "katrina@focusshopfit.com.au";
    $email_bcc = "ian@focusshopfit.com.au,mark.obis2012@gmail.com,".$sender_user_email;
    $subject = "Induction Slides Generated for project: ".$project_id;
    $message = "Induction Slides has been generated for project number: ".$project_id.". Please see link: https://sojourn.focusshopfit.com.au/induction_health_safety/inductions_videos";
    $prompt = $this->email_send($sender_name,$email_from,$email_to,$email_cc,$email_bcc,$subject,$message);
    echo $prompt;
    // $query = $this->induction_health_safety_m->fetch_induction_slide_detials($project_id);
    // echo json_encode($query->result());
  }


  public function fetch_state(){
    $query = $this->induction_health_safety_m->fetch_state();
    echo json_encode($query->getResult());
  }

  


}