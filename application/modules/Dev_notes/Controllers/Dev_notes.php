<?php
// module created by Jervy 20-9-2022
namespace App\Modules\Dev_notes\Controllers;

use App\Controllers\BaseController;
use App\Modules\Users\Models\Users_m;
use App\Modules\Dev_notes\Models\Dev_notes_m;

class Dev_notes extends BaseController {

  function __construct(){
    $this->dev_notes_m = new Dev_notes_m();
    $this->session = \Config\Services::session();
  }


  public function index($value='') {
    $this->user_model = new Users_m();

    $data = array();

    if($this->session->get('is_admin') != 1 ):   
      return redirect()->to('/users');
    endif;


    $list_q = $this->dev_notes_m->fetch_sections();
    $data['sections'] = $list_q->getResult();

    $programmer_list = $this->user_model->fetch_user_by_role(13);
    $data['programmer'] = $programmer_list->getResult();

    $data['screen'] = 'Dev Notes';
    $data['page_title'] = 'Dev Notes';
    $data['main_content'] = 'App\Modules\Dev_notes\Views\dev_notes_home';

    return view('App\Views\page',$data);
  }

  public function list_post_comments($post_id){
    $this->user_model = new Users_m();

    $list_post_q = $this->dev_notes_m->list_comments($post_id);

    if($list_post_q->getNumRows() >= 1){
      foreach ($list_post_q->getResult() as $post) {
        $fetch_user = $this->user_model->fetch_user($post->dn_post_user_id);
        $getResultArray = $fetch_user->getResultArray();
        $user_details = array_shift($getResultArray);

        echo '<div class="clearfix m-bottom-10 post_container">';
        echo '<div class="pull-left m-right-10"  style="height: 50px; width:50px; border-radius:10px; overflow:hidden; border: 1px solid #999999;"><img class="user_avatar img-responsive img-rounded" src="'.base_url().'/uploads/users/'.$user_details['user_profile_photo'].'"" /></div>';
        echo '<div id="" class="post_content" style="margin-left:60px; padding: 5px;  margin-bottom:10px;  background: #eee;    border-radius: 10px;    border: 1px solid #e2e2e2; ">'.nl2br($post->dn_post_details).'</div><p class="show_text pointer" style="font-weight:bold; display:none; margin-left:60px;">Show More</p></div>';
      }

    }else{
      echo '<p style="padding: 10px;    background: #eee;    border-radius: 6px;    border: 1px solid #e2e2e2;">No Posts Yet.</p>';
    }
    
  }


  public function delete_post($post_id){
    $this->dev_notes_m->delete_post($post_id);
    return redirect()->to('/dev_notes');
  }

  public function list_post($is_bugs = ''){
    $this->user_model = new Users_m();
    $list_post_q = $this->dev_notes_m->list_post($is_bugs);

    $cat_sign = ''; 
    $sign = '';

    foreach ($list_post_q->getResult() as $post) {

      if($post->dn_date_complete > 0){
        $status = 'Completed';
      }else{
        $status = 'Outstanding';
      }

      if($post->dn_prgm_user_id > 0){ 
        $fetch_user = $this->user_model->fetch_user($post->dn_prgm_user_id);
        $getResultArray = $fetch_user->getResultArray();
        $user_details = array_shift($getResultArray);

        $programmer_name = ($post->dn_date_commence != '' && $status == 'Outstanding' ? '<strong>' : '');
          $programmer_name .= $user_details['user_first_name'];
        $programmer_name .= ($post->dn_date_commence != '' && $status == 'Outstanding' ? '</strong>' : '');

      }else{
        $programmer_name = 'Un-Assigned';
      }


      switch ($post->dn_category) {
        case 'Urgent':
        $sign = '<i class="fa fa-exclamation-triangle" aria-hidden="true" style="color: red;"></i> ';
        $cat_sign = '<span Style="color: red; font-weight:bold">'.$post->dn_category.'</span>';
        break;

        case 'Important':
        $sign = '';
        $cat_sign = '<span Style="color: orange; font-weight:bold">'.$post->dn_category.'</span>';
        break;

        case 'When Time Permits':
        $sign = '';
        $cat_sign = '<span Style="color: #009688; font-weight:bold">'.$post->dn_category.'</span>';
        break;

        case 'Maybe':
        $sign = '';
        $cat_sign = '<span Style="color: #3f51b5; font-weight:bold">'.$post->dn_category.'</span>';
        break;
      }

      $is_ongoing = '';

      if($post->dn_date_commence != '' && $status == 'Outstanding'){
        $is_ongoing = '<span style="color:green;" ><i class="fa fa-cog"></i></span>';
      }else{
        $is_ongoing = '';
      }

      echo '<tr><td style="display:none;">'.$post->priority_sort.'</td><td class=""><a href="./dev_notes/view_post/'.$post->dn_id.'">'.$is_ongoing.' '.$sign.' ';
      
      echo ($post->dn_date_commence != '' ? '<span style="font-weight:bold;" >' : '');
      echo $post->dn_title;
      echo ($post->dn_date_commence != '' ? '</span>' : '');

      echo '</a></td><td>'.$cat_sign.'</td><td class="">'.$status.'</td><td class="">'.$post->dn_date_posted.'</td><td>'.$programmer_name.'</td><td>'.$post->dn_section_label.'</td><td>'.$post->unix_posted.'</td></tr>';
    }
  }

