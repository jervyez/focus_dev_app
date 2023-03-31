<?php
// module created by Jervy 20-9-2022
namespace App\Modules\Etc\Controllers;

use App\Controllers\BaseController;


use App\Modules\Admin\Controllers\Admin;
use App\Modules\Admin\Models\Admin_m;

use App\Modules\Etc\Models\Etc_m;



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Etc extends BaseController {

  function __construct(){
    $this->admin = new Admin();
    $this->admin_m = new Admin_m();
    $this->etc_m = new Etc_m();
    $this->session = \Config\Services::session();
  }


  public function index($value='') {

    $data = array();
    $data['main_content'] = 'App\Modules\Etc\Views\landing';
    echo '<p class="">USERS SCREEN LIST HERE ******</p>';

    return view('App\Views\page',$data);
  }

  public function sum_total_wip_cost(){
    $data = array();
    $data['main_content'] = 'App\Modules\Etc\Views\landing';
    echo '<p class="">USERS SCREEN LIST HERE ******</p>';

  }

  public function set_email_notif($work_id=''){
        
    $user_id = $this->session->get('user_id');
    $con_feedbacks_arr = array();
    $arr_cc = array();

    //return;

    $from = '';
    $to = '';
    $subject = '';
    $email_msg = '';

    



    if($work_id != '' && isset($work_id)){


      $q_email_feedbacks = $this->etc_m->get_email_feedbacks(1);
      $get_result_a = $q_email_feedbacks->getResult();
      $email_feedback = array_shift($get_result_a);


      $q_contractors = $this->etc_m->list_group_contractors_per_workid($work_id);

      $q_best_price = $this->etc_m->get_best_quoted_price_per_workid($work_id);
      $get_result_b = $q_best_price->getResult();
      $best_price = array_shift($get_result_b);
      $selected_price = $best_price->ex_gst ?? 0;

      $selected_work_contractor_id = $best_price->works_contrator_id ?? 0;
    }else{


      $selected_work_contractor_id = $_POST['selected_work_contractor_id'];


      $q_email_feedbacks = $this->etc_m->get_email_feedbacks();
      $email_feedback = array_shift($q_email_feedbacks->result() );

      $q_contractors = $this->etc_m->list_group_contractors_per_selected($selected_work_contractor_id);

      $q_best_price = $this->etc_m->get_best_quoted_price($selected_work_contractor_id);
      $best_price = array_shift($q_best_price->result() );
      $selected_price = $best_price->ex_gst;
    }


    foreach ($q_contractors->getResult() as $works_contractor){


      if($selected_work_contractor_id == $works_contractor->works_contrator_id){

        $q_feedback = $this->etc_m->get_prime_feedback();
        $get_result_c = $q_feedback->getResult();
        $contractor_feedback = array_shift( $get_result_c );
        $con_feedbacks_arr[$works_contractor->works_contrator_id] = $contractor_feedback->feedback_statement;
      //  $quoted_price = $selected_price;
        
      }else{

        if( floatval($works_contractor->ex_gst) > 0  && ($selected_work_contractor_id != $works_contractor->works_contrator_id)    ){


          $diff_value = ( ( $works_contractor->ex_gst - $selected_price ) / $selected_price ) * 100;
          $diff_value =  round(abs($diff_value) ,2);

          if($diff_value >= 100){
            $diff_value = 99.99;
          }

          $q_feedback = $this->etc_m->get_cont_feedbacks($diff_value);
          $get_result_j = $q_feedback->getResult();
          $contractor_feedback = array_shift( $get_result_j );
          $con_feedbacks_arr[$works_contractor->works_contrator_id] = $contractor_feedback->feedback_statement;

        }
      }
    }



    foreach ($con_feedbacks_arr as $works_contrator_id => $con_fb_message) {

    
      $email_contents = '';
      

      $work_contractor_q = $this->etc_m->get_work_contractor_details($works_contrator_id);
      $get_result_d = $work_contractor_q->getResult();
      $row = array_shift( $get_result_d );

          
              
 if($row->job_category == 'Maintenance' || $row->job_category == 'Design Works' || $row->job_category == 'Minor Works'){
        return;
      }


      $q_client_details = $this->etc_m->display_company_detail_by_id($row->client_id,$row->is_pending_client);
      $get_result_e = $q_client_details->getResult();
      $client = array_shift($get_result_e);

      $company_contractor_name = '';
      if($row->is_pending == 1){
        $q_pending_contractor = $this->etc_m->display_company_detail_by_id($row->company_id,1);
        $getResult = $q_pending_contractor->getResult();
        $pending_contractor = array_shift($getResult);
        $file_company_name = strtolower( str_replace(' ', '_', $pending_contractor->company_name )  );
        $company_contractor_name = $pending_contractor->company_name;
      }else{
        $company_contractor_name = $row->company_name;
      }

      if($row->project_estiamator_id > 0){
        $sender_id = $row->project_estiamator_id;

      }else{
        $sender_id = $row->project_manager_id;
      }


      $q_sender_contact = $this->etc_m->fetch_user($sender_id); // $row->project_manager_id
      $get_result_f = $q_sender_contact->getResult();
      $sender_contact = array_shift($get_result_f);
      $from = $sender_contact->general_email;

    //  if($sender_id != $user_id){
        $q_sender_contact_cc = $this->etc_m->fetch_user($row->project_admin_id); // $row->project_manager_id
        $get_result_g = $q_sender_contact_cc->getResult();
        $sender_contact_cc = array_shift($get_result_g);
        array_push($arr_cc,$sender_contact_cc->general_email);

    //  }


      $to = $row->general_email;

      if($row->is_pending == 1){
        $to = $pending_contractor->email;
      }


      if($row->contractor_type == 2){
        $work_job_name = $row->job_sub_cat;
      }else{
        $work_job_name = $row->supplier_cat_name;
      }

      if($work_job_name == 'Other'){
        $work_job_name = $row->other_work_desc;
      }



      $work_contractor_joinery = $this->etc_m->get_work_contractor_joinery($works_contrator_id);

      if($work_contractor_joinery->getNumRows() > 0){

        $get_result_h = $work_contractor_joinery->getResult();

        $work_joinery_id = array_shift( $get_result_h );
        $distinct_character = preg_replace('/[0-9]+/', '', $work_joinery_id->works_id);
        $joinery_work_q = $this->etc_m->get_joineryDetails($work_joinery_id->works_id,$distinct_character);

        if($joinery_work_q->getNumRows() > 0){
          $joinery_work_details = array_shift($joinery_work_q->result() );
          $work_job_name = 'Joinery - '.$joinery_work_details->joinery_name;
        }

      }



      if($selected_work_contractor_id == $works_contrator_id){
        $fb_email_message = '<p>'.$email_feedback->selected_contractor_email.'</p>';
      }else{
        $fb_email_message = '<p>'.$email_feedback->unsuccessful_contractor_email.'</p>';
      }


      $email_contents = str_replace('<works_desc>',  '<span style="font-weight:bold;">'.$work_job_name.'</span>'   ,$fb_email_message);
      $email_contents = str_replace('<client_name>',''.$client->company_name.'',$email_contents);

      $tendered_amount = number_format($row->ex_gst,2);


      $email_contents = str_replace('<tendered_amount>',''.$tendered_amount.'',$email_contents);
      $email_contents = str_replace('<contractor_name>',$company_contractor_name,$email_contents);
      $email_contents = str_replace('<feedback>',$con_fb_message,$email_contents);
      $email_contents = str_replace('<sender_email>',$from,$email_contents);



      if($row->ex_gst < $selected_price){
        $email_contents = str_replace('greater','less',$email_contents);
      }



      $email_contents = str_replace('<bold_start>','<span style="font-weight:bold;">',$email_contents);
      $email_contents = str_replace('<bold_end>','</span>',$email_contents);


      $email_contents = nl2br($email_contents);




      $user_signature_q = $this->etc_m->get_user_email_signature($row->focus_company_id);
      $get_result_i = $user_signature_q->getResult();
      $user_signature = array_shift($get_result_i);



      $email_contents .= '<p title="'.$works_contrator_id.'"><br />Regards,<br /><br />'.$sender_contact->user_first_name.' '.$sender_contact->user_last_name.'<br /><strong id="" class="">'.$from.'</strong><br />'.$sender_contact->role_types.'</p>';
      $email_contents .= '<img src="https://sojourn.focusshopfit.com.au/uploads/misc/'.$user_signature->banner_name.'" width="788" height="170" alt="Focus Shopfit PTY LTD Signature" />';



      $subject = 'Contractor Feedback: '.$client->company_name.' - '.$row->project_id.' '.$row->project_name.' - '.$company_contractor_name;
      $email_msg = '<div style="font-family: verdana,sans-serif; font-size:12px; ">'.$email_contents.'</div>';
      $email_contents = '';

      if($row->ex_gst > 0    ){

        if($row->set_send_feedback == 1  && $row->set_wrk_fb == 1  && $row->set_prj_fb == 1  ){

          $date_today = date('d/m/Y');
          $this->etc_m->update_sent_feedback($date_today,$works_contrator_id);            
          $this->set_send_email($from,$to,$subject,$email_msg,$arr_cc); // done
        }
      }
    }
  }

  public function set_send_email($from,$to,$subject,$msg,$arr_cc=array(),$arr_bcc=array()){


    $mail = new PHPMailer; 
    $mail->Host = 'sojourn-focusshopfit-com-au.mail.protection.outlook.com';    
    $mail->Port = 587;    
    $mail->ishtml(true);
    $mail->smtpdebug = 2;
    
    $mail->setFrom($from,'Focus Shopfit');
    $mail->addAddress(strtolower($to));
    $mail->addReplyTo($from,'Focus Shopfit');
    $mail->addBCC('jervyezaballa@gmail.com');

    if(!empty($arr_cc)){
      foreach ($arr_cc as $key => $cc_email) {
        $mail->addCC(strtolower($cc_email));
      }
    }

    if(!empty($arr_bcc)){
      foreach ($arr_bcc as $key => $bcc_email) {
        $mail->addBCC(strtolower($bcc_email));
      }
    }

    $mail->Subject = $subject; //'Focus Shopfit Tender - Flight Centre Ltd, Carillon City, Perth';
    $mail->Body = $msg;
    

    if(!$mail->send()) {
      echo 'Message could not be sent.';
      echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
  }



  public function remind_hr_left(){

    $fetch_admin_defaults_q = $this->etc_m->fetch_admin_defaults();
    $get_result = $fetch_admin_defaults_q->getResult();
    $admin_defaults = array_shift($get_result);

    $today_time = date('H:i', strtotime('-'.$admin_defaults->hrs_before_due.' hour'));

    $has_attachment = 0;

    $day_today = date('Y-m-d');
    $today_date = date('d/m/Y');

    $works_contractor_q = $this->etc_m->get_remind_hr_left($today_date);

    foreach ($works_contractor_q->getResult() as $works_contractor){

      $starttimestamp = strtotime($today_time);
      $endtimestamp = strtotime($works_contractor->work_replyby_time);
      $difference = abs($endtimestamp - $starttimestamp)/3600;
      $difference = number_format($difference,2);

      if($difference <= 0.17){ // 15 mins - 30 mins allowance

        $attachment_q = $this->etc_m->check_attachments($works_contractor->project_id);
        if($attachment_q->num_rows > 0){
          $has_attachment = 1;
        }else{
          $has_attachment = 0;
        }

      $work_contractor_q = $this->etc_m->get_work_contractor_details($works_contractor->work_contractors_id);
      $contractor_supplier = array_shift($work_contractor_q->result() );

      if($contractor_supplier->contractor_type == 2){
        $work_job_name = $contractor_supplier->job_sub_cat;
      }else{
        $work_job_name = $contractor_supplier->supplier_cat_name;
      }

      $work_contractor_joinery = $this->etc_m->get_work_contractor_joinery($works_contractor->work_contractors_id);

      if($work_contractor_joinery->num_rows > 0){

        $work_joinery_id = array_shift($work_contractor_joinery->result() );
        $distinct_character = preg_replace('/[0-9]+/', '', $work_joinery_id->works_id);
        $joinery_work_q = $this->etc_m->get_joineryDetails($work_joinery_id->works_id,$distinct_character);

        if($joinery_work_q->num_rows > 0){
          $joinery_work_details = array_shift($joinery_work_q->result() );
          $work_job_name = 'Joinery - '.$joinery_work_details->joinery_name;
        }
      }

      if($work_job_name == 'Other'){
        $work_job_name = $contractor_supplier->other_work_desc;
      }

      $q_client_company = $this->etc_m->display_company_detail_by_id($contractor_supplier->client_id,$contractor_supplier->is_pending_client);
      $client_company = array_shift($q_client_company->result());

      if($contractor_supplier->project_estiamator_id > 0){
        $q_esti_contact = $this->etc_m->fetch_email_user($contractor_supplier->project_estiamator_id);
        $esti_contact = array_shift($q_esti_contact->result());
      }else{
        $q_esti_contact = $this->etc_m->fetch_email_user($contractor_supplier->project_manager_id);
        $esti_contact = array_shift($q_esti_contact->result());
      }

      $user_signature_q = $this->etc_m->get_user_email_signature($contractor_supplier->focus_company_id);
      $user_signature = array_shift($user_signature_q->result() );
 
      $file_company_name = strtolower( str_replace(' ', '_', $contractor_supplier->company_name )  );


      if($contractor_supplier->is_pending == 1){
        $q_pending_contractor = $this->etc_m->display_company_detail_by_id($contractor_supplier->company_id,1);
        $getResult = $q_pending_contractor->getResult();
        $pending_contractor = array_shift($getResult);

        $contractor_supplier->first_name = $pending_contractor->contact_person_fname;
        $contractor_supplier->last_name = $pending_contractor->contact_person_sname;

        $contractor_supplier->company_name = $pending_contractor->company_name;
        $contractor_supplier_location = '';

        $contractor_supplier->office_number = $pending_contractor->contact_number;
        $contractor_supplier->general_email = $pending_contractor->email;

        $contractor_supplier->abn = '';
        $file_company_name = strtolower( str_replace(' ', '_', $pending_contractor->company_name )  );
      }


      $message = '<p class="">You are receiving this reminder that your tender submission for <strong style="background-color: #ffff00;">'.$work_job_name.'</strong> job for our client <strong style="background-color: #ffff00;">'.$client_company->company_name.'</strong> is about to end in '.$admin_defaults->hrs_before_due.' hour(s)
      <br />If for any reason you do not believe you will be able to meet this deadline, then please contact <strong style="background-color: #ffff00;">'.$esti_contact->user_full_name.'</strong> at <strong class=""><u>'.$esti_contact->general_email.'</u></strong>.</p>
      <p class=""><br />Tenders received after the deadline may not be accepted.</p><p class="">Your tender <strong class="">MUST BE SUBMITTED ONLINE</strong> by clicking on this link.</p>
      <h2><a href="https://sojourn.focusshopfit.com.au/submit_quote/'.$works_contractor->work_contractors_id.'" style="padding: 8px 10px; text-decoration: none; border-radius: 5px; border: 1px solid #c8c6c4; color: #4f6bed; background-color: #EFEFEF;" target="_blank" title="Online Quotation Form">Online Quotation Form</a></h2><p>';


      if($has_attachment == 1){
        $message .= '<br />&bull; This project has downloadable attachments, click <a href="https://sojourn.focusshopfit.com.au/project_attachments/proj_attachment?project_id='.$works_contractor->project_id.'" class="" id="" target="_blank" tile="Download project attachments"><strong id="" class="">this link</strong></a> to view.';
        $has_attachment = 0;
      }

      $file_company_name = str_replace( array( "'",  '"', ',', '"'  , '`' ,'%' ,  '&apos', ' ' , '&',  ';', '<', '>','/' ), '', $file_company_name);


      $pdf_file = $works_contractor->project_id.'_cqr_'.$contractor_supplier->works_id.'_'.$file_company_name.'_';
      $pdf_file_cqr = $this->pull_out_file($pdf_file);

      $message .= '<br />&bull; Download the CQR (Contractor Quote Request) form, click <a href="https://sojourn.focusshopfit.com.au/docs/stored_docs/'.$pdf_file_cqr.'" class="" id="" target="_blank" tile="Download Contractor Quote Request Form"><strong id="" class="">this link</strong></a> to view.</p>';

  

      $message .= '<p class=""><br />Regards,<br /><br />'.$esti_contact->user_full_name.'<br /><strong id="" class="">'.$esti_contact->general_email.'</strong><br />Estimator</p>
      <img src="https://sojourn.focusshopfit.com.au/uploads/misc/'.$user_signature->banner_name.'" width="788" height="170" alt="Focus Shopfit PTY LTD Signature" />';

      $email_msg = '<div style="font-family: verdana,sans-serif; font-size:12px; ">'.$message.'</div>';

      $this->etc_m->set_remind_hr_before($works_contractor->cqr_reminder_id ,$today_date);   // trigger tagged

      $from = $esti_contact->general_email;
      $to = $contractor_supplier->general_email;
      $subject = 'Tender Reminder: '.$client_company->company_name.' - '.$works_contractor->project_id.' '.$works_contractor->project_name.' - '.$contractor_supplier->company_name;  //hr left reminder



      $this->set_send_email($from,$to,$subject,$email_msg);

      }
    }
  }





}