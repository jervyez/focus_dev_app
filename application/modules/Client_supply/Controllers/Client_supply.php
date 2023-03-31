<?php
// module created by Jervy 20-9-2022
namespace App\Modules\Client_supply\Controllers;

use App\Controllers\BaseController;

use App\Modules\Admin\Controllers\Admin;
use App\Modules\Admin\Models\Admin_m;

use App\Modules\Users\Controllers\Users;
use App\Modules\Users\Models\Users_m;

use App\Modules\Client_supply\Models\Client_supply_m;


class Client_supply extends BaseController {

  function __construct(){
    $this->admin = new Admin();
    $this->admin_m = new Admin_m();
    $this->client_supply_m = new Client_supply_m();

    $this->request = \Config\Services::request();
    $this->session = \Config\Services::session();
  }


  public function index($value='') {

    $this->users = new Users();
    $this->user_model = new Users_m();

    if(!$this->users->_is_logged_in() ):
      $this->users->logout();
      return redirect()->to('/signin');
    endif;

    $this->users->_check_user_access('client_supply',1);

    $data = array();
    $data['main_content'] = 'App\Modules\Client_supply\Views\client_supply_home';
    $data['screen'] = 'Client Supply';
    $data['page_title'] = 'Client Supply';

    $select_static_defaults_q = $this->user_model->select_static_defaults();
    $getResultArray = $select_static_defaults_q->getResultArray();
    $data['static_data'] = array_shift($getResultArray);

    $client_supply_reminder_dys = $data['static_data']['client_supply_reminder_dys'];
    $date_today = date('d/m/Y');
    $date_limit_name = date('D', strtotime("today -$client_supply_reminder_dys days"));

    if($date_limit_name == 'Sun'){
      $client_supply_reminder_dys = $client_supply_reminder_dys-1;
      $date_limit = date('d/m/Y', strtotime("-$client_supply_reminder_dys days")); 
    }elseif($date_limit_name == 'Sat'){
      $client_supply_reminder_dys = $client_supply_reminder_dys+1;
      $date_limit = date('d/m/Y', strtotime("-$client_supply_reminder_dys days")); 
    }else{
      $date_limit = date('d/m/Y', strtotime("-$client_supply_reminder_dys days")); 
    }

    return view('App\Views\page',$data);

  }