  public function add_section(){

    $dn_section = $this->request->getPost('dn_n_section');
    $this->dev_notes_m->insert_new_section($dn_section );
    return redirect()->to('/dev_notes');

  }

  public function delete_section($section_id){
    $this->dev_notes_m->delete_section($section_id);
    return redirect()->to('/dev_notes');
  }


  public function update_section(){
    $ajax_var = $this->request->getPost('ajax_var');
    $update_data = explode('|', $ajax_var);
    $this->dev_notes_m->update_section($update_data[1],$update_data[0]);
  }

  public function view_post($post_id){
    $this->user_model = new Users_m();

    $programmer_list = $this->user_model->fetch_user_by_role(13);
    $data['programmer'] = $programmer_list->getResult();

    $q_fetched = $this->dev_notes_m->view_post_detail($post_id);
    $getResultArray = $q_fetched->getResultArray();
    $data['post_detail'] = array_shift($getResultArray);

    $list_q = $this->dev_notes_m->fetch_sections();
    $data['sections'] = $list_q->getResult();

    $data['screen'] = 'Development Notes';
    $data['page_title'] = 'Development Notes';
    $data['main_content'] = 'App\Modules\Dev_notes\Views\dev_notes_view_detail';

    return view('App\Views\page',$data);
  }


  public function update_post(){
    $this->user_model = new Users_m();

    $dn_title = $this->request->getPost('notes_title');
    $dn_category = $this->request->getPost('dn_category');
    $dn_id = $this->request->getPost('post_id');
    $dn_prgm_user_id = $this->request->getPost('dn_assnmt');
    $dn_date_commence = $this->request->getPost('date_stamp');
    $dn_post_details = $this->request->getPost('comments');
    $dn_date_complete = $this->request->getPost('dn_date_complete'); 
    $dn_section = $this->request->getPost('dn_section'); 

    if($dn_date_complete != ''){

      $user_id = $this->session->get('user_id');
      $date = date("d/m/Y");
      $time = date("H:i:s");
      $type = "Completed";
      $actions = "Note is been completed: ".$dn_title;
      $this->user_model->insert_user_log($user_id,$date,$time,$actions,$dn_id,$type);
    }

    $this->dev_notes_m->update_post($dn_title,$dn_post_details,$dn_category,$dn_date_commence,$dn_date_complete,$dn_prgm_user_id,$dn_id,$dn_section);

    return redirect()->to('/dev_notes/view_post/'.$dn_id,);
  }


  public function post_comment(){
    $this->user_model = new Users_m();

    $dn_post_date = date("d/m/Y");

    $dn_tread_id = $this->request->getPost('dn_tread_id');
    $dn_post_details = $this->request->getPost('comments');

    $q_fetched = $this->dev_notes_m->view_post_detail($dn_tread_id);
    $getResultArray = $q_fetched->getResultArray();
    $post_detail = array_shift($getResultArray);

    $user_id = $this->session->get('user_id');
    $date = date("d/m/Y");
    $time = date("H:i:s");
    $type = "Comment";
    $actions = "New comments for: ".$post_detail['dn_title'];

    $this->user_model->insert_user_log($user_id,$date,$time,$actions,$dn_tread_id,$type);
    $this->dev_notes_m->post_comment($dn_post_date,$user_id,$dn_tread_id,$dn_post_details);

    return redirect()->to('/dev_notes/view_post/'.$dn_tread_id,);
  }

  public function post(){
    $this->user_model = new Users_m();

    $dn_user_post = $this->session->get('user_id');
    $dn_title = $this->request->getPost('notes_title');
    $dn_post_details = $this->request->getPost('comments');
    $dn_category = $this->request->getPost('dn_category');
    $dn_date_posted = date('d/m/Y');
    $dn_date_commence = $this->request->getPost('date_stamp');
    $dn_prgm_user_id = $this->request->getPost('dn_assnmt');
    $dn_section = $this->request->getPost('dn_section');
    $dn_bugs = $this->request->getPost('dn_bugs');

    $user_id = $this->session->get('user_id');
    $date = date("d/m/Y");
    $time = date("H:i:s");
    $type = "New Notes";
    $actions = "Insert new note is added: ".$dn_title;

    $post_id = $this->dev_notes_m->insert_post($dn_user_post,$dn_title, $dn_post_details, $dn_category, $dn_date_posted ,  $dn_date_commence, $dn_prgm_user_id, $dn_section, $dn_bugs );
    $this->user_model->insert_user_log($user_id,$date,$time,$actions,$post_id,$type);

    return redirect()->to('/dev_notes');

  }








}