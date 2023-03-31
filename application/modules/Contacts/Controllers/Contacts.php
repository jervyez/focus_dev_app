<?php
// module created by Jervy 20-9-2022
namespace App\Modules\Contacts\Controllers;

use App\Controllers\BaseController;

use App\Modules\Company\Controllers\Company;
use App\Modules\Company\Models\Company_m;

use App\Modules\Users\Controllers\Users;
use App\Modules\Users\Models\Users_m;

use App\Modules\Contacts\Models\Contacts_m;


class Contacts extends BaseController {

  function __construct(){
    $this->contacts_m = new Contacts_m();
    $this->session = \Config\Services::session();
  }


  public function index($value='') {

    $this->users = new Users();
    $this->user_model = new Users_m();

    if(!$this->users->_is_logged_in() ):    
      redirect('', 'refresh');
    endif;

    $data = array();
    $data['main_content'] = 'App\Modules\Contacts\Views\contacts_v';
    $data['new_compamy_id'] = $this->session->get('item');

    $data['comp_type'] = 1;
    $data['screen'] = 'Contacts';
    $data['page_title'] = 'Contacts List';

    return view('App\Views\page',$data);

  }


  public function display_focus_contacts(){
    $q_focus_contacts = $this->contacts_m->fetch_focus_staff_all();
    $focus_contacts = $q_focus_contacts->getResultArray();

    foreach ($focus_contacts as $row) {
      echo '<tr>
      <td>'.$row['contact_name'].'</td>
      <td>'.$row['company_name'].'</td>
      <td>'.$row['direct_number'].'</td>
      <td>'.$row['mobile_number'].'</td>
      <td>'.$row['personal_mobile_number'].'</td>
      <td><a href="mailto:'.strtolower($row['general_email']).'">'.$row['general_email'].'</a></td>
      <td><a href="mailto:'.strtolower($row['personal_email']).'">'.$row['personal_email'].'</a></td>
      <td>'.$row['user_skype'].'</td>
      </tr>';
    }

  }

  public function display_contacts($contact_type){
    if($contact_type==0):
      $data['cont_c'] = $this->contacts_m->fetch_contacts();
    else:
      $data['cont_c'] = $this->contacts_m->fetch_contacts_by_company_type($contact_type);
    endif;
    
    return view('App\Modules\Contacts\Views\contacts_t',$data);
  }




}