  public function list_client_supply_table(){

    $unix_date_sort = '';

    $bound = '';
    $custom = '';
    $delvr = '';

    $supply_list_q_wa = $this->client_supply_m->list_client_supply($custom);

    foreach ($supply_list_q_wa->getResult() as $supply):  
      $status_late = ''; 
      $status_late = ( $supply->unix_dlvy_dt < strtotime(date('Y-m-d'))   ? 'late_delv' : '');   


      if($supply->date_goods_arrived == ''){
        $has_arrived_warehouse = 0;
        $bound = 'inbnd';
      }else{
        $has_arrived_warehouse = 1;
        $bound = 'outbnd';
      }

      if($supply->is_delivered_date != ''){
        $bound = 'cpmltd';
      }

      echo '<tr class="'.$status_late.' '.$bound.'  csup_row  focus_comp_loc_'.$supply->focus_company_id.' ">';


      if($has_arrived_warehouse == 1 ){

        $delvr = $supply->delivery_date;
        $unix_date_sort = $supply->unix_dlvy_dt;

        if($delvr == ''){
          $unix_date_sort = '_';
        }

        echo '<td class="hide">'.$unix_date_sort.' '.$supply->project_name.' </td>';

      }else{
        $delvr = $supply->date_goods_expected;
        $unix_date_sort = $supply->unix_dt_gds_expt;

        if($delvr == ''){
          $unix_date_sort = '_';
        }

        echo '<td class="hide">'.$unix_date_sort.' '.$supply->project_name.' </td>';
      }



      echo '<td><a href="'.site_url().'projects/view/'.$supply->project_id.'" target="_blank">'.$supply->project_id.'</a></td>
      <td>'.$supply->company_name.'</td>';

      echo '<td><a href="#" class="pointer view_edit_supply" id="'.$supply->client_supply_id.'">'.$supply->supply_name.'</a>';

      if($has_arrived_warehouse == 1 ){
        if($bound != 'cpmltd'):
          echo '<button class="pull-right btn-success btn  tooltip-enabled" title="Set Delivered to Site Address" data-html="true" data-placement="top" data-original-title="Set Delivered to Site Address"  style="padding: 2px 4px;" onClick="set_as_delivered('.$supply->client_supply_id.',this)"> <em class="fa fa-truck" style=""></em></button>';
        endif;
      }else{

        if($bound != 'cpmltd'):
          echo '<button class="pull-right btn-info btn  tooltip-enabled" title="Set Arrived to Warehouse" data-html="true" data-placement="top" data-original-title="Set Arrived to Warehouse"  style="padding: 2px 4px;" onClick="set_as_arrived('.$supply->client_supply_id.',this)"> <em class="fa fa-cubes" style=""></em></button>';
        endif;
      }

      echo '</strong></td>';
      echo '<td>'.$supply->warehouse.'</td>';


      echo '<td>'.$delvr.'</td>';

      if( $supply->photos != '' ): 

        $haystack = strtoupper($supply->photos);
        $needle = '.PDF';

        if (strpos($haystack,$needle) !== false) {

          echo '<td> <em class="fa fa-file-pdf-o fa-lg pad-3" style="color: #DC1D00;margin: 0 2px;"  ></em>';
          echo '<em class="hide list_set_bound">'.$supply->focus_company_id.'_'.$bound.'</em> </td>  ';
        
        }else{

          echo '<td> <em class="fa fa-photo fa-lg  view_img pointer pad-3" id="set_img_'.$supply->client_supply_id.'" style="color:#35a239;"  ></em>';
          echo '<em class="hide list_set_bound">'.$supply->focus_company_id.'_'.$bound.'</em> </td>  ';
        }



      else:
        echo '<td>';
        if($this->session->get('client_supply') ==  2):  
          echo ' <em class="fa fa-cloud-upload upload_img fa-lg pointer pad-3" style="color:#3f51b5;" data-prj-id="'.$supply->project_id.'" title="Upload Photo" id="'.$supply->client_supply_id.'" ></em>';
        endif;

        echo ' <em class="hide list_set_bound">'.$supply->focus_company_id.'_'.$bound.'</em></td>';
      endif;  

      echo '</tr>';
    endforeach;


  }

  public function set_as_arrived($id){
    $set_date = date("d/m/Y");
    $this->client_supply_m->set_arrived($id,$set_date);
    return redirect()->to('/client_supply');
  }


  public function set_as_delivered($id){
    $set_date = date("d/m/Y");
    $this->client_supply_m->set_delivered($id,$set_date);
    return redirect()->to('/client_supply');
  }

  public function upload_photos(){
    $client_supply_id = $_POST['client_supply_id'];
    $supply_project_id = $_POST['supply_project_id'];
    $photos = $this->processUpload('supply_photos','client_supply',$supply_project_id.'_supply',1);
    $this->client_supply_m->update_photos($client_supply_id,$photos);

    return redirect()->to('/client_supply');
  }





  public function display_client_logo($warehouse_id){

    $clinent_logo_q = $this->client_supply_m->get_client_supply_logo($warehouse_id);
    $getResultArray = $clinent_logo_q->getResultArray();
    $clinent_logo = array_shift($getResultArray);
    $brand_name = $clinent_logo['company_name'];
    echo '<span class=" btn-info pad-5 block m-10" style="border-radius: 6px;     font-size: 12px;     padding: 0px 5px;     margin: 5px;">'.$brand_name.'</span>';

  }

    public function view_supply($supply_id=''){
    $get_supply_data_q = $this->client_supply_m->get_supply_data($supply_id);
    $getResultArray = $get_supply_data_q->getResultArray();
    $supply_data = array_shift($getResultArray);

    echo $supply_data['client_supply_id'].'|';
    echo $supply_data['supply_name'].'|';
    echo $supply_data['project_id'].'|';
    echo $supply_data['client_id'].'|';
    echo $supply_data['quantity'].'|';
    echo $supply_data['date_goods_expected'].'|';
    echo $supply_data['date_goods_arrived'].'|';
    echo $supply_data['delivered_by'].'|';
    echo $supply_data['to_be_advised'].'|';
    echo $supply_data['delivery_date'].'|';
    echo $supply_data['is_deliver_to_site'].'|';
    echo $supply_data['address'].'|';
    echo $supply_data['photos'].'|';
    echo $supply_data['description'].'|';
    echo $supply_data['warehouse'].'|';
    echo $supply_data['is_active'].'|';
    echo $supply_data['is_delivered_date'].'|';
    echo $supply_data['user_posted'].'|';

    $end_date_formatted = date_format(date_create_from_format('d/m/Y', $supply_data['date_site_finish']), 'Y-m-d');

    echo date('d/m/Y', strtotime($end_date_formatted. ' + 2 weeks'));
  }




