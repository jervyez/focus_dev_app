<?php date_default_timezone_set("Australia/Perth");  // date is set to perth and important setting for diff timezone acounts ?>

<?php use App\Modules\Projects\Controllers\Projects; ?>
<?php $this->projects = new Projects(); ?>

<?php use App\Modules\Company\Controllers\Company; ?>
<?php $this->company = new Company(); ?>

<?php use App\Modules\Bulletin_board\Controllers\Bulletin_board; ?>
<?php $this->bulletin_board = new Bulletin_board(); ?>

<?php use App\Modules\Schedule\Controllers\Schedule; ?>
<?php $this->schedule = new Schedule(); ?>

<?php use App\Modules\Admin\Models\Admin_m; ?>
<?php $this->admin_m = new Admin_m(); ?>

<?php //$this->load->module('company'); ?>
<?php //$this->load->module('projects'); ?>
<?php //$this->load->module('bulletin_board'); ?>
<?php //$this->load->module('schedule'); ?>
<?php //$this->load->model('admin_m'); ?>
<?php  echo $tab ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>/css/vue-select.css">

<script src="<?php echo base_url(); ?>/js/vue.js"></script>
<script src="<?php echo base_url(); ?>/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>/js/vue-select.js"></script>
<script src="<?php echo base_url(); ?>/js/vue-combobox.min.js"></script>
<script src="<?php echo base_url(); ?>/js/jmespath.js"></script>
<script src="<?php echo base_url(); ?>/js/axios.min.js"></script>

<!-- title bar -->

<style>
  .expired{
    color: red !important;
  }

  .induction_watched{
    color: blue !important;
  }

</style>
<input type = "hidden" id = "base_url" value = "<?php echo base_url() ?>">
<div class="container-fluid head-control">
  <div class="container-fluid">
    <div class="row">

      <div class="col-md-5 col-sm-4 col-xs-12 pull-left">
        <header class="page-header">
          <h3><?php $datestring = "%l, %F %d, %Y"; $time = time(); //use time() for timestamp  ?>
            <?php echo $screen; ?> Screen<br><small><?php //echo mdate($datestring, $time); #echo date("l, F d, Y"); ?></small>
          </h3>
        </header>
      </div>

      <div class="page-nav-options col-md-7 col-sm-8 col-xs-12 pull-right hidden-xs">
        <ul class="nav nav-tabs navbar-right">
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>"><i class="fa fa-home"></i> Home</a>
          </li>
          <li>
            <a href="<?php echo base_url(); ?>induction_health_safety" class = "active"><i class="fa fa-home"></i> Induction Site Staff</a>
          </li>
          <li>
            <a href="<?php echo base_url(); ?>induction_health_safety/inductions_projects"><i class="fa fa-home"></i> Induction Projects</a>
          </li>
           <li>
            <a href="<?php echo base_url(); ?>induction_health_safety/inductions_videos"><i class="fa fa-home"></i> Uploading Video for Induction</a>
          </li>
          <li>
            <a href="<?php echo base_url(); ?>induction_health_safety/induction_slide_editor_view"><i class="fa fa-home"></i> Induction Slide Templates</a>
          </li>

          <?php //if($this->session->userdata('is_admin') ==  1 || $this->session->userdata('induction_archive_upload') == 1): ?>
          <li>
            <a href="<?php echo base_url(); ?>induction_health_safety/archive_documents"><i class="fa fa-file-text-o"></i> Archive Documents</a>
          </li>
        <?php //endif; ?>
        </ul>
      </div>

    </div>
  </div>
</div>
<!-- title bar -->

<div class="container-fluid" id = "ihs_app">
  <!-- Example row of columns -->
  <div class="row">   
    <?php echo view('assets/sidebar'); ?>    
    <?php //$this->load->view('assets/sidebar'); ?>
    <div class="section col-sm-12 col-md-11 col-lg-11">
      <div class="container-fluid">
      
        <div class="row" style = "border-bottom: 1px solid; border-color: #CCC">
          <div class="col-lg-4 col-md-12 hidden-md hidden-sm hidden-xs">
            <?php if(@$this->session->get('project_deleted')): ?>
              <div class="m-15">
                <div class="border-less-box alert alert-danger fade in">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                  <h4>Opps! No turning back now!</h4>
                  <?php echo $this->session->get('project_deleted');?>
                </div>
              </div>
            <?php endif; ?>
            <div class="box-head pad-10 clearfix">
                <label style = "font-size: 21px"><?php echo $screen; ?> List</label><span> (<a href="#" data-placement="right" class="popover-test" title="" data-content="Hello there mate! Welcome to the clients screen." data-original-title="Welcome">?</a>)</span>
                <p>This is where the Induction, Health and Safety are listed.</p>
                <p><a href="#" class="tooltip-test" title="Tooltip">This link</a> and <a href="#" class="tooltip-test" title="Tooltip">that link</a> should have tooltips on hover.</p>               
            </div>


          </div>

          <div class="col-lg-8 col-md-12 hidden-md hidden-sm hidden-xs pad-5">
           <!--  <button type = "button" class = "btn btn-warning btn-sm pull-right" v-on:click = "showEditInductionSlide">View Induction Slide Template</button>
            <button type = "button" class = "btn btn-success btn-sm pull-right" v-on:click = "showInductionVideos">View Induction Videos</button>
            <button type = "button" class = "btn btn-primary btn-sm pull-right" v-on:click = "showInductionProjects">View Prjects for Induction</button> -->
          </div>
        </div>

        
        <div class="box-area">
          <div class="col-sm-12">
            <button type = "button" class = "btn btn-warning btn-sm pull-right"  v-on:click = "view_site_login">View Site Login</button>
            <button type = "button" class = "btn btn-primary btn-sm pull-right" v-on:click = "show_site_staff_certificates">View Site Staff Covid Certificates</button>
          </div>
          <div class="box-tabs m-bottom-15 pad-5" >
            <ul class="nav nav-tabs pull-right">
              <li class = "active"><a data-toggle="tab" href="#focusSiteStaff">Focus Site Staff</a></li>
              <li><a data-toggle="tab" href="#ContractorSiteStaff">Contractor Site Staff</a></li>
              <li><a data-toggle="tab" href="#SubmittedContractorSiteStaff">Submitted from Contractors</a></li>
              <li><a data-toggle="tab" href="#UnsubmittedContractorSiteStaff">Unsubmitted Site Staff</a></li>
            </ul>

            <div class="tab-content">
              <div id="focusSiteStaff" class="tab-pane fade in active row">
                
                <div class="col-sm-12 pad-5">
                  <b style = "font-size: 20px">Focus Shopfit – Site Staff</b> <input type = "text" class = "input-sm pull-right" placeholder = "Type Search here..." v-model = "searchSiteStaff">
                  &nbsp;&nbsp;&nbsp;
                  <b style = "color: red">Total No of Site Staff: {{ site_staff.length }} </b>
                  &nbsp;&nbsp;&nbsp;
                  <b style = "color: orange">Total Site Staff with Induction: {{ totalFocusStaffInduction }} </b>

                  
                </div>
                
                <div class="col-sm-12 pad-10">
                  <div class="col-sm-12 pad-5" style = "height: 500px; overflow:auto">
                    <table id="fixTable" class = "table table-condensed table-bordered m-bottom-0 table-striped fancyTable" style = "font-size: 12px">
                      <thead>
                        <tr>
                          <th>Site Staff Name</th>
                          <th>Position</th>
                          <th>Mobile Number</th>
                          <th>E-mail</th>
                          <th>Company Induction</th>
                          <th>Emergency Contact Details</th>
                          <th>License and Certificates</th>
                          <th>Training Records</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="site_staff in filterSiteStaff">
                          <td>{{ site_staff.user_first_name +" "+ site_staff.user_last_name}}</td>
                          <td>{{ site_staff.role_types }}</td>
                          <td>{{ site_staff.mobile_number }}</td>
                          <td>{{ site_staff.general_email }}</td>
                          <td v-if="site_staff.general_induction_date == '0000-00-00'">Not Taken</td>
                          <td v-if="site_staff.general_induction_date !== '0000-00-00'">{{ site_staff.general_induction_date | ausdate }}</td>
                          <td>
                            <span class="badge alert-success pointer pull-right" title = "Add Emergency Contact Details" v-on:click="showAddEmergenceContacts(site_staff.user_id)"><i class="fa fa-plus-circle"></i></span>
                            <ul  v-for="emergency_contatacts in emergency_contatacts" v-if="emergency_contatacts.user_id == site_staff.user_id ">
                              <li><a href="" onclick="return false" v-on:click = "showUpdateEmergencyContacts(emergency_contatacts.sitestaff_emergency_contacts_id)" style = "font-size: 12px">{{ emergency_contatacts.contact_fname+" "+emergency_contatacts.contact_sname+" ("+emergency_contatacts.relation +") - "+ emergency_contatacts.contacts }}</a></li>
                            </ul>
                          </td>
                          <td>
                            <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px">
                              <tr>
                                <td style = "border: none">
                                  <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px"> 
                                    <tr v-for="license_certificates in license_certificates" v-if="license_certificates.user_id == site_staff.user_id">
                                      <td style = "border: none"><a href="" onclick="return false" v-on:click = "showUpdateLicenceCert(license_certificates.user_license_certificates_id)" style = "font-size: 12px" v-bind:class="{ expired: isExpired(license_certificates.expiration_date) }">{{ license_certificates.type+" ("+license_certificates.number +") "}}</a></td>
                                      <td v-if = "license_certificates.expiration_date == '0000-00-00'" class = "text-right" style = "border: none">No Expiration</td>
                                      <td v-if = "license_certificates.expiration_date !== '0000-00-00'" v-bind:class="{ expired: isExpired(license_certificates.expiration_date) }" class = "text-right" style = "border: none"> Expiration: {{ license_certificates.expiration_date | ausdate }}</td>
                                      <td><button type = "button" class = "btn btn-primary btn-xs" v-if = "license_certificates.type == 'COVID-19 Digital Certificate'" v-on:click = "view_certificate(license_certificates.image_path,0)"><i class="fa fa-paperclip"></i></button></td>
                                    </tr>
                                  </table>
                                </td>
                                <td style = "border: none"><span class="badge alert-warning pointer pull-right" title = "Add Licenses and Certificates" v-on:click="showAddLicencesCert(site_staff.user_id)"><i class="fa fa-plus-circle"></i></span></td>
                              </tr>
                            </table>
                          </td>
                          <td>
                            <span class="badge alert-info pointer pull-right" title = "Add Training Records" v-on:click="showAddTraining(site_staff.user_id)"><i class="fa fa-plus-circle"></i></span>
                            <ul v-for="training_records in training_records" v-if="training_records.user_id == site_staff.user_id " style = "font-size: 12px" >
                                <li><a href="" onclick="return false" v-on:click = "showUpdateTraining(training_records.training_records_id)" style = "font-size: 12px">{{ training_records.training_type+" ( " }} {{ training_records.date_undertaken | ausdate }}{{" ) -"+ training_records.taken_with }}</a></li>
                            </ul>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

              </div>
              
              <div id="ContractorSiteStaff" class="tab-pane fade row">
                
                <div class="col-sm-12">
                  <b style = "font-size: 20px">Contractors – Site Staff</b>
                </div>
                <div class="col-sm-12 pad-5">
                  <select class = "input-sm" v-model = "filterContSiteStaff" v-on:change = "filterByCompany">
                    <option value = '' selected="Selected">All</option>
                    <option v-for = "contractors_with_site_staff in sortContractorWithSiteStaff" :value="contractors_with_site_staff.company_id">{{ contractors_with_site_staff.company_name }}</option>
                  </select>
                  <button type = "button" class = "btn btn-success" v-on:click = "showAddContSiteStaff">Add Site Staff</button>
                  <button type = "button" class = "btn btn-success" v-on:click = "showSendSiteStaffLink">Send Site Staff Link</button>
                  &nbsp;&nbsp;&nbsp;
                  <b style = "color: red">Total No of Companies: {{ contractors_with_site_staff.length }} </b>
                  &nbsp;&nbsp;&nbsp;
                  <b style = "color: orange">Total No of Site Staff: {{ contractor_site_staff.length }} </b>
                  &nbsp;&nbsp;&nbsp;
                  <b style = "color: blue">Total Contractors Staff with Induction: {{ totalContInduction }} </b>
                  
                  <input type = "text" class = "input-sm pull-right" placeholder = "Type Search here" v-model= "searchContractorSiteStaff">
                </div>
                <div class="col-sm-12 pad-10">
                  <div class="col-sm-12 pad-5" style = "height: 500px; overflow:auto">
                    <table class = "table table-condensed table-striped table-bordered m-bottom-0 fancyTable"  style = "font-size: 12px">
                      <thead>
                        <tr>
                          <th class = "text-center">Company Name</th>
                          <th class = "text-center">Date Updated</th>
                          <th class = "text-center">Site Staff Name</th>
                          <th class = "text-center">Position</th>
                          <th class = "text-center">Is Apprentice</th>
                          <th class = "text-center">Mobile Number</th>
                          <th class = "text-center">E-mail</th>
                          <th class = "text-center">Company Induction</th>
                          <th class = "text-center">Emergency Contact Details</th>
                          <th class = "text-center">License and Certificates</th>
                          <th class = "text-center">Training Records</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <tr v-for="contractor_site_staff in filterContractorSiteStaff">
                          <td>
                            <span v-if = "checkRowCompName(contractor_site_staff.company_name)">
                              <button type = "button" class = "btn btn-warning btn-xs pull-right" title = "Send Notification" v-on:click="send_update_link(contractor_site_staff.company_id)" style = "position: relative"><i class="fa fa-paper-plane"></i></button>

                              <span style = "width: 500px">{{ contractor_site_staff.company_name }}</span><br><br>

                              <span title = "Primary Contact Person">
                                {{ contractor_site_staff.first_name + " " + contractor_site_staff.last_name}}
                              </span><br>
                              <span title = "Office Contact Number">
                                {{ contractor_site_staff.area_code + " " + contractor_site_staff.office_number}}
                              </span><br>
                              <span title = "Mobile Number">
                                {{ contractor_site_staff.mobile_number }}
                              </span>
                              <span title = "E-mail">
                                <a :href="'mailto:'+ contractor_site_staff.general_email ">{{ contractor_site_staff.general_email }}</a>
                              </span>
                            </span>

                            
                            <br><br>
                          </td>
                          <td style = "width: 500px">
                            <table width = '100%' v-if = "checkRowCompName2(contractor_site_staff.company_name)">
                              <tr>
                                <td  style = "border: 0 !important">
                                  <span style = "width: 200px">{{ contractor_site_staff.induction_date_updated | ausdate }}</span>
                                </td>
                                <td  style = "border: 0 !important">
                                  <button type = "button" class = "btn btn-success btn-xs pull-right" title = "Update Date" v-on:click="showUpdateDate(contractor_site_staff.company_id,contractor_site_staff.induction_date_updated)" style = "position: relative"><i class="fa fa-edit"></i></button>
                                </td>
                              </tr>
                            </table>
                          </td>
                          <td><a href="" onclick="return false" v-on:click = "showUpdateContractorSiteStaff(contractor_site_staff.contractor_site_staff_id)">{{ contractor_site_staff.site_staff_fname +" "+ contractor_site_staff.site_staff_sname}}</a></td>
                          <td>{{ contractor_site_staff.position }}</td>
                          <td v-if = "contractor_site_staff.is_apprentice == '1'">Yes</td>
                          <td v-if = "contractor_site_staff.is_apprentice == '0'">No</td>
                          <td>{{ contractor_site_staff.ss_mobile_number }}</td>
                          <td><a :href="'mailto:'+ contractor_site_staff.email ">{{ contractor_site_staff.email }}</a></td>
                          <td v-if = "contractor_site_staff.general_induction_date == '0000-00-00'">Not Taken</td>
                          <td v-if = "contractor_site_staff.general_induction_date !== '0000-00-00'">{{ contractor_site_staff.general_induction_date | ausdate}}</td>
                          <td>
                            <span class="badge alert-success pointer pull-right" title = "Add Emergency Contact Details" v-on:click="showAddCSSEmergenceContacts(contractor_site_staff.contractor_site_staff_id)"><i class="fa fa-plus-circle"></i></span>
                            <ul  v-for="cont_sitestaff_emergency_contatacts in cont_sitestaff_emergency_contatacts" v-if="cont_sitestaff_emergency_contatacts.user_id == contractor_site_staff.contractor_site_staff_id ">
                              <li><a href="" onclick="return false" v-on:click = "showUpdateCSSEmergencyContacts(cont_sitestaff_emergency_contatacts.sitestaff_emergency_contacts_id)" style = "font-size: 12px">{{ cont_sitestaff_emergency_contatacts.contact_fname+" "+cont_sitestaff_emergency_contatacts.contact_sname+" ("+cont_sitestaff_emergency_contatacts.relation +") - "+ cont_sitestaff_emergency_contatacts.contacts }}</a></li>
                            </ul>
                          </td>
                          <td>
                            <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px">
                              <tr>
                                <td style = "border: none">
                                  <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px"> 
                                    <tr v-for="cont_sitestaff_license_certificates in cont_sitestaff_license_certificates" v-if="cont_sitestaff_license_certificates.user_id == contractor_site_staff.contractor_site_staff_id">
                                      <td style = "border: none"><a href="" onclick="return false" v-on:click = "showUpdateCSSLicenceCert(cont_sitestaff_license_certificates.user_license_certificates_id)" style = "font-size: 12px" v-bind:class="{ expired: isExpired(cont_sitestaff_license_certificates.expiration_date) }">{{ cont_sitestaff_license_certificates.type+" ("+cont_sitestaff_license_certificates.number +") "}}</a></td>
                                      <td v-if = "cont_sitestaff_license_certificates.has_expiration == 1" v-bind:class="{ expired: isExpired(cont_sitestaff_license_certificates.expiration_date) }" class = "text-right" style = "border: none"> Expiration: {{ cont_sitestaff_license_certificates.expiration_date | ausdate }}</td>
                                      <td v-if = "cont_sitestaff_license_certificates.has_expiration == 0" class = "text-right" style = "border: none">No Expiration</td>
                                      <td><button type = "button" class = "btn btn-primary btn-xs" v-if = "cont_sitestaff_license_certificates.type == 'COVID-19 Digital Certificate'" v-on:click = "view_certificate(cont_sitestaff_license_certificates.image_path,contractor_site_staff.company_id)"><i class="fa fa-paperclip"></i></button></td>
                                    </tr>

                                  </table>
                                </td>
                                <td style = "border: none"><span class="badge alert-warning pointer pull-right" title = "Add Licenses and Certificates" v-on:click="showAddCSSLicencesCert(contractor_site_staff.contractor_site_staff_id,contractor_site_staff.company_id)"><i class="fa fa-plus-circle"></i></span></td>
                              </tr>
                            </table>

                          </td>
                          <td>
                            <span class="badge alert-info pointer pull-right" title = "Add Training Records" v-on:click="showAddCSSTraining(contractor_site_staff.contractor_site_staff_id)"><i class="fa fa-plus-circle"></i></span>
                            <ul v-for="cont_sitestaff_training_records in cont_sitestaff_training_records" v-if="cont_sitestaff_training_records.user_id == contractor_site_staff.contractor_site_staff_id " style = "font-size: 12px" >
                                <li><a href="" onclick="return false" v-on:click = "showUpdateCSSTraining(cont_sitestaff_training_records.training_records_id)" style = "font-size: 12px">{{ cont_sitestaff_training_records.training_type+" ( " }} {{ cont_sitestaff_training_records.date_undertaken | ausdate }}{{" ) -"+ cont_sitestaff_training_records.taken_with }}</a></li>
                            </ul>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <!-- <table id="fixTable1" class = "table table-condensed table-striped table-bordered m-bottom-0 fancyTable"  style = "font-size: 12px">
                      <thead>
                        <tr>
                          <th class = "text-center">Company Name</th>
                          <th class = "text-center" style = "width: 100px">Date Updated</th>
                          <th class = "text-center">Site Staff Details</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <tr v-for="css_contractors_list in css_contractors_list">
                          <td :title = "css_contractors_list.first_name + ' ' + css_contractors_list.last_name +'( ' + css_contractors_list.area_code + ' ' +css_contractors_list.office_number +' )' + '( ' + css_contractors_list.mobile_number +' )'">
                            {{ css_contractors_list.company_name }}
                            <button type = "button" class = "btn btn-warning btn-xs pull-right" title = "Send Notification" v-on:click="send_update_link(css_contractors_list.company_id)"><i class="fa fa-paper-plane"></i></button>
                            <br><br>
                            
                            <span title = "Primary Contact Person">{{ css_contractors_list.first_name + " " + css_contractors_list.last_name}}</span><br>
                            <span title = "Office Contact Number">{{ css_contractors_list.area_code + " " + css_contractors_list.office_number}}</span><br>
                            <span title = "Mobile Number">{{ css_contractors_list.mobile_number }}</span>
                            <span title = "E-mail"><a :href="'mailto:'+ css_contractors_list.general_email ">{{ css_contractors_list.general_email }}</a></span>
                          </td>
                          <td style = "width: 100px">{{ css_contractors_list.induction_date_updated | ausdate }} <button type = "button" class = "btn btn-success btn-xs pull-right" title = "Update Date" v-on:click="showUpdateDate(css_contractors_list.company_id,css_contractors_list.induction_date_updated)"><i class="fa fa-edit"></i></button></td>
                          <td>
                            <table id="myTable" class = "table table-condensed table-striped table-bordered m-bottom-0 fancyTable"  style = "font-size: 12px">
                              <thead>
                                <tr>
                                  <th class = "text-center">Site Staff Name</th>
                                  <th class = "text-center">Position</th>
                                  <th class = "text-center">Is Apprentice</th>
                                  <th class = "text-center">Mobile Number</th>
                                  <th class = "text-center">E-mail</th>
                                  <th class = "text-center">Company Induction</th>
                                  <th class = "text-center">Emergency Contact Details</th>
                                  <th class = "text-center">License and Certificates</th>
                                  <th class = "text-center">Training Records</th>
                                </tr>  
                              </thead>
                              <tbody>
                                <tr v-for="contractor_site_staff in filterContractorSiteStaff" v-if = "contractor_site_staff.company_id == css_contractors_list.company_id">
                                  <td><a href="" onclick="return false" v-on:click = "showUpdateContractorSiteStaff(contractor_site_staff.contractor_site_staff_id)">{{ contractor_site_staff.site_staff_fname +" "+ contractor_site_staff.site_staff_sname}}</a></td>
                                  <td>{{ contractor_site_staff.position }}</td>
                                  <td v-if = "contractor_site_staff.is_apprentice == '1'">Yes</td>
                                  <td v-if = "contractor_site_staff.is_apprentice == '0'">No</td>
                                  <td>{{ contractor_site_staff.mobile_number }}</td>
                                  <td><a :href="'mailto:'+ contractor_site_staff.email ">{{ contractor_site_staff.email }}</a></td>
                                  <td v-if = "contractor_site_staff.general_induction_date == '0000-00-00'">Not Taken</td>
                                  <td v-if = "contractor_site_staff.general_induction_date !== '0000-00-00'">{{ contractor_site_staff.general_induction_date | ausdate}}</td>
                                  <td>
                                    <span class="badge alert-success pointer pull-right" title = "Add Emergency Contact Details" v-on:click="showAddCSSEmergenceContacts(contractor_site_staff.contractor_site_staff_id)"><i class="fa fa-plus-circle"></i></span>
                                    <ul  v-for="cont_sitestaff_emergency_contatacts in cont_sitestaff_emergency_contatacts" v-if="cont_sitestaff_emergency_contatacts.user_id == contractor_site_staff.contractor_site_staff_id ">
                                      <li><a href="" onclick="return false" v-on:click = "showUpdateCSSEmergencyContacts(cont_sitestaff_emergency_contatacts.sitestaff_emergency_contacts_id)" style = "font-size: 12px">{{ cont_sitestaff_emergency_contatacts.contact_fname+" "+cont_sitestaff_emergency_contatacts.contact_sname+" ("+cont_sitestaff_emergency_contatacts.relation +") - "+ cont_sitestaff_emergency_contatacts.contacts }}</a></li>
                                    </ul>
                                  </td>
                                  <td>
                                    <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px">
                                      <tr>
                                        <td style = "border: none">
                                          <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px"> 
                                            <tr v-for="cont_sitestaff_license_certificates in cont_sitestaff_license_certificates" v-if="cont_sitestaff_license_certificates.user_id == contractor_site_staff.contractor_site_staff_id">
                                              <td style = "border: none"><a href="" onclick="return false" v-on:click = "showUpdateCSSLicenceCert(cont_sitestaff_license_certificates.user_license_certificates_id)" style = "font-size: 12px" v-bind:class="{ expired: isExpired(cont_sitestaff_license_certificates.expiration_date) }">{{ cont_sitestaff_license_certificates.type+" ("+cont_sitestaff_license_certificates.number +") "}}</a></td>
                                              <td v-if = "cont_sitestaff_license_certificates.has_expiration == 1" v-bind:class="{ expired: isExpired(cont_sitestaff_license_certificates.expiration_date) }" class = "text-right" style = "border: none"> Expiration: {{ cont_sitestaff_license_certificates.expiration_date | ausdate }}</td>
                                              <td v-if = "cont_sitestaff_license_certificates.has_expiration == 0" class = "text-right" style = "border: none">No Expiration</td>
                                            </tr>

                                          </table>
                                        </td>
                                        <td style = "border: none"><span class="badge alert-warning pointer pull-right" title = "Add Licenses and Certificates" v-on:click="showAddCSSLicencesCert(contractor_site_staff.contractor_site_staff_id)"><i class="fa fa-plus-circle"></i></span></td>
                                      </tr>
                                    </table>

                                  </td>
                                  <td>
                                    <span class="badge alert-info pointer pull-right" title = "Add Training Records" v-on:click="showAddCSSTraining(contractor_site_staff.contractor_site_staff_id)"><i class="fa fa-plus-circle"></i></span>
                                    <ul v-for="cont_sitestaff_training_records in cont_sitestaff_training_records" v-if="cont_sitestaff_training_records.user_id == contractor_site_staff.contractor_site_staff_id " style = "font-size: 12px" >
                                        <li><a href="" onclick="return false" v-on:click = "showUpdateCSSTraining(cont_sitestaff_training_records.training_records_id)" style = "font-size: 12px">{{ cont_sitestaff_training_records.training_type+" ( " }} {{ cont_sitestaff_training_records.date_undertaken | ausdate }}{{" ) -"+ cont_sitestaff_training_records.taken_with }}</a></li>
                                    </ul>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table> -->
                  </div>
                </div>


              </div>

              <div id="SubmittedContractorSiteStaff" class="tab-pane fade row">
                <div class="col-sm-12"><b style = "font-size: 20px">From Contractors – Site Staff</b></div>
                <div class="col-sm-12 pad-5">
                  <label for="">Select Contractor</label>
                  <select class = "input-sm" v-model = "TempContSiteStaff" v-on:change = "filterSubmittedTempSiteStaff">
                    <option value="All" selected="selected">All</option>
                    <option v-for = "contractors_submitted in sortContractorSubmitted" v-if = "contractors_submitted.induction_date_sent !== '0000-00-00'" :value="contractors_submitted.company_id">{{ contractors_submitted.company_name }}</option>
                  </select>
                 <!--  <button type = "button" class = "btn btn-success btn-sm" v-on:click = "approveSelected">Approve Selected Company</button> -->
                  <button type = "button" class = "btn btn-success btn-sm" v-on:click = "approveSelectedSiteStaff">Approve Selected Site Staff</button>
                  &nbsp;&nbsp;&nbsp;
                  <b style = "color: red">Total No of Companies: {{ totalTempContNo }} </b>
                  &nbsp;&nbsp;&nbsp;
                  <b style = "color: orange">Total No of Site Staff: {{ total_submitted_staff }} </b>
                  <input type = "text" class = "input-sm pull-right" placeholder = "Type Search here" v-model= "searchContractorSiteStaffSubmitted">
                </div>
                <div class="col-sm-12 pad-10">
                  <div class="col-sm-12 pad-5" style = "height: 500px; overflow:auto">
                    <table id="myTable" class = "table table-condensed table-striped table-bordered m-bottom-0 fancyTable"  style = "font-size: 12px">
                      <thead>
                        <tr>
                          <th style = "width: 20px"></th>
                          <th class = "text-center">Company Name</th>
                          <th class = "text-center">Date Submitted</th>

                          <th class = "text-center">Site Staff Name</th>
                          <th class = "text-center">Position</th>
                          <th class = "text-center">Is Apprentice</th>
                          <th class = "text-center">Mobile Number</th>
                          <th class = "text-center">E-mail</th>
                          <th class = "text-center">Emergency Contact Details</th>
                          <th class = "text-center">License and Certificates</th>
                          <th class = "text-center">Training Records</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <tr v-for="uploaded_site_staff in filterUploadedSiteStaff" v-if="uploaded_site_staff.induction_date_sent !== '0000-00-00' && uploaded_site_staff.is_completed == 1 ">

                          <td><input type="checkbox" :value="uploaded_site_staff.temp_contractors_staff_id" v-model="chkcontSiteStaffID"></td>
                          
                          <td>
                            <span v-if = "checkRowCompNameSubmitted(uploaded_site_staff.company_name)">
                              {{ uploaded_site_staff.company_name }}<br><br>
                              <span title = "Primary Contact Person">{{ uploaded_site_staff.first_name + " " + uploaded_site_staff.last_name}}</span><br>
                              <span title = "Office Contact Number">{{ uploaded_site_staff.area_code + " " + uploaded_site_staff.office_number}}</span><br>
                              <span title = "Mobile Number">{{ uploaded_site_staff.mobile_number }}</span><br>
                              <span title = "E-mail"><a :href="'mailto:'+ temp_contractors_list.email ">{{ uploaded_site_staff.email }}</a></span>
                            </span>
                          </td>
                          <td>{{ uploaded_site_staff.induction_date_sent | ausdate}}</td>

                          <td>{{ uploaded_site_staff.staff_fname +" "+ uploaded_site_staff.staff_sname}}</td>
                          <td>{{ uploaded_site_staff.position }}</td>
                          <td v-if="uploaded_site_staff.is_apprentice == '1'">Yes</td>
                          <td v-if="uploaded_site_staff.is_apprentice == '0'">No</td>
                          <td>{{ uploaded_site_staff.ss_mobile_number }}</td>
                          <td><a :href="'mailto:'+ uploaded_site_staff.email ">{{ uploaded_site_staff.email }}</a></td>
                          <td>                            
                            <b>{{ uploaded_site_staff.emergency_contact_fname +" "+ uploaded_site_staff.emergency_contact_sname}}</b>
                            <br>{{ uploaded_site_staff.relation }}
                            <br>{{ uploaded_site_staff.emergency_contact_number }}
                          </td>
                          <td>
                            <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px">
                              <tr>
                                <td style = "border: none">
                                  <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px"> 
                                    <tr v-for="temp_cont_lc in temp_cont_lc" v-if="temp_cont_lc.temp_contractors_staff_id == uploaded_site_staff.temp_contractors_staff_id">
                                      <td style = "border: none">{{ temp_cont_lc.lc_type+" ("+temp_cont_lc.lc_number +") "}}</td>
                                      <td v-if= "temp_cont_lc.has_expiration == 1" v-bind:class="{ expired: isExpired(temp_cont_lc.lc_expiration_date) }" class = "text-right" style = "border: none"> Expiration: {{ temp_cont_lc.lc_expiration_date | ausdate }}</td>
                                      <td v-if= "temp_cont_lc.has_expiration == 0" class = "text-right" style = "border: none">No Expiration</td>
                                      <td><button type = "button" class = "btn btn-primary btn-xs" v-if = "temp_cont_lc.lc_type == 'COVID-19 Digital Certificate'" v-on:click = "view_certificate(temp_cont_lc.image_path,uploaded_site_staff.company_id)"><i class="fa fa-paperclip"></i></button></td>
                                    </tr>

                                  </table>
                                </td>
                              </tr>
                            </table>

                          </td>
                          <td>
                            <ul v-for="temp_training_records in temp_training_records" v-if="temp_training_records.temp_contractors_staff_id == uploaded_site_staff.temp_contractors_staff_id " style = "font-size: 12px" >
                                <li>{{ temp_training_records.training +" ( " }} {{ temp_training_records.date_undertaken | ausdate }}{{" ) -"+ temp_training_records.location }}</li>
                            </ul>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <!-- <table id="fixTable2" class = "table table-condensed table-striped table-bordered m-bottom-0 fancyTable"  style = "font-size: 12px">
                      <thead>
                        <tr>
                          <th style = "width: 20px"></th>
                          <th class = "text-center">Company Name</th>
                          <th class = "text-center">Date Submitted</th>
                          <th class = "text-center">Site Staffs</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <tr v-for="temp_contractors_list in filterTempContSiteStaff" v-if="temp_contractors_list.induction_date_sent !== '0000-00-00' ">
                          <td><input type="checkbox" :value="temp_contractors_list.company_id" v-model="chkCompID"></td>
                          <td>
                            {{ temp_contractors_list.company_name }}<br><br>
                            <span title = "Primary Contact Person">{{ temp_contractors_list.first_name + " " + temp_contractors_list.last_name}}</span><br>
                            <span title = "Office Contact Number">{{ temp_contractors_list.area_code + " " + temp_contractors_list.office_number}}</span><br>
                            <span title = "Mobile Number">{{ temp_contractors_list.mobile_number }}</span><br>
                            <span title = "E-mail"><a :href="'mailto:'+ temp_contractors_list.email ">{{ temp_contractors_list.email }}</a></span>
                          </td>
                          <td>{{ temp_contractors_list.induction_date_sent | ausdate}}</td>
                          <td>
                            <table id="myTable" class = "table table-condensed table-striped table-bordered m-bottom-0 fancyTable"  style = "font-size: 12px">
                              <thead>
                                <tr>
                                  <th style = "width: 20px"></th>
                                  <th class = "text-center">Site Staff Name</th>
                                  <th class = "text-center">Position</th>
                                  <th class = "text-center">Is Apprentice</th>
                                  <th class = "text-center">Mobile Number</th>
                                  <th class = "text-center">E-mail</th>
                                  <th class = "text-center">Emergency Contact Details</th>
                                  <th class = "text-center">License and Certificates</th>
                                  <th class = "text-center">Training Records</th>
                                </tr>  
                              </thead>
                              <tbody>
                                <tr v-for="uploaded_site_staff in filterUploadedSiteStaff" v-if="temp_contractors_list.company_id == uploaded_site_staff.company_id">
                                  <td><input type="checkbox" :value="uploaded_site_staff.temp_contractors_staff_id" v-model="chkcontSiteStaffID"></td>
                                  <td>{{ uploaded_site_staff.staff_fname +" "+ uploaded_site_staff.staff_sname}}</td>
                                  <td>{{ uploaded_site_staff.position }}</td>
                                  <td v-if="uploaded_site_staff.is_apprentice == '1'">Yes</td>
                                  <td v-if="uploaded_site_staff.is_apprentice == '0'">No</td>
                                  <td>{{ uploaded_site_staff.mobile_number }}</td>
                                  <td><a :href="'mailto:'+ uploaded_site_staff.email ">{{ uploaded_site_staff.email }}</a></td>
                                  <td>                            
                                    <b>{{ uploaded_site_staff.emergency_contact_fname +" "+ uploaded_site_staff.emergency_contact_sname}}</b>
                                    <br>{{ uploaded_site_staff.relation }}
                                    <br>{{ uploaded_site_staff.emergency_contact_number }}
                                  </td>
                                  <td>
                                    <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px">
                                      <tr>
                                        <td style = "border: none">
                                          <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px"> 
                                            <tr v-for="temp_cont_lc in temp_cont_lc" v-if="temp_cont_lc.temp_contractors_staff_id == uploaded_site_staff.temp_contractors_staff_id">
                                              <td style = "border: none">{{ temp_cont_lc.lc_type+" ("+temp_cont_lc.lc_number +") "}}</td>
                                              <td v-if= "temp_cont_lc.has_expiration == 1" v-bind:class="{ expired: isExpired(temp_cont_lc.lc_expiration_date) }" class = "text-right" style = "border: none"> Expiration: {{ temp_cont_lc.lc_expiration_date | ausdate }}</td>
                                              <td v-if= "temp_cont_lc.has_expiration == 0" class = "text-right" style = "border: none">No Expiration</td>
                                            </tr>

                                          </table>
                                        </td>
                                      </tr>
                                    </table>

                                  </td>
                                  <td>
                                    <ul v-for="temp_training_records in temp_training_records" v-if="temp_training_records.temp_contractors_staff_id == uploaded_site_staff.temp_contractors_staff_id " style = "font-size: 12px" >
                                        <li>{{ temp_training_records.training +" ( " }} {{ temp_training_records.date_undertaken | ausdate }}{{" ) -"+ temp_training_records.location }}</li>
                                    </ul>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                          
                        </tr>
                      </tbody>
                    </table> -->
                  </div>
                </div>
              </div>

              <div id="UnsubmittedContractorSiteStaff" class="tab-pane fade row">
                <div class="col-sm-12"><b style = "font-size: 20px">Unsubmitted Site Staff</b></div>
                <div class="col-sm-12 pad-5">
                  <label for="">Select Contractor</label>
                  <select class = "input-sm" v-model = "TempContSiteStaff" v-on:change = "filterSubmittedTempSiteStaff">
                    <option value="All" selected="selected">All</option>
                    <option v-for = "contractors_submitted in sortContractorSubmitted" v-if = "contractors_submitted.induction_date_sent == '0000-00-00'" :value="contractors_submitted.company_id">{{ contractors_submitted.company_name }}</option>
                  </select>
                </div>
                <div class="col-sm-12 pad-10">
                  <div class="col-sm-12 pad-5" style = "height: 500px; overflow:auto">
                    <table id="myTable" class = "table table-condensed table-striped table-bordered m-bottom-0 fancyTable"  style = "font-size: 12px">
                      <thead>
                        <tr>
                          <th style = "width: 20px"></th>
                          <th class = "text-center">Company Name</th>
                          <th class = "text-center">Date Submitted</th>

                          <th class = "text-center">Site Staff Name</th>
                          <th class = "text-center">Position</th>
                          <th class = "text-center">Is Apprentice</th>
                          <th class = "text-center">Mobile Number</th>
                          <th class = "text-center">E-mail</th>
                          <th class = "text-center">Emergency Contact Details</th>
                          <th class = "text-center">License and Certificates</th>
                          <th class = "text-center">Training Records</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <tr v-for="uploaded_site_staff in filterUploadedSiteStaff" v-if="uploaded_site_staff.induction_date_sent == '0000-00-00' ">

                          <td><input type="checkbox" :value="uploaded_site_staff.temp_contractors_staff_id" v-model="chkcontSiteStaffID"></td>
                          
                          <td>
                            <span v-if = "checkRowCompNameSubmitted(uploaded_site_staff.company_name)">
                              {{ uploaded_site_staff.company_name }}<br><br>
                              <span title = "Primary Contact Person">{{ uploaded_site_staff.first_name + " " + uploaded_site_staff.last_name}}</span><br>
                              <span title = "Office Contact Number">{{ uploaded_site_staff.area_code + " " + uploaded_site_staff.office_number}}</span><br>
                              <span title = "Mobile Number">{{ uploaded_site_staff.mobile_number }}</span><br>
                              <span title = "E-mail"><a :href="'mailto:'+ temp_contractors_list.email ">{{ uploaded_site_staff.email }}</a></span>
                            </span>
                          </td>
                          <td>{{ uploaded_site_staff.induction_date_sent | ausdate}}</td>

                          <td>{{ uploaded_site_staff.staff_fname +" "+ uploaded_site_staff.staff_sname}}</td>
                          <td>{{ uploaded_site_staff.position }}</td>
                          <td v-if="uploaded_site_staff.is_apprentice == '1'">Yes</td>
                          <td v-if="uploaded_site_staff.is_apprentice == '0'">No</td>
                          <td>{{ uploaded_site_staff.ss_mobile_number }}</td>
                          <td><a :href="'mailto:'+ uploaded_site_staff.email ">{{ uploaded_site_staff.email }}</a></td>
                          <td>                            
                            <b>{{ uploaded_site_staff.emergency_contact_fname +" "+ uploaded_site_staff.emergency_contact_sname}}</b>
                            <br>{{ uploaded_site_staff.relation }}
                            <br>{{ uploaded_site_staff.emergency_contact_number }}
                          </td>
                          <td>
                            <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px">
                              <tr>
                                <td style = "border: none">
                                  <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px"> 
                                    <tr v-for="temp_cont_lc in temp_cont_lc" v-if="temp_cont_lc.temp_contractors_staff_id == uploaded_site_staff.temp_contractors_staff_id">
                                      <td style = "border: none">{{ temp_cont_lc.lc_type+" ("+temp_cont_lc.lc_number +") "}}</td>
                                      <td v-if= "temp_cont_lc.has_expiration == 1" v-bind:class="{ expired: isExpired(temp_cont_lc.lc_expiration_date) }" class = "text-right" style = "border: none"> Expiration: {{ temp_cont_lc.lc_expiration_date | ausdate }}</td>
                                      <td v-if= "temp_cont_lc.has_expiration == 0" class = "text-right" style = "border: none">No Expiration</td>
                                    </tr>

                                  </table>
                                </td>
                              </tr>
                            </table>

                          </td>
                          <td>
                            <ul v-for="temp_training_records in temp_training_records" v-if="temp_training_records.temp_contractors_staff_id == uploaded_site_staff.temp_contractors_staff_id " style = "font-size: 12px" >
                                <li>{{ temp_training_records.training +" ( " }} {{ temp_training_records.date_undertaken | ausdate }}{{" ) -"+ temp_training_records.location }}</li>
                            </ul>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <!-- <table id="fixTable2" class = "table table-condensed table-striped table-bordered m-bottom-0 fancyTable"  style = "font-size: 12px">
                      <thead>
                        <tr>
                          <th style = "width: 20px"></th>
                          <th class = "text-center">Company Name</th>
                          <th class = "text-center">Date Submitted</th>
                          <th class = "text-center">Site Staffs</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <tr v-for="temp_contractors_list in filterUnsubmittedTempContSiteStaff" v-if="temp_contractors_list.induction_date_sent == '0000-00-00'">
                          <td><input type="checkbox" :value="temp_contractors_list.company_id" v-model="chkCompID"></td>
                          <td>
                            {{ temp_contractors_list.company_name }}<br><br>
                            <span title = "Primary Contact Person">{{ temp_contractors_list.first_name + " " + temp_contractors_list.last_name}}</span><br>
                            <span title = "Office Contact Number">{{ temp_contractors_list.area_code + " " + temp_contractors_list.office_number}}</span><br>
                            <span title = "Mobile Number">{{ temp_contractors_list.mobile_number }}</span><br>
                            <span title = "E-mail"><a :href="'mailto:'+ temp_contractors_list.email ">{{ temp_contractors_list.email }}</a></span>
                          </td>
                          <td>{{ temp_contractors_list.induction_date_sent | ausdate}}</td>
                          <td>
                            <table id="myTable" class = "table table-condensed table-striped table-bordered m-bottom-0 fancyTable"  style = "font-size: 12px">
                              <thead>
                                <tr>
                                  <th style = "width: 20px"></th>
                                  <th class = "text-center">Site Staff Name</th>
                                  <th class = "text-center">Position</th>
                                  <th class = "text-center">Is Apprentice</th>
                                  <th class = "text-center">Mobile Number</th>
                                  <th class = "text-center">E-mail</th>
                                  <th class = "text-center">Emergency Contact Details</th>
                                  <th class = "text-center">License and Certificates</th>
                                  <th class = "text-center">Training Records</th>
                                </tr>  
                              </thead>
                              <tbody>
                                <tr v-for="uploaded_site_staff in filterUploadedSiteStaff" v-if="temp_contractors_list.company_id == uploaded_site_staff.company_id">
                                  <td><input type="checkbox" :value="uploaded_site_staff.temp_contractors_staff_id" v-model="chkcontSiteStaffID"></td>
                                  <td>{{ uploaded_site_staff.staff_fname +" "+ uploaded_site_staff.staff_sname}}</td>
                                  <td>{{ uploaded_site_staff.position }}</td>
                                  <td v-if="uploaded_site_staff.is_apprentice == '1'">Yes</td>
                                  <td v-if="uploaded_site_staff.is_apprentice == '0'">No</td>
                                  <td>{{ uploaded_site_staff.mobile_number }}</td>
                                  <td><a :href="'mailto:'+ uploaded_site_staff.email ">{{ uploaded_site_staff.email }}</a></td>
                                  <td>                            
                                    <b>{{ uploaded_site_staff.emergency_contact_fname +" "+ uploaded_site_staff.emergency_contact_sname}}</b>
                                    <br>{{ uploaded_site_staff.relation }}
                                    <br>{{ uploaded_site_staff.emergency_contact_number }}
                                  </td>
                                  <td>
                                    <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px">
                                      <tr>
                                        <td style = "border: none">
                                          <table class = "table table-condensed" style = "width: 100%; padding: 0px; margin: 0px"> 
                                            <tr v-for="temp_cont_lc in temp_cont_lc" v-if="temp_cont_lc.temp_contractors_staff_id == uploaded_site_staff.temp_contractors_staff_id">
                                              <td style = "border: none">{{ temp_cont_lc.lc_type+" ("+temp_cont_lc.lc_number +") "}}</td>
                                              <td v-if= "temp_cont_lc.has_expiration == 1" v-bind:class="{ expired: isExpired(temp_cont_lc.lc_expiration_date) }" class = "text-right" style = "border: none"> Expiration: {{ temp_cont_lc.lc_expiration_date | ausdate }}</td>
                                              <td v-if= "temp_cont_lc.has_expiration == 0" class = "text-right" style = "border: none">No Expiration</td>
                                            </tr>

                                          </table>
                                        </td>
                                      </tr>
                                    </table>

                                  </td>
                                  <td>
                                    <ul v-for="temp_training_records in temp_training_records" v-if="temp_training_records.temp_contractors_staff_id == uploaded_site_staff.temp_contractors_staff_id " style = "font-size: 12px" >
                                        <li>{{ temp_training_records.training +" ( " }} {{ temp_training_records.date_undertaken | ausdate }}{{" ) -"+ temp_training_records.location }}</li>
                                    </ul>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                          
                        </tr>
                      </tbody>
                    </table> -->
                  </div>
                </div>
              </div>


            </div>
           
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="update_date_updated" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Date Updated</h4>
        </div>
        <!-- <form @submit="checkAddECForm" action="<?php echo base_url() ?>induction_health_safety/add_emergency_contact" method="post"> -->
        <div class="modal-body row">
          <div class="col-sm-12 pad-5"><input type="date" v-model="dateUpdated" class = "form-control input-sm"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-success" v-on:click="clkUpdateDateUpdated" data-dismiss="modal">Update</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        <!-- </form> -->
      </div>
        
    </div>
  </div>

  <div class="modal fade" id="add_emergency_contact" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Emergency Contacts</h4>
        </div>
        <!-- <form @submit="checkAddECForm" action="<?php echo base_url() ?>induction_health_safety/add_emergency_contact" method="post"> -->
        <div class="modal-body row">
          <div v-if="errors.length" class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <b>Please correct the following error(s):</b>
            <ul>
              <li v-for="error in errors">{{ error }}</li>
            </ul>
          </div>
          <!-- <input type="hidden" name = "is_contractors" v-model="is_contractors">
          <input type="hidden" name = "user_id" v-model="user_id"> -->
          <div class="col-sm-3 pad-5">First Name</div>
          <div class="col-sm-9 pad-5"><input type = "text" class = "form-control input-sm" name = "ecFName" name = "ecFName" v-model = "ecFName"></div>
          <div class="col-sm-3 pad-5">Last Name</div>
          <div class="col-sm-9 pad-5"><input type = "text" class = "form-control input-sm" name = "ecSName" v-model = "ecSName"></div>
          <div class="col-sm-3 pad-5">Relation</div>
          <div class="col-sm-9 pad-5"><input type = "text" class = "form-control input-sm" name = "ecRelation" v-model = "ecRelation"></div>
          <div class="col-sm-3 pad-5">Contact Numbers</div>
          <div class="col-sm-9 pad-5"><input type = "text" class = "form-control input-sm" name = "ecContacts" v-model = "ecContacts" v-on:keyup="formatPhoneNumber"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-success" v-on:click="addEmergencyContacts" data-dismiss="modal">Add</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        <!-- </form> -->
      </div>
        
    </div>
  </div>

  <div class="modal fade" id="update_emergency_contact" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Emergency Contacts</h4>
        </div>
        <!-- <form @submit="checkAddECForm" action="<?php echo base_url() ?>induction_health_safety/update_emergency_contact" method="post"> -->
        <div class="modal-body row">
    
          <!-- <input type="hidden" name = "sitestaff_emergency_contacts_id" id = "sitestaff_emergency_contacts_id" v-model = "sitestaff_emergency_contacts_id" > -->
          <div class="col-sm-3 pad-5">First Name</div>
          <div class="col-sm-9 pad-5"><input type = "text" class = "form-control input-sm" name = "ecFName" v-model = "ecFName"></div>
          <div class="col-sm-3 pad-5">Last Name</div>
          <div class="col-sm-9 pad-5"><input type = "text" class = "form-control input-sm" name = "ecSName" v-model = "ecSName"></div>
          <div class="col-sm-3 pad-5">Relation</div>
          <div class="col-sm-9 pad-5"><input type = "text" class = "form-control input-sm" name = "ecRelation" v-model = "ecRelation"></div>
          <div class="col-sm-3 pad-5">Contact Numbers</div>
          <div class="col-sm-9 pad-5"><input type = "text" class = "form-control input-sm" name = "ecContacts" v-model = "ecContacts" v-on:keyup="formatPhoneNumber"></div>
        </div>
        <div class="modal-footer">
          <input type="button" class="btn btn-default btn-success" v-on:click = "updateEmergencyContacts" name = "update" value = "Update" data-dismiss="modal">
          <input type="button" class="btn btn-default btn-danger pull-left" v-on:click = "deleteEmergencyContacts" name = "remove" value = "Remove" data-dismiss="modal">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        <!-- </form> -->
      </div>
        
    </div>
  </div>

  <div class="modal fade" id="add_license_cert" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">License and Certificates</h4>
          </div>
        
          <div class="modal-body row">
            <div class="col-sm-4 pad-5">Select*: </div>
            <div class="col-sm-8 pad-5">
                <SELECT class = "form-control input-sm" name = "LCtype" name = "LCtype" v-model = "LCtype">
                    <option value="1">Licence</option>
                    <option value="0">Certificates </option>
                </SELECT>
            </div>
            <div class="clearfix"></div>
              <div class="col-sm-4 pad-5">Type*:</div>
              <div class="col-sm-8 pad-5">
                <SELECT class = "form-control input-sm" name = "LCName" name = "LCName" v-model = "LCName" v-on:change = "typeChange">
                    <option v-for = "licenseCertTypes in licenseCertTypes" :value="licenseCertTypes.lc_type_name">{{ licenseCertTypes.lc_type_name }}</option>
                    <!-- <option value = "0">Other</option> -->
                </SELECT>
              </div>
          
              <div class="clearfix" v-if = "showLCTypeName"></div>
              <div class="col-sm-4 pad-5" v-if = "showLCTypeName">Enter Type Name*:</div>
              <div class="col-sm-8 pad-5" v-if = "showLCTypeName">
                <input type = "text" class = "form-control input-sm" v-model = "LCTypeName">
              </div>

              <div class="clearfix" v-if = "LCName == 'COVID-19 Digital Certificate'"></div>
              <div class="col-sm-4 pad-5" v-if = "LCName == 'COVID-19 Digital Certificate'">1st Dose Date*:</div>
              <div class="col-sm-8 pad-5" v-if = "LCName == 'COVID-19 Digital Certificate'">
                <input type = "date" class = "form-control input-sm" v-model = "first_dose">
              </div>

              <div class="clearfix" v-if = "LCName == 'COVID-19 Digital Certificate'"></div>
              <div class="col-sm-4 pad-5" v-if = "LCName == 'COVID-19 Digital Certificate'">2nd Dose Date*:</div>
              <div class="col-sm-8 pad-5" v-if = "LCName == 'COVID-19 Digital Certificate'">
                <input type = "date" class = "form-control input-sm" v-model = "second_dose">
              </div>

              <div class="clearfix"></div>
              <div class="col-sm-4 pad-5">Licences/Certificate Number*: </div>
              <div class="col-sm-8 pad-5"><input type = "text" class = "form-control input-sm" name = "lcNumber" v-model = "lcNumber"></div>
              <div class="clearfix"></div>
            <div class="col-sm-4 pad-5">Has Expiration:</div>
            <div class="col-sm-8 pad-5">
              <select class = "form-control input-sm" name = "has_espiration" v-model = "has_expiration" v-on:change="changeHasExpiration">
            <option value="1">Yes</option>
            <option value="0">No</option>
          </select>
            </div>
              <div class="clearfix"></div>
            <div class="col-sm-4 pad-5" v-show="showExpirationDate">Expiration Date (if applicable)</div>
            <div class="col-sm-8 pad-5" v-show="showExpirationDate"><input type = "date" class = "form-control input-sm" name = "expirationDate" v-model = "expirationDate"></div>

          </div>
          <div class="modal-footer">
            <button type="button" class = "btn btn-success" v-show = "show_add_button" v-on:click="addContSiteStaffLC">Add</button>
              <button type="button" class="btn btn-default btn-danger pull-left" v-on:click= "removeContSiteStaffLC" data-dismiss="modal" v-show = "!show_add_button">Remove</button>
              <button type="button" class="btn btn-default btn-success" v-on:click= "udpateContSiteStaffLC" data-dismiss="modal" v-show = "!show_add_button">Update</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      
    </div>
  </div>


  <!-- <div class="modal fade" id="add_license_cert" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Licences / Certificates</h4>
        </div>
        
        <div class="modal-body row">
                    
          <div class="col-sm-4 pad-5">Select*: </div>
          <div class="col-sm-8 pad-5">
            <SELECT class = "form-control input-sm" name = "LCtype" name = "LCtype" v-model = "LCtype">
              <option value="1">Licence</option>
              <option value="0">Certificates </option>
            </SELECT>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5">Type*:</div>
          <div class="col-sm-8 pad-5">
            <SELECT class = "form-control input-sm" name = "LCName" name = "LCName" v-model = "LCName" v-on:change = "typeChange">
              <option v-for = "licenseCertTypes in licenseCertTypes" :value="licenseCertTypes.lc_type_name">{{ licenseCertTypes.lc_type_name }}</option>
              <option value = "0">Other</option>
            </SELECT>
          </div>
          
          <div class="clearfix" v-if = "showLCTypeName"></div>
          <div class="col-sm-4 pad-5" v-if = "showLCTypeName">Enter Type Name*:</div>
          <div class="col-sm-8 pad-5" v-if = "showLCTypeName">
            <input type = "text" class = "form-control input-sm" v-model = "LCTypeName">
          </div>

          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5">Licences/Certificate Number*: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control input-sm" name = "lcNumber" v-model = "lcNumber"></div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad_5">Has Expiration:</div>
          <div class="col-sm-8 pad_5">
            <select class = "form-control input-sm" name = "has_espiration" v-model = "has_expiration" v-on:change="changeHasExpiration">
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5" v-show="showExpirationDate">Expiration Date (if applicable)</div>
          <div class="col-sm-8 pad-5" v-show="showExpirationDate"><input type = "date" class = "form-control input-sm" name = "expirationDate" v-model = "expirationDate"></div>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-success" v-on:click= "addLC" data-dismiss="modal">Add</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
        
    </div>
  </div> -->


  <div class="modal fade" id="update_license_cert" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Licences / Certificates</h4>
        </div>
        
        <div class="modal-body row">
                    
          <div class="col-sm-4 pad-5">Select*: </div>
          <div class="col-sm-8 pad-5">
            <SELECT class = "form-control input-sm" name = "LCtype" name = "LCtype" v-model = "LCtype">
              <option value="1">Licence</option>
              <option value="0">Certificates </option>
            </SELECT>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5">Type*:</div>
          <div class="col-sm-8 pad-5">
            <SELECT class = "form-control input-sm" name = "LCName" name = "LCName" v-model = "LCName">
              <option v-for = "licenseCertTypes in licenseCertTypes" :value="licenseCertTypes.lc_type_name">{{ licenseCertTypes.lc_type_name }}</option>
              <option value = "0">Other</option>
            </SELECT>
          </div>
          <div class="clearfix" v-if = "showLCTypeName"></div>
          <div class="col-sm-4 pad-5" v-if = "showLCTypeName">Type*:</div>
          <div class="col-sm-8 pad-5" v-if = "showLCTypeName">
            <input type = "text" class = "form-control input-sm" v-model = "LCTypeName">
          </div>
          
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5">Licences/Certificate Number*: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control input-sm" name = "lcNumber"  v-model = "lcNumber"></div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad_5">Has Expiration:</div>
          <div class="col-sm-8 pad_5">
            <select class = "form-control input-sm" name = "has_espiration" v-model = "has_expiration" v-on:change="changeHasExpiration">
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5" v-show="showExpirationDate">Expiration Date (if applicable)</div>
          <div class="col-sm-8 pad-5" v-show="showExpirationDate"><input type = "date" class = "form-control input-sm" name = "expirationDate" v-model = "expirationDate"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-success" v-on:click= "updateLC" data-dismiss="modal">Update</button>
          <button type="button" class="btn btn-default btn-danger pull-left" v-on:click= "removeLC" data-dismiss="modal">Remove</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
        
    </div>
  </div>

  <div class="modal fade" id="add_training_cert" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Training Records</h4>
        </div>
        
        <div class="modal-body row">
                    
          <div class="col-sm-4 pad-5">Training: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control input-sm" name = "trainingName" v-model = "trainingName"></div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5">Training Date:</div>
          <div class="col-sm-8 pad-5"><input type="date" class = "form-control input-sm" name = "trainingDate" v-model = "trainingDate"></div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5">Training location: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control input-sm" name = "trainingLoc"  v-model = "trainingLoc"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-success" v-on:click= "addTraining" data-dismiss="modal">Add</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
        
    </div>
  </div>

  <div class="modal fade" id="update_training_cert" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Training Records</h4>
        </div>
        
        <div class="modal-body row">
                    
          <div class="col-sm-4 pad-5">Training: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control input-sm" name = "trainingName" v-model = "trainingName"></div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5">Training Date:</div>
          <div class="col-sm-8 pad-5"><input type="date" class = "form-control input-sm" name = "trainingDate" v-model = "trainingDate"></div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5">Training location: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control input-sm" name = "trainingLoc"  v-model = "trainingLoc"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-success" v-on:click= "updateTraining" data-dismiss="modal">Update</button>
          <button type="button" class="btn btn-default btn-danger pull-left" v-on:click= "removeTraining" data-dismiss="modal">Remove</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
        
    </div>
  </div>

  <div class="modal fade" id="add_cont_site_staff" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Contractor Site Staff</h4>
        </div>
        
        <div class="modal-body row">
                    
          <div class="col-sm-4 pad-5">Contractor Company Name: </div>
          <div class="col-sm-8 pad-5">
            <select class = "form-control chosen" id = "contractorName" >
              <option v-for = "contractors_list in contractors_list" :value = "contractors_list.company_id">{{ contractors_list.company_name }}</option>
            </select>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5">First Name:</div>
          <div class="col-sm-8 pad-5"><input type="text" class = "form-control" name = "siteStaffFName" v-model = "siteStaffFName"></div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5">Last Name: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control" name = "siteStaffLName"  v-model = "siteStaffLName"></div>
          <div class="col-sm-4 pad-5">Position</div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control" v-model = "siteStaffPosition"></div>
          <div class="col-sm-4 pad-5">Mobile Number: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control" name = "siteStaffMobile"  v-model = "siteStaffMobile" v-on:keyup="formatMobileNumber"></div>
          <div class="col-sm-4 pad-5">E-mail: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control" name = "siteStaffEmail"  v-model = "siteStaffEmail"></div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-success" v-on:click= "addContractorSiteStaff" data-dismiss="modal">Add</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
        
    </div>
  </div>

  <div class="modal fade" id="update_cont_site_staff" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Contractor Site Staff</h4>
        </div>
        
        <div class="modal-body row">
                    
          <div class="col-sm-4 pad-5">Contractor Company Name: </div>
          <div class="col-sm-8 pad-5">
            <select class = "form-control chosen" id = "updatecontractorName" >
              <option v-for = "contractors_list in contractors_list" :value = "contractors_list.company_id">{{ contractors_list.company_name }}</option>
            </select>
          </div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5">First Name:</div>
          <div class="col-sm-8 pad-5"><input type="text" class = "form-control" name = "siteStaffFName" v-model = "siteStaffFName"></div>
          <div class="clearfix"></div>
          <div class="col-sm-4 pad-5">Last Name: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control" name = "siteStaffLName"  v-model = "siteStaffLName"></div>
          <div class="col-sm-4 pad-5">Position</div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control" v-model = "siteStaffPosition"></div>
          <div class="col-sm-4 pad-5">Mobile Number: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control" name = "siteStaffMobile"  v-model = "siteStaffMobile" v-on:keyup="formatMobileNumber"></div>
          <div class="col-sm-4 pad-5">E-mail: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control" name = "siteStaffEmail"  v-model = "siteStaffEmail"></div>
          <div class="col-sm-4 pad-5">General Induction Date: </div>
          <div class="col-sm-8 pad-5"><input type = "date" class = "form-control" name = "gi_date" id = "gi_date"></div>
          <div class="col-sm-4 pad-5">Username: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control" name = "sh_username" id = "sh_username" disabled></div>
          <div class="col-sm-4 pad-5">Password: </div>
          <div class="col-sm-8 pad-5"><input type = "text" class = "form-control" name = "sh_password" id = "sh_password" disabled></div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-danger pull-left" v-on:click= "removeContractorSiteStaff" data-dismiss="modal">Remove</button>
          <button type="button" class="btn btn-default btn-success" v-on:click= "updateContractorSiteStaff" data-dismiss="modal">Update</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
        
    </div>
  </div>

  <div class="modal fade" id="send_site_staff_link" role="dialog" >
    <div class="modal-dialog" style = "width: 1000px">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Send Site Staff Link</h4>
        </div>
        
        <div class="modal-body row">
          <div class="col-sm-4">
            <div class="col-sm-12 pad-5"><input type="text" class = "form-control input-sm" v-model = "searchContractor" placeholder = "search contractor..."></div>
            <div class="col-sm-12 pad_5" style = "height: 400px; overflow: auto">
              <table class = "table  table-condensed table-bordered m-bottom-0 table-striped fancyTable">
                <tr>
                  <th>Contractors List</th>
                </tr>
                <tr v-for = "contractors_list in filterContractor" >
                  <td>
                    <input type = "checkbox" :value = "contractors_list.company_id" v-model = 'chk_sel_cont_sending' >{{ contractors_list.company_name }}
                    <span class = "pull-right" style = "color: red" v-if="contractors_list.induction_email_stat == '1'" :title = "contractors_list.induction_date_sent | ausdate">Email Sent</span>
                  </td>
                </tr>
              </table>
            </div>
              
          </div>         
          <div class="col-sm-8">
            <div class="col-sm-2 pad-5">CC:</div>
            <dvi class="col-sm-10 pad-5"><input type = 'text' class= "form-control input-sm" id = "cc"></dvi>
            <div class="col-sm-2 pad-5">BCC:</div>
            <dvi class="col-sm-10 pad-5"><input type = 'text' class= "form-control input-sm" id = "bcc"></dvi>
            <div class="col-sm-2 pad-5">Subject:</div>
            <dvi class="col-sm-10 pad-5"><input type = 'text' class= "form-control input-sm" id = "subject"></dvi>
            <div class="col-sm-12 pad-5">Message:</div>
            <div class="cols-sm-12 pad-5">
              <textarea class = "form-control input-sm" style = "height: 200px" id = "message"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-success" id = "send_button" v-on:click= "sendEmails">Send</button>
          <button class="btn btn-xs btn-warning sending_button"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Sending...</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
        
    </div>
  </div>

  <div class="modal fade" id="site_staff_covid_cert" role="dialog" >
    <div class="modal-dialog" style = "width: 1000px">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Site Staff Uploaded Certificates</h4>
        </div>
        
        <div class="modal-body row">

          <div class="col-sm-4">
            <div class="col-sm-12 pad-5">
              <v-select v-model = "project_id" :options="options" @input="select_project"></v-select>
            </div>
            <div class="col-sm-12 pad-5"><input type="text" class = "form-control input-sm" v-model = "searchContractorCovid" placeholder = "search contractor..."></div>
            <div class="col-sm-12 pad_5" style = "height: 400px; overflow: auto">
              <table class = "table  table-condensed table-bordered m-bottom-0 table-striped fancyTable">
                <tr>
                  <th>Contractors List</th>
                </tr>
                <tr v-for = "project_contractors in project_contractors" v-on:click = "select_contractor_company(project_contractors.company_id)">
                  <td>
                    {{ project_contractors.works_id +"-"+ project_contractors.company_name }}
                  </td>
                </tr>
              </table>
            </div>
              
          </div>         
          <div class="col-sm-8">
            <div class="col-sm-12">
              <div style = "height: 20px; width: 20px; background: blue !important; border: 1px solid #888; position: absolute"  ></div> 
              <span style = "padding: 20px">Done with Site Specific Induction</span>
              <button type = "button" class = "btn btn-warning btn-sm pull-right" v-on:click = "printSiteStaffwihCovidCert">Print</button>
            </div>
            <div class="col-sm-12 pad-5" style ="height: 400px; overflow: auto">
              <table class = "table  table-condensed table-bordered m-bottom-0 table-striped fancyTable">
                <tr>
                  <th>Site Staff Name</th>
                  <th>Mobile</th>
                  <th>1st Dose Date</th>
                  <th>2nd Dose Date</th>
                  <th>File</th>
                </tr>
                <tr v-for = "ss_covid_cert in ss_covid_cert">
                  <td :class = "checkwatched(ss_covid_cert.date_watched)">
                    {{ ss_covid_cert.site_staff_fname +" "+ ss_covid_cert.site_staff_sname}}
                  </td>
                  <td :class = "checkwatched(ss_covid_cert.date_watched)">{{ ss_covid_cert.mobile_number }}</td>
                  <td :class = "checkwatched(ss_covid_cert.date_watched)" v-if = "ss_covid_cert.first_dose_date !== null">{{ ss_covid_cert.first_dose_date | ausdate}}</td>
                  <td :class = "checkwatched(ss_covid_cert.date_watched)" v-if = "ss_covid_cert.first_dose_date == null">--</td>
                  <td :class = "checkwatched(ss_covid_cert.date_watched)" v-if = "ss_covid_cert.sec_dose_date !== null">{{ ss_covid_cert.sec_dose_date | ausdate }}</td>
                  <td :class = "checkwatched(ss_covid_cert.date_watched)" v-if = "ss_covid_cert.sec_dose_date == null">--</td>
                  <td v-if = "ss_covid_cert.sec_dose_date !== null"><button type = "button" class = "btn btn-success btn-xs" v-on:click = "view_certificate(ss_covid_cert.image_path,ss_covid_cert.company_id)">View File</button></td>
                  <td :class = "checkwatched(ss_covid_cert.date_watched)" v-if = "ss_covid_cert.sec_dose_date == null">No File Uploaded</td
                </tr>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
        
    </div>
  </div>

  <div class="modal fade" id="mdl_covid_file_preview" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
          
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">COVID 19 Digital Certificate Preview</h4>
          </div>
          <div class="modal-body row">
            <iframe id = "iframe" style = "height: 400px;" class = "col-sm-12"></iframe>
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      
    </div>
  </div>

  <div class="modal fade" id="mdl_upload_covid_file" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
          <form id = "target" action="<?php echo base_url(); ?>induction_health_safety/do_upload_covid_file" method="post" enctype="multipart/form-data">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Upload COVID 19 Digital Certificate</h4>
          </div>
          <div class="modal-body row">
            <input type="hidden" name="ci" :value = "company_id">
            <input type="hidden" name="site_staff_id" id = "site_staff_id" v-model = "site_staff_id">
            <input type="hidden" name="user_type" id = "user_type" v-model = "user_type">


            <div class="col-ms-12 pad_5"><input type="file" name="userfile[]" multiple="multiple" accept="application/pdf"></div>
          </div>
          <div class="modal-footer">
            <button type = "button" class = "btn btn-success form-control" v-on:click = "clk_upload_covid_file">Upload</button>
          </div>
          </form>
        </div>
      
    </div>
  </div>