  public function process_form_supply(){
    $this->user_model = new Users_m();


    $supply_name = $_POST["supply_name"];

    $project_data = explode('_', $_POST["project_data"]);
    $project_number = $project_data['0'];

    $delivered_by = $_POST["delivered_by"];
    $to_be_advised = $_POST["to_be_advised"];
    $date_goods_expected = $_POST["date_goods_expected"];
    $date_goods_arrived = $_POST["date_goods_arrived"];
    $qty = $_POST["qty"];
    $delivery_date = $_POST["delivery_date"];
    $is_deliver_to_site_select = $_POST["is_deliver_to_site_select"];
    $set_address = trim(preg_replace('/\s+/',' ', $_POST["set_address"]));
    $warehouse_selected =  str_replace('&nbsp;', '',strip_tags(trim(preg_replace('/\s+/',' ', $_POST["warehouse_selected"]))));


    if($warehouse_selected == ''){
      $warehouse_selected = 'Un-Allocated';
    }

    $description = $_POST["description"];

    $select_static_defaults_q = $this->user_model->select_static_defaults();
    $getResultArray = $select_static_defaults_q->getResultArray();
    $data['static_data'] = array_shift($getResultArray);

    $project_finish = $project_data['2'];
    $srchDate = date_format(date_create_from_format('d/m/Y', "$project_finish"), 'Y-m-d');

    $client_supply_reminder_dys = $data['static_data']['client_supply_reminder_dys'];// + 2;

    $date_limit_name = date('D', strtotime("$srchDate -$client_supply_reminder_dys days"));
    $set_delivery_date =  date('d/m/Y', strtotime($srchDate."  -$client_supply_reminder_dys days"));


    if($date_goods_expected == ''){
      $date_goods_expected = $set_delivery_date;
    }

    if($delivery_date == ''){
      $delivery_date = $set_delivery_date;
    }

    $user_id = $this->session->get('user_id');


    if ( array_sum($_FILES['supply_photos']['error']) > 0 ){
      $photos = '';
    }else{
      $photos = $this->processUpload('supply_photos','client_supply',$project_number.'_supply',1);

    }

    $this->client_supply_m->inset_new_supply($supply_name,$project_number,$qty,$date_goods_expected,$date_goods_arrived,$delivered_by,$to_be_advised,$delivery_date,$is_deliver_to_site_select,$set_address,$photos,$description,$warehouse_selected,$user_id);

    return redirect()->to('/client_supply');
  }