</div>
   
       

<?php echo view('assets/logout-modal'); ?>  
<?php //$this->load->view('assets/logout-modal'); ?>


<?php $this->bulletin_board->list_latest_post(); ?>

<script>


  jQuery(document).ready(function () {
        'use strict';

        jQuery('#expirationDate').datetimepicker({format: 'd/m/Y'});

        $("#send_button").show();
        $(".sending_button").hide();

    $("#fixTable").tableHeadFixer({"left" : 3});
    $("#fixTable1").tableHeadFixer({"left" : 3});
    $("#fixTable2").tableHeadFixer({"left" : 3});
    
  });

  var baseurl = $("#base_url").val(); 
  var proj_post_code = $("#proj_post_code").val();

  var sender_name = "";
  var sender_email = "";
  var assigned_user_id = "";
  var rowCompName = "";
  var rowCompName2 = "";

  var rowCompNameSubmitted = "";

  Vue.component('v-select', VueSelect.VueSelect);

  var app = new Vue({
      el: '#ihs_app',
      data: {
        licenseCertTypes: [],
        errors:[],
        site_staff: [],
        emergency_contatacts: [],
        license_certificates: [],
        training_records: [],
        user_id: null,
        ecFName: null,
        ecSName: null,
        ecRelation: null,
        ecContacts: null,
        sitestaff_emergency_contacts_id: null,

        lcerrors:[],
        LCtype: null,
        LCName: null,
        lcNumber: null,
        expirationDate: null,
        user_license_certificates_id: null,

        trainingName: null,
        trainingDate: null,
        trainingLoc: null,
        training_records_id: null,

        contractor_site_staff: [],
        LCTypeName: null,
        showLCTypeName: false,

        searchSiteStaff: '',
        searchContractorSiteStaff: '',
        searchContractorSiteStaffSubmitted: '',
        filterContSiteStaff: '',
        contractorName: null,
        siteStaffFName: null,
        siteStaffLName: null,
        siteStaffPosition: null,
        siteStaffMobile: null,
        siteStaffEmail: null,

        contractors_list: [],
        contractors_with_site_staff: [],
        contractor_site_staff_id: null,

        is_contractors: null,
        cont_sitestaff_emergency_contatacts: [],
        cont_sitestaff_license_certificates:[],
        cont_sitestaff_training_records: [],

        contractors_submitted: [],
        uploaded_site_staff: [],
        temp_cont_lc: [],
        temp_training_records: [],
        TempContSiteStaff: "All",
        UnsubmittedTempContSiteStaff: "All",

        chk_sel_cont_sending: [],
        searchContractor: '',
        cc: '',
        bcc: '',
        subject: '',
        message: '',
        sender_name: '',
        sender_email: '',
        assigned_user_id: 0,

        temp_contractors_list: [],
        chkCompID: [],
        chkcontSiteStaffID: [],

        css_contractors_list: [],

        showExpirationDate: true,
        has_expiration: 1,
        is_apprentice: 0,
        dateUpdated: "<?php echo date('Y-m-d'); ?>",
        company_id: 0,

        cert_selected_comp_id: 0,
        searchContractorCovid: "",
        ss_covid_cert: [],
        project_contractors: [],
        backup_css_list: [],
        backup_css_temp_list: [],

        first_dose: "",
        second_dose: "",
        site_staff_id: 0,
        show_add_button: true,
        user_type: 0,
        project_list: [],
        options: [],
        project_id: 0,
        contractors_list_backup: [],
      },

      filters: {
          getDayname: function(date){
            return moment(date).format('ddd');
          },
          ausdate: function(date) {
            if(date == '0000-00-00'){
              return '';
            }else{
              return moment(date).format('DD/MM/YYYY');
            }
            
          },
          getTime: function(date){
            return moment(date).format('h:m a');
          },
          replaceApos: function(string){
            return
          }

      },

      mounted: function(){
        this.fetch_project_per_state();
        this.fetchFocusSiteStaff();
        this.fetchUserEmergencyContacts();
        this.fetchUserLicencesCertificates();
        this.fetchTrainingRecords();
       
        this.fetchContractosSiteStaff();
        this.fetchLicenseCertType();
        this.fetchContractorsList();
        //this.fetchContractorsWithSiteStaff();

        this.fetchContSiteStaffEmergencyContacts();
        this.fetchContSiteStaffUserLicencesCertificates();
        this.fetchContSiteStaffTrainingRecords();

        this.fetchContractorsSubmitted();
        this.fetchTempSiteStaff();
        this.fetchTempLC();
        this.fetchTempTrainingRecords();

        // this.fetchTempContractorsList();
        //this.fetch_site_staff_covid_cert();
        //this.load_comp_with_cert();
        this.fetchProjectLis();
        //this.fetch_project_contractors();
      },

      methods: {
        checkwatched: function(date_watched){
          if(date_watched !== null){
            return "induction_watched";
          }else{
            return "";
          }

        },

        fetchProjectLis: function(){
          axios.post(baseurl+"induction_health_safety/project_list_for_covid", 
          {
          }).then(response => {
            this.project_list = response.data;    
            for (var key in this.project_list) {
              this.options.push({'value': this.project_list[key].project_id, 'label': this.project_list[key].project_id+"-"+this.project_list[key].project_name }); 
            }      
          }).catch(error => {
            console.log(error.response)
          });
        },

        fetch_project_contractors: function(){
          axios.post(baseurl+"induction_health_safety/selected_project_contractors", 
          {
            'project_id': this.project_id.value
          }).then(response => {
            this.project_contractors = response.data;      
            this.contractors_list_backup = response.data; 
          }).catch(error => {
            console.log(error.response)
          });
        },

        select_project: function(){
          this.fetch_project_contractors();

          // this.project_contractors = this.contractors_list_backup.filter((data) => {
          //   return data.project_id == this.project_id.value;
          // });
          // this.project_contractors = this.contractors_list_backup.filter(function (data) {
          //     return data.project_id == this.project_id.value;
          // });

        },

        checkRowCompName: function(CompName){
          if(rowCompName !== CompName){
            rowCompName = CompName;
            return true;
          }else{
            return false;
          }
           
        },

        checkRowCompName2: function(CompName){
          if(rowCompName2 !== CompName){
            rowCompName2 = CompName;
            return true;
          }else{
            return false;
          }
           
        },

        checkRowCompNameSubmitted: function(CompName){
          if(rowCompNameSubmitted !== CompName){
            rowCompNameSubmitted = CompName;
            return true;
          }else{
            return false;
          }
           
        },

        // load_comp_with_cert: function(){
        //   $.post(baseurl+"induction_health_safety/fetch_company_with_covid_cert",
        //   {},
        //   function(result){
        //     app.comp_with_cert = JSON.parse(result);
        //   });
        // },

        select_contractor_company: function(company_id){
          this.cert_selected_comp_id = company_id;
          this.fetch_site_staff_covid_cert();

        },

        send_update_link: function(company_id){
          axios.post(baseurl+"induction_health_safety/send_cont_site_staff_update", 
          {
            'company_id': company_id
          }).then(response => {
            alert(response.data);              
          }).catch(error => {
            console.log(error.response)
          });
        },

        showUpdateDate: function(company_id,dateUpdated){
          this.company_id = company_id;
          this.dateUpdated = dateUpdated;
          $("#update_date_updated").modal("show");
        },

        clkUpdateDateUpdated: function(){
          axios.post(baseurl+"induction_health_safety/update_induction_date_updated", 
          {
            'date_updated': this.dateUpdated,
            'company_id': this.company_id
          }).then(response => {
            //this.fetchContractorsWithSiteStaff();               
          }).catch(error => {
            console.log(error.response)
          }); 
        },

        showEditInductionSlide: function(){
          window.open(baseurl+'induction_health_safety/induction_slide_editor_view', '_self');
        },

        changeHasExpiration: function(){
          if(this.has_expiration == 1){
            this.showExpirationDate = true;
          }else{
            this.showExpirationDate = false;
            this.expirationDate = "";
          }
          
        },

        isExpired: function(expiration_date){
          return moment().isAfter(expiration_date);
        },

        typeChange: function(){
          if(this.LCName == '0'){
            this.showLCTypeName = true;
          }else{
            this.showLCTypeName = false;
          }

          if(this.LCName == 'COVID-19 Digital Certificate'){
            this.has_expiration = 0;
            this.showExpirationDate = false;
          }
          
        },


        formatPhoneNumber: function() {
          //alert(this.ecContacts.length);
          if(this.ecContacts.length == 2){
            this.ecContacts = this.ecContacts + " ";
          }
          if(this.ecContacts.length == 7){
            this.ecContacts = this.ecContacts + " "; 
          }
        },

        formatMobileNumber: function(){
          if(this.siteStaffMobile.length == 4){
            this.siteStaffMobile = this.siteStaffMobile + " ";
          }

          if(this.siteStaffMobile.length == 8){
            this.siteStaffMobile = this.siteStaffMobile + " ";
          }
        },

        checkAddLCForm:function(){

          if(this.LCtype && this.LCName && this.lcNumber){
            return true;
          }else{
            this.lcerrors = [];
            this.lcerrors.push("Items with * required.");
             e.preventDefault();
          }
          
         
        }, 

        checkAddECForm:function(e) {
          if(this.ecFName && this.ecSName && this.ecRelation && this.ecContacts) return true;
          this.errors = [];
          if(!this.ecFName) this.errors.push("First Name required.");
          if(!this.ecSName) this.errors.push("Last Name required.");
          if(!this.ecRelation) this.errors.push("Relation required.");
          if(!this.ecContacts) this.errors.push("Contact Numbers required.");
          e.preventDefault();
        },

        fetchTempContractorsList: function(){
          // $.post(baseurl+"induction_health_safety/fetch_temp_contractors",
          // {},
          // function(result){
          //   app.temp_contractors_list = JSON.parse(result);
          // });
          
        },

        fetchContractorsList: function(){
          axios.post(baseurl+"induction_health_safety/fetch_contractors", 
          {
          }).then(response => {
            this.contractors_list = response.data               
          }).catch(error => {
            console.log(error.response)
          }); 

          // $.post(baseurl+"induction_health_safety/fetch_contractors",
          // {},
          // function(result){
          //   app.contractors_list = JSON.parse(result);
          // });
          
        },

        // fetchContractorsWithSiteStaff: function(){
        //   $.post(baseurl+"induction_health_safety/fetch_contractors_with_sitestaff",
        //   {
        //     company_id: this.filterContSiteStaff,
        //   },
        //   function(result){
        //     app.contractors_with_site_staff = JSON.parse(result);
        //     app.css_contractors_list = JSON.parse(result);
        //   });
        // },

        fetchFocusSiteStaff: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/view_focus_site_staff", 
          {
          }).then(response => {
            this.site_staff = response.data               
          }).catch(error => {
            console.log(error.response)
          }); 
          // $.post(baseurl+"induction_health_safety/view_focus_site_staff",
          // {},
          // function(result){
          //   app.site_staff = JSON.parse(result);

          // }); 
        },

        fetchUserEmergencyContacts: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/fetch_user_emergency_contacts", 
          {
          }).then(response => {
            this.emergency_contatacts = response.data               
          }).catch(error => {
            console.log(error.response)
          }); 

          // $.post(baseurl+"induction_health_safety/fetch_user_emergency_contacts",
          // {},
          // function(result){
          //   app.emergency_contatacts = JSON.parse(result);
          // }); 
        },

        fetchContSiteStaffEmergencyContacts: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/fetch_cont_sitestaff_emergency_contacts", 
          {
          }).then(response => {
            this.cont_sitestaff_emergency_contatacts = response.data               
          }).catch(error => {
            console.log(error.response)
          }); 

          // $.post(baseurl+"induction_health_safety/fetch_cont_sitestaff_emergency_contacts",
          // {},
          // function(result){
          //   app.cont_sitestaff_emergency_contatacts = JSON.parse(result);
          // }); 
        },

        fetchUserLicencesCertificates: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/fetch_user_licences_certificates", 
          {
          }).then(response => {
            this.license_certificates = response.data               
          }).catch(error => {
            console.log(error.response)
          }); 

          // $.post(baseurl+"induction_health_safety/fetch_user_licences_certificates",
          // {},
          // function(result){
          //   app.license_certificates = JSON.parse(result);
          // }); 
        },

        fetchContSiteStaffUserLicencesCertificates: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/fetch_cont_sitestaff_licences_certificates", 
          {
          }).then(response => {
            this.cont_sitestaff_license_certificates = response.data               
          }).catch(error => {
            console.log(error.response)
          }); 

          // $.post(baseurl+"induction_health_safety/fetch_cont_sitestaff_licences_certificates",
          // {},
          // function(result){
          //   app.cont_sitestaff_license_certificates = JSON.parse(result);
          // }); 
        },

        fetchTrainingRecords: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/fetch_user_training_records", 
          {
          }).then(response => {
            this.training_records = response.data               
          }).catch(error => {
            console.log(error.response)
          });

          // $.post(baseurl+"induction_health_safety/fetch_user_training_records",
          // {},
          // function(result){
          //   app.training_records = JSON.parse(result);
          // }); 
        },

        fetchContSiteStaffTrainingRecords: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/fetch_cont_sitestaff_training_records", 
          {
          }).then(response => {
            this.cont_sitestaff_training_records = response.data               
          }).catch(error => {
            console.log(error.response)
          });

          // $.post(baseurl+"induction_health_safety/fetch_cont_sitestaff_training_records",
          // {},
          // function(result){
          //   app.cont_sitestaff_training_records = JSON.parse(result);
          // }); 
        },

        

        fetchLicenseCertType: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/fetch_license_cert_type", 
          {
          }).then(response => {
            this.licenseCertTypes = response.data               
          }).catch(error => {
            console.log(error.response)
          });
          
          // $.post(baseurl+"induction_health_safety/fetch_license_cert_type",
          // {},
          // function(result){
          //   app.licenseCertTypes = JSON.parse(result);
          // });
        },

        showAddEmergenceContacts: function(user_id){
          this.is_contractors = 0;
          this.user_id = user_id;
          this.ecFName = null;
          this.ecSName = null;
          this.ecRelation = null;
          this.ecContacts = null;
          $("#add_emergency_contact").modal('show');
        },

        showAddCSSEmergenceContacts: function(user_id){
          this.is_contractors = 1;
          this.user_id = user_id;
          this.ecFName = null;
          this.ecSName = null;
          this.ecRelation = null;
          this.ecContacts = null;
          $("#add_emergency_contact").modal('show');
        },


        showUpdateEmergencyContacts: function(sitestaff_emergency_contacts_id){
          this.sitestaff_emergency_contacts_id = sitestaff_emergency_contacts_id;
          for (var key in app.emergency_contatacts) {
              if(app.emergency_contatacts[key].sitestaff_emergency_contacts_id == sitestaff_emergency_contacts_id){
                this.user_id = app.emergency_contatacts[key].user_id;
                this.ecFName = app.emergency_contatacts[key].contact_fname;
                this.ecSName = app.emergency_contatacts[key].contact_sname;
                this.ecRelation = app.emergency_contatacts[key].relation;
                this.ecContacts = app.emergency_contatacts[key].contacts;
                this.is_contractors = 0;
              }
          }
          $("#update_emergency_contact").modal('show');
          return false;
        },

        showUpdateCSSEmergencyContacts: function(sitestaff_emergency_contacts_id){
          this.sitestaff_emergency_contacts_id = sitestaff_emergency_contacts_id;
          for (var key in app.cont_sitestaff_emergency_contatacts) {
              if(app.cont_sitestaff_emergency_contatacts[key].sitestaff_emergency_contacts_id == sitestaff_emergency_contacts_id){
                this.user_id = app.cont_sitestaff_emergency_contatacts[key].user_id;
                this.ecFName = app.cont_sitestaff_emergency_contatacts[key].contact_fname;
                this.ecSName = app.cont_sitestaff_emergency_contatacts[key].contact_sname;
                this.ecRelation = app.cont_sitestaff_emergency_contatacts[key].relation;
                this.ecContacts = app.cont_sitestaff_emergency_contatacts[key].contacts;
                this.is_contractors = 1;
              }
          }
          $("#update_emergency_contact").modal('show');
          return false;
        },

        showAddLicencesCert: function(user_id){
          this.user_type = 0;
          this.company_id = 0;
          this.site_staff_id = user_id;
          this.user_id = user_id;
          this.is_contractors = 0;
          this.LCtype = null;
          this.LCName = null;
          this.LCTypeName = null;
          this.lcNumber = null;
          this.expirationDate = null;
          $("#add_license_cert").modal("show");
        },

        showAddCSSLicencesCert: function(user_id,company_id){
          this.user_type = 1;
          this.company_id = company_id;
          this.user_id = user_id;
          this.site_staff_id = user_id;
          this.is_contractors = 1;
          this.LCtype = null;
          this.LCName = null;
          this.LCTypeName = null;
          this.lcNumber = null;
          this.expirationDate = null;
          this.is_contractors = 1;
          this.showLCTypeName = false;
          this.first_dose = "";
          this.second_dose = "";
          $("#add_license_cert").modal("show");
        },

        addContSiteStaffLC: function(){
          if(this.LCName == 'COVID-19 Digital Certificate'){
            $("#mdl_upload_covid_file").modal("show");
          }else{
            if(this.showLCTypeName == true){
              this.LCName = this.LCTypeName;
              $.post("<?php echo base_url() ?>/induction_health_safety/insert_lc_type",
              {
                lctypename: this.LCTypeName
              },
              function(result){
                app.licenseCertTypes = JSON.parse(result);
                this.showLCTypeName = false;
              }); 
            }
        
            var is_contractors = this.is_contractors;
            $.post("<?php echo base_url() ?>/induction_health_safety/add_licence_cert",
            {
              is_contractors: this.is_contractors,
              user_id: this.user_id,
              LCtype: this.LCtype,
              LCName: this.LCName,
              lcNumber: this.lcNumber,
              has_expiration: this.has_expiration,
              expirationDate: this.expirationDate,
              first_dose: this.first_dose,
              second_dose: this.second_dose
            },
            function(result){
              if(is_contractors == '1'){
                app.cont_sitestaff_license_certificates = JSON.parse(result);
              }else{
                app.license_certificates = JSON.parse(result);
              }
            }); 
          }
        },

        clk_upload_covid_file: function(){
          if(this.showLCTypeName == true){
            this.LCName = this.LCTypeName;
            $.post("<?php echo base_url() ?>/induction_health_safety/insert_lc_type",
            {
              lctypename: this.LCTypeName
            },
            function(result){
              app.licenseCertTypes = JSON.parse(result);
              this.showLCTypeName = false;
            }); 
          }
      
          var is_contractors = this.is_contractors;
          $.post("<?php echo base_url() ?>/induction_health_safety/add_licence_cert",
          {
            is_contractors: this.is_contractors,
            user_id: this.user_id,
            LCtype: this.LCtype,
            LCName: this.LCName,
            lcNumber: this.lcNumber,
            has_expiration: this.has_expiration,
            expirationDate: this.expirationDate,
            first_dose: this.first_dose,
            second_dose: this.second_dose
          },
          function(result){
            if(is_contractors == '1'){
              app.cont_sitestaff_license_certificates = JSON.parse(result);
            }else{
              app.license_certificates = JSON.parse(result);
            }
            $( "#target" ).submit();
          }); 
    
        },

        removeContSiteStaffLC: function(){
          var is_contractors = this.is_contractors;
          $.post("<?php echo base_url() ?>/induction_health_safety/remove_licence_cert",
          {
            is_contractors: this.is_contractors,
            user_license_certificates_id: this.user_license_certificates_id
          },
          function(result){
            if(is_contractors == '1'){
              app.cont_sitestaff_license_certificates = JSON.parse(result);
            }else{
              app.license_certificates = JSON.parse(result);
            }
          }); 
        },

        udpateContSiteStaffLC: function(){
          var is_contractors = this.is_contractors;
          $.post("<?php echo base_url() ?>/induction_health_safety/update_licence_cert",
          {
            is_contractors: this.is_contractors,
            user_license_certificates_id: this.user_license_certificates_id,
            LCtype: this.LCtype,
            LCName: this.LCName,
            lcNumber: this.lcNumber,
            has_expiration: this.has_expiration,
            expirationDate: this.expirationDate,
            first_dose: this.first_dose,
            second_dose: this.second_dose
          },
          function(result){
            if(is_contractors == '1'){
              app.cont_sitestaff_license_certificates = JSON.parse(result);
            }else{
              app.license_certificates = JSON.parse(result);
            }
          }); 
        },




        addLC: function(){
          // if(this.showLCTypeName == true){
          //   this.LCName = this.LCTypeName;
          //   $.post(baseurl+"induction_health_safety/insert_lc_type",
          //   {
          //     lctypename: this.LCTypeName
          //   },
          //   function(result){
          //     app.licenseCertTypes = JSON.parse(result);
          //     this.showLCTypeName = false;
          //   }); 
          // }
      
          // var is_contractors = this.is_contractors;
          // $.post(baseurl+"induction_health_safety/add_licence_cert",
          // {
          //   is_contractors: this.is_contractors,
          //   user_id: this.user_id,
          //   LCtype: this.LCtype,
          //   LCName: this.LCName,
          //   lcNumber: this.lcNumber,
          //   has_expiration: this.has_expiration,
          //   expirationDate: this.expirationDate
          // },
          // function(result){
          //   if(is_contractors == '1'){
          //     app.cont_sitestaff_license_certificates = JSON.parse(result);
          //   }else{
          //     app.license_certificates = JSON.parse(result);
          //   }
          // }); 
        }, 

        showUpdateLicenceCert: function(user_license_certificates_id){
          this.showLCTypeName = false;
          this.user_license_certificates_id = user_license_certificates_id;
          this.is_contractors = 0;
          for (var key in app.license_certificates) {
              if(app.license_certificates[key].user_license_certificates_id == user_license_certificates_id){
                this.LCtype = app.license_certificates[key].is_license;
                this.LCName = app.license_certificates[key].type;
                this.lcNumber = app.license_certificates[key].number;
                this.expirationDate = app.license_certificates[key].expiration_date;
                this.has_expiration = app.license_certificates[key].has_expiration
                if(this.has_expiration == '0'){
                  this.showExpirationDate = false;
                }else{
                  this.showExpirationDate = true;
                }
                this.first_dose = app.license_certificates[key].first_dose_date;
                this.second_dose = app.license_certificates[key].sec_dose_date;
              }
          }

          this.show_add_button = false;

          $("#add_license_cert").modal("show");
        },

        showUpdateCSSLicenceCert: function(user_license_certificates_id){
          this.showLCTypeName = false;
          this.user_license_certificates_id = user_license_certificates_id;
          this.is_contractors = 1;
          for (var key in app.cont_sitestaff_license_certificates) {
              if(app.cont_sitestaff_license_certificates[key].user_license_certificates_id == user_license_certificates_id){
                this.LCtype = app.cont_sitestaff_license_certificates[key].is_license;
                this.LCName = app.cont_sitestaff_license_certificates[key].type;
                this.lcNumber = app.cont_sitestaff_license_certificates[key].number;
                this.expirationDate = app.cont_sitestaff_license_certificates[key].expiration_date;
                this.has_expiration = app.cont_sitestaff_license_certificates[key].has_expiration
                if(this.has_expiration == '0'){
                  this.showExpirationDate = false;
                }else{
                  this.showExpirationDate = true;
                }
                this.first_dose = app.cont_sitestaff_license_certificates[key].first_dose_date;
                this.second_dose = app.cont_sitestaff_license_certificates[key].sec_dose_date;
              }
          }

          this.show_add_button = false;
          $("#add_license_cert").modal("show");
        },

        updateLC: function(){
          var is_contractors = this.is_contractors;
          $.post("<?php echo base_url() ?>/induction_health_safety/update_licence_cert",
          {
            is_contractors: this.is_contractors,
            user_license_certificates_id: this.user_license_certificates_id,
            LCtype: this.LCtype,
            LCName: this.LCName,
            lcNumber: this.lcNumber,
            has_expiration: this.has_expiration,
            expirationDate: this.expirationDate
          },
          function(result){
            if(is_contractors == '1'){
              app.cont_sitestaff_license_certificates = JSON.parse(result);
            }else{
              app.license_certificates = JSON.parse(result);
            }
          }); 
        },

        removeLC: function(){
          var is_contractors = this.is_contractors;
          $.post("<?php echo base_url() ?>/induction_health_safety/remove_licence_cert",
          {
            is_contractors: this.is_contractors,
            user_license_certificates_id: this.user_license_certificates_id
          },
          function(result){
            if(is_contractors == '1'){
              app.cont_sitestaff_license_certificates = JSON.parse(result);
            }else{
              app.license_certificates = JSON.parse(result);
            }
          }); 
        },

        showAddTraining: function(user_id){
          this.is_contractors = 0;
          this.user_id = user_id;
          this.trainingName = null;
          this.trainingDate = null;
          this.trainingLoc = null;
          $("#add_training_cert").modal('show');
        },

        showAddCSSTraining: function(user_id){
          this.is_contractors = 1;
          this.user_id = user_id;
          this.trainingName = null;
          this.trainingDate = null;
          this.trainingLoc = null;
          $("#add_training_cert").modal('show');
        },
        
        addTraining: function(){
          var is_contractors = this.is_contractors;
          $.post("<?php echo base_url() ?>/induction_health_safety/add_training",
          {
            is_contractors: this.is_contractors,
            user_id: this.user_id,
            trainingName: this.trainingName,
            trainingDate: this.trainingDate,
            trainingLoc: this.trainingLoc
          },
          function(result){
            if(is_contractors == '1'){
              app.cont_sitestaff_training_records = JSON.parse(result);
            }else{
              app.training_records = JSON.parse(result);
            }
          }); 
        },

        showUpdateTraining: function(training_records_id){
          this.training_records_id = training_records_id;
          this.is_contractors = 0;
          for (var key in app.training_records) {
              if(app.training_records[key].training_records_id == training_records_id){
                this.trainingName = app.training_records[key].training_type;
                this.trainingDate = app.training_records[key].date_undertaken;
                this.trainingLoc = app.training_records[key].taken_with;
              }
          }

          $("#update_training_cert").modal('show');
        },

        showUpdateCSSTraining: function(training_records_id){
          this.training_records_id = training_records_id;
          this.is_contractors = 1;
          for (var key in app.cont_sitestaff_training_records) {
              if(app.cont_sitestaff_training_records[key].training_records_id == training_records_id){
                this.trainingName = app.cont_sitestaff_training_records[key].training_type;
                this.trainingDate = app.cont_sitestaff_training_records[key].date_undertaken;
                this.trainingLoc = app.cont_sitestaff_training_records[key].taken_with;
              }
          }

          $("#update_training_cert").modal('show');
        },
        

        updateTraining: function(){
          var is_contractors = this.is_contractors;
          $.post("<?php echo base_url() ?>/induction_health_safety/update_training",
          {
            is_contractors: this.is_contractors,
            training_records_id: this.training_records_id,
            trainingName: this.trainingName,
            trainingDate: this.trainingDate,
            trainingLoc: this.trainingLoc
          },
          function(result){
            if(is_contractors == '1'){
              app.cont_sitestaff_training_records = JSON.parse(result);
            }else{
              app.training_records = JSON.parse(result);
            }
          }); 
        },

        removeTraining: function(){
          var is_contractors = this.is_contractors;

          $.post("<?php echo base_url() ?>/induction_health_safety/remove_training",
          {
            is_contractors: this.is_contractors,
            training_records_id: this.training_records_id
          },
          function(result){
            if(is_contractors == '1'){
              app.cont_sitestaff_training_records = JSON.parse(result);
            }else{
              app.training_records = JSON.parse(result);
            }
          }); 
        },

        fetchContractosSiteStaff: function(){
          axios.post("<?php echo base_url() ?>/company/get_cont_site_staff_all", 
          {
          }).then(response => {   
            var selected_company = "";
            this.contractors_with_site_staff = [];
            this.contractor_site_staff = response.data; 
            this.backup_css_list = response.data;
            for (var key in this.contractor_site_staff) {
              if(selected_company !== this.contractor_site_staff[key].company_name && this.contractor_site_staff[key].company_name !== undefined){
                selected_company = this.contractor_site_staff[key].company_name;
                this.contractors_with_site_staff.push({ company_id: this.contractor_site_staff[key].company_id, company_name: this.contractor_site_staff[key].company_name }); 
              }
            } 

            this.contractors_with_site_staff.filter(x => x !== undefined);
                    
          }).catch(error => {
            console.log(error.response)
          });
        },

        showAddContSiteStaff: function(){
          $('#s2id_contractorName span.select2-chosen').text("");
          this.siteStaffFName = null;
          this.siteStaffLName = null;
          this.siteStaffMobile = null;
          this.contractorName = null;
          this.siteStaffPosition = null;
          $("#add_cont_site_staff").modal('show');
        },

        addContractorSiteStaff: function(){
          this.contractorName = $('#contractorName').val();

          if(this.contractorName == null || this.siteStaffFName == null || this.siteStaffLName == null || this.siteStaffMobile == null || this.siteStaffPosition == null){
            alert("All fields are required!");
          }else{
            
            axios.post(baseurl+"company/add_cont_site_staff", 
            {
              'company_id': this.contractorName,
              'ss_fname': this.siteStaffFName,
              'ss_sname': this.siteStaffLName,
              'ss_position': this.siteStaffPosition,
              'ss_mobile_no': this.siteStaffMobile,
              'ss_email': this.siteStaffEmail
            }).then(response => {   
              this.fetchContractosSiteStaff();
            }).catch(error => {
              console.log(error.response)
            }); 

            // $.post(baseurl+"company/add_cont_site_staff",
            // {
            //   company_id: this.contractorName,
            //   ss_fname: this.siteStaffFName,
            //   ss_sname: this.siteStaffLName,
            //   ss_position: this.siteStaffPosition,
            //   ss_mobile_no: this.siteStaffMobile,
            //   ss_email: this.siteStaffEmail
            // },
            // function(result){
            //   app.contractor_site_staff = JSON.parse(result);
            // });  
          }
          
          
        },

        filterByCompany: function(){

          this.contractor_site_staff = this.backup_css_list.filter(siteStaff => !siteStaff.company_id.indexOf(this.filterContSiteStaff));

          // this.filterContractorSiteStaff = this.contractor_site_staff.filter((siteStaff) => {
          //   return siteStaff.company_id.match(+(this.filterContSiteStaff);
          // });
        },

        showUpdateContractorSiteStaff: function(contractor_site_staff_id){
          this.contractor_site_staff_id = contractor_site_staff_id;
          var gi_date = "";
          for (var key in app.contractor_site_staff) {
              if(app.contractor_site_staff[key].contractor_site_staff_id == contractor_site_staff_id){
                this.siteStaffFName = app.contractor_site_staff[key].site_staff_fname;
                this.siteStaffLName = app.contractor_site_staff[key].site_staff_sname;
                this.siteStaffMobile = app.contractor_site_staff[key].ss_mobile_number;
                this.siteStaffEmail = app.contractor_site_staff[key].email;
                var contractorCompID = app.contractor_site_staff[key].company_id;
                var contractorName = app.contractor_site_staff[key].company_name;
                this.siteStaffPosition = app.contractor_site_staff[key].position;
                gi_date = app.contractor_site_staff[key].general_induction_date;
                var sh_username = app.contractor_site_staff[key].safety_hub_username;
                var sh_password = app.contractor_site_staff[key].safety_hub_password;
                this.is_apprentice = app.contractor_site_staff[key].is_apprentice;
              }
          }

          $("#sh_username").val(sh_username);
          $("#sh_password").val(sh_password);

          $("#gi_date").val(gi_date);
          $('#s2id_updatecontractorName span.select2-chosen').text(contractorName);
          $('#updatecontractorName').val(contractorCompID);
          $("#update_cont_site_staff").modal('show');

        },

        updateContractorSiteStaff: function(){
          var company_id = $('#updatecontractorName').val();
          var gi_date = $("#gi_date").val();

          axios.post("<?php echo base_url() ?>/company/update_cont_site_staff", 
          {
            'contractor_site_staff_id': this.contractor_site_staff_id,
            'company_id': company_id,
            'ss_fname': this.siteStaffFName,
            'ss_sname': this.siteStaffLName,
            'ss_position': this.siteStaffPosition,
            'ss_mobile_no': this.siteStaffMobile,
            'ss_email': this.siteStaffEmail,
            'gi_date': gi_date,
            'is_apprentice': this.is_apprentice
          }).then(response => {   
            this.fetchContractosSiteStaff();
          }).catch(error => {
            console.log(error.response)
          }); 

          // $.post(baseurl+"company/update_cont_site_staff",
          // {
          //   contractor_site_staff_id: this.contractor_site_staff_id,
          //   company_id: company_id,
          //   ss_fname: this.siteStaffFName,
          //   ss_sname: this.siteStaffLName,
          //   ss_position: this.siteStaffPosition,
          //   ss_mobile_no: this.siteStaffMobile,
          //   ss_email: this.siteStaffEmail,
          //   gi_date: gi_date,
          //   is_apprentice: this.is_apprentice
          // },
          // function(result){
          //   app.contractor_site_staff = JSON.parse(result);
          // }); 
        },

        removeContractorSiteStaff: function(){
          axios.post("<?php echo base_url() ?>/company/remove_cont_site_staff", 
          {
            'contractor_site_staff_id': this.contractor_site_staff_id
          }).then(response => {   
            this.fetchContractosSiteStaff();
          }).catch(error => {
            console.log(error.response)
          }); 

          // $.post(baseurl+"company/remove_cont_site_staff",
          // {
          //   contractor_site_staff_id: this.contractor_site_staff_id,
          // },
          // function(result){
          //   app.contractor_site_staff = JSON.parse(result);
          // }); 
        },

        addEmergencyContacts: function(){
          var is_contractors = this.is_contractors;
          $.post("<?php echo base_url() ?>/induction_health_safety/add_emergency_contact",
          {
            is_contractors: this.is_contractors,
            user_id: this.user_id,
            ecFName: this.ecFName,
            ecSName: this.ecSName,
            ecRelation: this.ecRelation,
            ecContacts: this.ecContacts
          },
          function(result){
            if(is_contractors == '1'){
              app.cont_sitestaff_emergency_contatacts = JSON.parse(result);
            }else{
              app.emergency_contatacts = JSON.parse(result);
            }
          }); 
        },

        updateEmergencyContacts: function(){
          var is_contractors = this.is_contractors;

          $.post("<?php echo base_url() ?>/induction_health_safety/update_emergency_contact",
          {
            sitestaff_emergency_contacts_id: this.sitestaff_emergency_contacts_id,
            user_id: this.user_id,
            ecFName: this.ecFName,
            ecSName: this.ecSName,
            ecRelation: this.ecRelation,
            ecContacts: this.ecContacts,
            is_contractors: this.is_contractors
          },
          function(result){
            if(is_contractors == '1'){
              app.cont_sitestaff_emergency_contatacts = JSON.parse(result);
            }else{
              app.emergency_contatacts = JSON.parse(result);
            }
          }); 
        },
        
        deleteEmergencyContacts: function(){
          var is_contractors = this.is_contractors;

          $.post("<?php echo base_url() ?>/induction_health_safety/remove_emergency_contacts",
          {
            sitestaff_emergency_contacts_id: this.sitestaff_emergency_contacts_id,
            is_contractors: this.is_contractors
          },
          function(result){
            if(is_contractors == '1'){
              app.cont_sitestaff_emergency_contatacts = JSON.parse(result);
            }else{
              app.emergency_contatacts = JSON.parse(result);
            }
          });
        },
        
        fetchContractorsSubmitted: function(){
          // $.post(baseurl+"induction_health_safety/fetch_cont_sitestaff_submitted",
          // {},
          // function(result){
          //   app.contractors_submitted = JSON.parse(result);
          // }); 
        },

        fetchTempSiteStaff: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/fetch_temp_sitestaff", 
          {
          }).then(response => {   
            this.contractors_submitted = [];
            var selected_company = "";
            this.uploaded_site_staff = response.data; 
            this.backup_css_temp_list = response.data;
            for (var key in this.uploaded_site_staff) {

              if(selected_company !== this.uploaded_site_staff[key].company_name && this.uploaded_site_staff[key].company_name !== undefined){
                selected_company = this.uploaded_site_staff[key].company_name;
                this.contractors_submitted.push({ company_id: this.uploaded_site_staff[key].company_id, company_name: this.uploaded_site_staff[key].company_name, induction_date_sent: this.uploaded_site_staff[key].induction_date_sent }); 
              }
            } 
                    
          }).catch(error => {
            console.log(error.response)
          }); 
        },

        filterSubmittedTempSiteStaff: function(){
          if(this.TempContSiteStaff == 'All'){
            this.uploaded_site_staff = this.backup_css_temp_list;
          }else{
            this.uploaded_site_staff = this.backup_css_temp_list.filter(siteStaff => !siteStaff.company_id.indexOf(this.TempContSiteStaff));
          }
          
        },

        fetchTempLC: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/fetch_temp_lc", 
          {
          }).then(response => {   
            this.temp_cont_lc = response.data;   
          }).catch(error => {
            console.log(error.response)
          }); 
          // $.post(baseurl+"induction_health_safety/fetch_temp_lc",
          // {},
          // function(result){
          //   app.temp_cont_lc = JSON.parse(result);
          // }); 
        },

        fetchTempTrainingRecords: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/fetch_temp_training", 
          {
          }).then(response => {   
            this.temp_training_records = response.data;   
          }).catch(error => {
            console.log(error.response)
          }); 

          // $.post(baseurl+"induction_health_safety/fetch_temp_training",
          // {},
          // function(result){
          //   app.temp_training_records = JSON.parse(result);
          // }); 
        },

        approveSelected: function(){
          if(this.chkCompID == ""){
            alert("No Selected Contractor");
          }else{
            $.post("<?php echo base_url() ?>/induction_health_safety/approve_updates",
            {
              comp_id: this.chkCompID
            },
            function(result){
              location.reload();
                this.chkCompID = "";
            });
          } 
        },

        approveSelectedSiteStaff: function(){
          if(this.chkcontSiteStaffID == ""){
            alert("No Selected Site Staff");
          }else{
            $.post("<?php echo base_url() ?>/induction_health_safety/approve_updates_site_staff",
            {
              site_staff_id: this.chkcontSiteStaffID
            },
            function(result){
              location.reload();
            }); 
          }
        },


        showSendSiteStaffLink: function(){
          this.chk_sel_cont_sending = [];
          this.searchContractor = '';
          $("#cc").val("");
          $.post("<?php echo base_url() ?>/induction_health_safety/sending_email_default",
          { 
            chk_sel_cont_sending: this.chk_sel_cont_sending,
            cc: this.cc,
            bcc: this.bcc,
            subject: this.subject,
            message: this.message
          },
          function(result){

            var result_arr = result.split( '|' );
            sender_name = result_arr[0];
            sender_email = result_arr[1];
            $("#bcc").val(result_arr[2]);
            $("#subject").val(result_arr[3]);
            $("#message").text(result_arr[4]);
            assigned_user_id = result_arr[5];

          });          
          
          $("#send_site_staff_link").modal('show');
        },

        sendEmails: function(){
          $("#send_button").hide();
          $(".sending_button").show();
          var cc = $("#cc").val();
          var bcc = $("#bcc").val();
          var subject =  $("#subject").val();
          var message =  $("#message").val();
          if(this.chk_sel_cont_sending == ""){
            alert("Please select Contractor");
          }else{
            $.post("<?php echo base_url() ?>/induction_health_safety/send_email",
            {
              chk_sel_cont_sending: this.chk_sel_cont_sending,
              sender_name: sender_name,
              sender_email: sender_email,
              assigned_user_id: assigned_user_id,
              cc: cc,
              bcc: bcc,
              subject: subject,
              message: message
            },
            function(result){
              $("#send_button").show();
              $(".sending_button").hide();
              alert(result);
            });  
          }

        },

        showInductionVideos: function(){
          window.open('<?php echo base_url() ?>/induction_health_safety/inductions_videos', '_self');
        },

        showInductionProjects: function(){
          window.open('<?php echo base_url() ?>/induction_health_safety/inductions_projects', '_self');
        },
    
        view_site_login: function(){
          window.open("<?php echo base_url() ?>induction_health_safety/site_login_view");
        },

        show_site_staff_certificates: function(){
          $("#site_staff_covid_cert").modal("show");
        },
        
        fetch_site_staff_covid_cert(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/fetch_company_site_staff_covid_cert_file", 
          {
            'project_id': this.project_id.value,
            'company_id': this.cert_selected_comp_id
          }).then(response => { 
            this.ss_covid_cert = response.data;          
          }).catch(error => {
            console.log(error.response)
          });
          // axios.post(baseurl+"induction_health_safety/fetch_site_staff_covid_cert_file", 
          // {
          // }).then(response => { 
          //   this.ss_covid_cert = response.data;            
          // }).catch(error => {
          //   console.log(error.response)
          // });
          
        },


        showCovidCertFile: function(temp_contractors_staff_id,company_id){
          $.post("<?php echo base_url() ?>/induction_health_safety/fetch_covid_file",
          {
            site_staff_id: temp_contractors_staff_id
          },
          function(result){
            $("#iframe").attr("src", "<?php echo base_url().'uploads/company/covid_cert/' ?>"+company_id+"/"+result);
            
            $("#mdl_covid_file_preview").modal('show');
          }); 
          
        },

        fetch_project_per_state: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/fetch_project_per_state", 
          {
            'state_id': 6
          }).then(response => {             
          }).catch(error => {
            console.log(error.response)
          });
        },

        view_certificate: function(img_path,company_id){
          var img_path = "<?php echo base_url() ?>/uploads/company/covid_cert/"+company_id+"/"+img_path;
          $("#iframe").attr("src", img_path);
          $("#mdl_covid_file_preview").modal('show');
        },

        printSiteStaffwihCovidCert: function(){
          axios.post("<?php echo base_url() ?>/induction_health_safety/print_ss_with_covid_cert", 
          {
            'project_id': this.project_id.value
          }).then(response => { 
            window.open('<?php echo base_url() ?>/docs/site_staff_with_covid_certificates/'+response.data+'.pdf?q='+$.now(), '', 'height=590,width=850,top=100,left=100,location=no,toolbar=no,resizable=yes,menubar=no,scrollbars=yes',true);
            // this.ss_covid_cert = response.data;          
          }).catch(error => {
            console.log(error.response)
          });
          
        },
      },

      computed:{
        totalFocusStaffInduction: function(){
          var total = 0;
          for (var key in this.site_staff) {
            if(this.site_staff[key].general_induction_date !== '0000-00-00'){
              total++;
            }
          }
            
          return total;            

        },

        totalTempContNo: function(){

          var total = 0;
          var comp_selected = "";
          for (var key in this.uploaded_site_staff) {
            if(this.uploaded_site_staff[key].induction_date_sent !== '0000-00-00'){
              if(comp_selected !== this.uploaded_site_staff[key].company_name && this.uploaded_site_staff[key].company_name !== undefined){
                total++;

                comp_selected = this.uploaded_site_staff[key].company_name;
              }
            }
          }

          return total;
          
        },

        totalContInduction: function(){

          var total = 0;
          for (var key in this.contractor_site_staff) {
            if(this.contractor_site_staff[key].general_induction_date !== '0000-00-00'){
              total++;
            }
          }
            
          return total;            

          
        },

        sortContractorWithSiteStaff: function(){
          return this.contractors_with_site_staff.sort((a,b) => a.company_name > b.company_name ? 1 : -1);
        },

        sortContractorSubmitted: function(){
          return this.contractors_submitted.sort((a,b) => a.company_name > b.company_name ? 1 : -1);
        },

        filterSiteStaff: function(){
          this.site_staff.sort((a,b) => a.user_first_name > b.user_first_name ? 1 : -1);

          return this.site_staff.filter((siteStaff) => {
            return siteStaff.user_first_name.toLowerCase().match(this.searchSiteStaff.toLowerCase()) || siteStaff.user_last_name.toLowerCase().match(this.searchSiteStaff.toLowerCase());
          });
        },

        filterContractorSiteStaff: function(){
          // this.contractor_site_staff.sort((a,b) => a.company_name > b.company_name ? 1 : -1  || a.site_staff_fname > b.site_staff_fname ? 1 : -1 );
          return this.contractor_site_staff.filter((siteStaff) => {
            return siteStaff.site_staff_fname.toLowerCase().match(this.searchContractorSiteStaff.toLowerCase()) || siteStaff.site_staff_sname.toLowerCase().match(this.searchContractorSiteStaff.toLowerCase());
          });
        },

        filterTempContSiteStaff: function(){
          var vm = this;
          var TempsiteStaff = vm.TempContSiteStaff;
          

          if(TempsiteStaff === "All") {
            return vm.temp_contractors_list;
          }else {
            return vm.temp_contractors_list.filter(function(siteStaff) {
              return siteStaff.company_id === TempsiteStaff;
            });
          }

        },

        filterUnsubmittedTempContSiteStaff: function(){
          var vm = this;
          var TempsiteStaff = vm.UnsubmittedTempContSiteStaff;
          

          if(TempsiteStaff === "All") {
            return vm.temp_contractors_list;
          }else {
            return vm.temp_contractors_list.filter(function(siteStaff) {
              return siteStaff.company_id === TempsiteStaff;
            });
          }

        },

        filterContractor: function(){
          this.contractors_list.sort((a,b) => a.company_name > b.company_name ? 1 : -1);

          return this.contractors_list.filter((contractor) => {
            return contractor.company_name.toLowerCase().match(this.searchContractor.toLowerCase());
          });
        },

        filterUploadedSiteStaff: function(){
          
          // this.uploaded_site_staff.sort((a,b) => a.company_name > b.company_name ? 1 : -1  || a.staff_fname > b.staff_fname ? 1 : -1);

          return this.uploaded_site_staff.filter((contractor) => {
            return contractor.staff_fname.toLowerCase().match(this.searchContractorSiteStaffSubmitted.toLowerCase())  || contractor.staff_sname.toLowerCase().match(this.searchContractorSiteStaffSubmitted.toLowerCase());
          });
        },

        // filterContractorforCovid:function(){
        //   return this.project_contractors.filter((contractor) => {
        //     return contractor.company_name.toLowerCase().match(this.searchContractorCovid.toLowerCase());
        //   });

        // },

        total_submitted_staff: function(){
          var total = 0;
          for (var key in this.uploaded_site_staff) {
            if(this.uploaded_site_staff[key].induction_date_sent !== '0000-00-00'){
              total++;
            }
          }
            
          return total;  
        },
        
        
      } 
  });
</script>