  public function update_form_supply(){
    $this->user_model = new Users_m();

    $supply_name = $_POST["supply_name"];
    $supply_data_id = $_POST["supply_data_id"];
    $init_project_id = $_POST["init_project_id"];
    $date_goods_expected = $_POST["date_goods_expected"];
    $date_goods_arrived = $_POST["date_goods_arrived"];
    $quantity = $_POST["qty"];
    $delivered_by = $_POST["delivered_by"];
    $to_be_advised = $_POST["to_be_advised"];
    $delivery_date = $_POST["delivery_date"];
    $is_deliver_to_site_select = $_POST["is_deliver_to_site_select"];
    $set_address = $_POST["set_address"];
    $warehouse_selected = $_POST["ups_warehouse_selected"];
    $description = $_POST["description"];

    $project_data = explode('_', $_POST["project_id"]);
    $data_project_set = $project_data['0'];

    if(isset($_POST["project_id"]) && $_POST["project_id"]!=''){
      if($data_project_set != $init_project_id){
        $data_project_id = $data_project_set;
      }else{
        $data_project_id = $init_project_id;
      }
    }else{
      $data_project_id = $init_project_id;
    }

    $client_id = 0;

  
    $select_static_defaults_q = $this->user_model->select_static_defaults();
    $getResultArray = $select_static_defaults_q->getResultArray();
    $data['static_data'] = array_shift($getResultArray);

    $project_finish = $project_data['2'];
    $srchDate = date_format(date_create_from_format('d/m/Y', "$project_finish"), 'Y-m-d');

    $client_supply_reminder_dys = $data['static_data']['client_supply_reminder_dys'];
    $date_limit_name = date('D', strtotime("$srchDate -$client_supply_reminder_dys days"));

    if($date_limit_name == 'Sun'){
      $client_supply_reminder_dys = $client_supply_reminder_dys-1;
      $set_delivery_date =  date('d/m/Y', strtotime($srchDate."  -$client_supply_reminder_dys days"));

    }elseif($date_limit_name == 'Sat'){
      $client_supply_reminder_dys = $client_supply_reminder_dys+1;
      $set_delivery_date =  date('d/m/Y', strtotime($srchDate."  -$client_supply_reminder_dys days"));

    }else{
      $set_delivery_date =  date('d/m/Y', strtotime($srchDate."  -$client_supply_reminder_dys days"));
    }


    $this->client_supply_m->update_supply_details($supply_data_id,$supply_name,$data_project_id,$quantity,$date_goods_expected,$date_goods_arrived,$delivered_by,$to_be_advised,$delivery_date,$is_deliver_to_site_select,$set_address,$description,$warehouse_selected);

    //var_dump($_FILES);



    if ( array_sum($_FILES['supply_photos']['error']) > 0 ){

      $this->client_supply_m->update_photos($supply_data_id,'');

    }else{

      $photos = $this->processUpload('supply_photos','client_supply',$data_project_id.'_supply',1);
      $get_supply_data_q = $this->client_supply_m->list_photos($supply_data_id);
      $getResultArray = $get_supply_data_q->getResultArray();
      $supply_data = array_shift($getResultArray);

      if(strlen($supply_data['photos'])>0){
        $data_photos = $supply_data['photos'].','.$photos;
      }else{
        $data_photos = $photos;
      }

      $this->client_supply_m->update_photos($supply_data_id,$data_photos);

    }

    return redirect()->to('/client_supply');
  }


  public function delete_supply($id){
    $this->client_supply_m->delete_supply($id);
    return redirect()->to('/client_supply');
  }






  /*

  public  function _upload_primary_photo($fileToUpload,$dir,$name_pref='user_'){

      $time = date("hismdY", time());
      $file = $this->request->getFile($fileToUpload);
      $ext = $file->getClientExtension();

      $newName = $name_pref.$time.'.'.$ext;

      if ($file->isValid() && !$file->hasMoved()) {
        $file->move(ROOTPATH . './uploads/'.$dir.'/', $newName);
        return 'success|'.$newName;
      }else{
        $upload_error = $file->getError();
        return 'error|'.$upload_error;
      }

    }

    */




  public function processUpload($file_data,$folder,$file_name_set='',$return_fname=''){

    $time = time(); 
    $file_counter = 1;


    if(isset($file_name_set) && $file_name_set!=''){
      $archive_registry_name = $file_name_set;
    }else{
      $archive_registry_name = 'file_upload';
    }


    $path = "docs/".$folder;
    $data_fname_arr = array();


    if ($this->request->getFileMultiple($file_data)){
     foreach($this->request->getFileMultiple($file_data) as $file){ // loop through files

        $file_name      = $file->getName();
        $file_name_arr  = explode('.',$file_name);
        $file_name_raw  = $file_name_arr[0];
        $file_ext       = $file_name_arr[1];

        $data_file_name = $archive_registry_name.'_'.$time.'_'.$file_counter.'.'.$file_ext;

        if ($file->isValid() && !$file->hasMoved()) {

          $file->move(ROOTPATH . $path.'/', $data_file_name);
          array_push($data_fname_arr, $data_file_name);

        }else{
          $upload_error = $file->getError();
          exit;
        }

        $file_counter++;

      } // loop through files
    }


    if(isset($return_fname) && $return_fname==1){
      return implode(',', $data_fname_arr);
    }
  }







}