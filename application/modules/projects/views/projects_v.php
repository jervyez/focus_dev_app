<script src="<?php echo base_url(); ?>js/vue.js"></script>
<script src="<?php echo base_url(); ?>js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>js/jmespath.js"></script>

<?php date_default_timezone_set("Australia/Perth");  // date is set to perth and important setting for diff timezone acounts ?>
<?php $this->load->module('company'); ?>
<?php $this->load->module('projects'); ?>
<?php $this->load->module('bulletin_board'); ?>
<?php $this->load->model('admin_m'); ?>

                    <?php $focus_id_main_display = $this->session->userdata('set_view_company_project'); ?>
<?php 

  $progress_reports = $this->session->userdata('progress_report');
  $get_table_status = $this->input->get('status');
  $custom_q = '';


  $current_year = date('Y');
  $default_yearsBack = $current_year - 2;


  $get_var = array();
  $get_var = $_GET;

//  var_dump($get_var);




  if(  isset($_GET['year']) && $_GET['year'] != '' &&    $_GET['year'] == '0'  ){
    $custom_q = "  AND UNIX_TIMESTAMP( STR_TO_DATE(`project`.`project_date`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('01/01/$default_yearsBack', '%d/%m/%Y') ) ";
  }elseif( isset($_GET['year']) && $_GET['year'] != '' && $_GET['year'] ==  'all'   ){
    

  $custom_q = '';
  }else{

    if(isset($_GET['year']) && $_GET['year'] != ''){

      $start_year = $_GET['year'];
      $endin_year = $_GET['year']+1;
      $custom_q = "  AND UNIX_TIMESTAMP( STR_TO_DATE(`project`.`project_date`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('01/01/$start_year', '%d/%m/%Y') ) AND UNIX_TIMESTAMP( STR_TO_DATE(`project`.`project_date`, '%d/%m/%Y') ) <= UNIX_TIMESTAMP( STR_TO_DATE('01/01/$endin_year', '%d/%m/%Y') ) ";
 
    }else{

    $custom_q = "  AND UNIX_TIMESTAMP( STR_TO_DATE(`project`.`project_date`, '%d/%m/%Y') ) >= UNIX_TIMESTAMP( STR_TO_DATE('01/01/$default_yearsBack', '%d/%m/%Y') ) ";
    }



  }
 
/*

  if(  isset($_GET['year']) && $_GET['year'] != '' &&  ( $_GET['year'] != '0' || $_GET['year'] != 'all' ) ){

  }if(  isset($_GET['year']) && $_GET['year'] != '' && $_GET['year'] == 'all' ){

    $custom_q = '';

  }else{
    
    
  
  }


 echo '<h1>'.$custom_q.'</h1>';
*/
 

 

  $user_responsible = 0;
  $user_query = $this->admin_m->fetch_admin_default_email_message();
  foreach ($user_query->result_array() as $row){
    $user_id = $row['user_id'];
    $message_content = $row['message_content'];
    $sender_name = $row['sender_name'];
    $sender_email = $row['sender_email'];
    $bcc_email = $row['bcc_email'].",insurance@focusshopfit.com.au";

    $newdata = array(
      'message_content'  => $message_content,
      'sender_name'  => $sender_name,
      'sender_email'  => $sender_email,
      'bcc_email'  => $bcc_email,
    );

    $this->session->set_userdata($newdata);
  }

  $focus_user_id = $this->session->userdata('user_id'); 
  if($user_id == $focus_user_id  || $focus_user_id == 6){
    $send_email = $this->session->userdata('auto_send_email'); 
    
    if($send_email == "" || $send_email == 0){
      $newdata = array(
                     'auto_send_email'  => 1
                 );

      $this->session->set_userdata($newdata);

      $expired_insurance = $this->company->check_for_expired_insurance(); 

      if($expired_insurance > 0){
?>

      <script type="text/javascript">
        $(document).ready(function() {
          $("#cont_sending_button").hide();
          $("#lbl_expired").text(<?php echo $expired_insurance ?>);
          $('#auto_send_confirmation').modal({
            backdrop: 'static',
            keyboard: true,
            show: true
          })

          //$("#auto_send_confirmation").modal('show');
          $("#auto_send_email").click(function(){
            $.post(baseurl+"company/autosend_email_expired_insurance",
            {
            },
            function(result){
              alert(result);
              $("#auto_send_confirmation").modal('hide');
            });
            
          });

          $("#view_list").click(function(){
            $("#auto_send_confirmation").modal('hide');
            $('#list_of_contractors').modal({
              backdrop: 'static',
              keyboard: true,
              show: true
            });

            $.post(baseurl+"company/contractors_list",
            {
            },
            function(result){
              $('#contractors_list').html(result);
            });
            
          });

          window.checkall_contractor = function(){
            if($('.checkall_contractor').is(':checked')){
                $(".chk_contractor_list").each(function(){
                  this.checked = true;
                });
            }else{
                $(".chk_contractor_list").each(function(){
                  this.checked = false;
                });
            }
          }

          $("#contlist_send_email").click(function(){
            var checkboxValues = [];
            $('input[name=chk_contractor_list]:checked').map(function() {
              checkboxValues.push($(this).val());
            });
            $("#cont_sending_button").show();
            $("#contlist_send_email").hide();
            $.post(baseurl+"company/selected_contractor_send_email",
            {
              checkboxValues: checkboxValues
            },
            function(result){
              alert(result);
              $("#cont_sending_button").hide();
              $("#contlist_send_email").show();
              $('#list_of_contractors').modal('hide');
            });
          });
        });
      </script>
<?php
      }
    }
  }
?>
 
<div class="modal fade" id="auto_send_confirmation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-sm">
    <div class="modal-content" style = "width:400px">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Auto Send Confirmation</h4>
      </div>
      <div class="modal-body">
        <p><label for="" id = "lbl_expired"></label> of the Contractor/s Insurances is about to expire. Would you like to Auto send Email to the contractor/s?</p>

      </div>
      <div class="modal-footer">
        <button type = "button" class="btn btn-warning" id = "view_list">View Contractors List</button>
        <button type = "button" class="btn btn-success pull-right" data-dismiss="modal">No</button>
        <button type = "button" class="btn btn-primary pull-right" id = "auto_send_email">Yes</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="brand_logo_view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style = "z-index: 1100 !important;">
  <div class="modal-dialog  modal-sm">
    <div class="modal-content" style = "width:400px" id = "brand_app">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="BrandLabel"></h4>
      </div>
      <div class="modal-body" style = "height: 300px">
        <form action="<?php echo base_url(); ?>induction_health_safety/upload_brand_logo" method="post" enctype="multipart/form-data">
          <span><b style = "color: red">Note: Only jpg file are allowed to be uploaded</b></span>
          <span class="btn btn-primary btn-sm btn-block btn-file">
            <i class = "fa fa-plus-circle"></i> Upload Logo<input type="file" name="userfile[]" multiple="multiple" accept="image/*" onchange="form.submit()">
          </span>
          <input type="hidden" id = "brand_id" name = "brand_id">
        </form>
        <div class="col-sm-12" style = "height: 200px; overflow:auto; border: 1px solid #888">
          <img id = "brandLogo" src="" alt="" style = "width: 100%">
        </div>
        
      </div>
      <div class="modal-footer">
        <button type = "button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="list_of_contractors" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-sm">
    <div class="modal-content" style = "width:400px">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">List of Contractors with Expired Insurance</h4>
      </div>
      <div class="modal-body">
        <div id="contractors_list" style = "height: 300px; overflow: auto">
        </div>
      </div>
      <div class="modal-footer">
        <button type = "button" class="btn btn-success pull-right" data-dismiss="modal">Close</button>
        <button type = "button" class="btn btn-primary pull-right" id = "contlist_send_email">Send</button>
        <button class="btn btn-warning" id = "cont_sending_button"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Sending...</button>
      </div>
    </div>
  </div>
</div>
<!-- title bar -->
<div class="container-fluid head-control">
	<div class="container-fluid">
		<div class="row">

			<div class="col-md-3 col-sm-3 col-xs-12 pull-left">
				<header class="page-header">

      
					<h3><?php $datestring = "%l, %F %d, %Y"; $time = time(); //use time() for timestamp  ?>
						<?php echo $screen; ?> Screen<br><small><?php echo mdate($datestring, $time); #echo date("l, F d, Y"); ?></small>
					</h3>
				</header>
			</div>
 
     

			<div class="page-nav-options col-md-9 col-sm-9 col-xs-12 pull-right hidden-xs">
				<ul class="nav nav-tabs navbar-right">
					<li>
						<a href="<?php echo base_url(); ?>"><i class="fa fa-home"></i> Home</a>
					</li>  
          <?php if($this->session->userdata('is_admin') == 1): ?>
          <li>
            <a href="<?php echo base_url(); ?>site_login"><i class="fa fa-map-marker"></i> Site Login</a>
          </li>   
          <?php endif; ?> 

          <?php if($this->session->userdata('is_admin') == 1 || $this->session->userdata('user_role_id') == 2 || $this->session->userdata('user_role_id') == 3 || $this->session->userdata('user_role_id') == 4  || $this->session->userdata('user_id') == 9 || $this->session->userdata('user_role_id') == 20): ?>
          <li>
            <a href="<?php echo base_url(); ?>induction_health_safety/inductions_projects"> Induction</a>
          </li> 
          <?php endif; ?>  

          <?php if($this->session->userdata('projects') >= 2): ?>
					<li class="">
						<a href="#" class="btn-small btn-primary" data-toggle="modal" data-target="#wip_filter_modal"><i class="fa fa-print"></i> Report</a>
					</li>  		  
          <?php endif; ?>



          <li>
            <a class="btn-small prj_amndnts_bttn"><i class="fa fa-file-text-o"></i> Amendments</a>
          </li>

            <?php if($this->session->userdata('projects') >= 1): ?>
					<li>
						<a class="btn-small sb-open-right"><i class="fa fa-file-text-o"></i> Project Comments</a>
					</li>
  <?php endif; ?>


          <?php if($this->session->userdata('user_role_id') == 3 ||$this->session->userdata('user_role_id') == 20 || $this->session->userdata('user_role_id') == 2 || $this->session->userdata('user_role_id') == 7   || $this->session->userdata('user_role_id') == 16  || $this->session->userdata('is_admin') == 1 || $this->session->userdata('user_id') == 6 ): ?>
             <li>
              <a href="<?php echo base_url(); ?>projects/projects_wip_review" class="btn-small btn-primary" ><i class="fa fa-file-text-o"></i> WIP Review</a>
            </li>
          <?php endif; ?>
         
          <?php if($this->session->userdata('quick_quote') == 1): ?>
          <li class="">
            <a href="<?php echo base_url(); ?>quick_quotes" class="btn-small btn-primary" > Quick Quotes</a>
          </li>  
          <?php endif; ?>
          



         <?php if($this->session->userdata('is_admin') == 1 || $this->session->userdata('user_id') == 6  ||  ( $this->session->userdata('projects') >= 1 && $this->session->userdata('user_role_id') == 2   )       ): ?>
          <li class="">
            <a href="#" class="btn-small btn-primary" data-toggle="modal" data-target="#brands_list"> Brands</a>
          </li>
          <?php endif; ?>


           <li class="">
            <a href="<?php echo base_url(); ?>projects/document_storage" class="btn-small btn-primary"><em class="fa fa-cloud-upload"></em> Doc Storage</a>
          </li> 
          
				</ul>
			</div>

		</div>
	</div>
</div>
<!-- title bar -->


<?php if(   $this->session->userdata('projects') >= 1 && $this->session->userdata('user_role_id') == 2        ): ?>
  <script type="text/javascript">
    /* FOR PA ONLY*/
    setTimeout(function(){  
      $('#brands_list .modal-body table').find('tr td button.btn-info').each (function() {
        $(this).remove();
      });  

      $('#brands_list .modal-body table').find('tr td button.btn-danger').each (function() {
        $(this).remove();
      });

      $('#brands_list .modal-body table').find('tr td input.brand_name_edit').each (function() {
        $(this).remove();
      });

      $('#brands_list .modal-body table').find('tr td button.btn-success').each (function() {

        var id_name = $(this).attr('id');
        if( id_name.includes("view") ){

         $(this).html('<i class="fa fa-eye fa-lg" aria-hidden="true"></i>');
       }else{
         $(this).remove();
       }

     });

      $('#brands_list .modal-body .input-group input#brand_name').parent().remove();
      $('#brands_list .modal-body hr').remove();

      $('#brand_logo_view form').remove();

    },500);

    /* FOR PA ONLY*/
  </script>
<?php endif; ?>



<div class="container-fluid">
	<!-- Example row of columns -->
	<div class="row">				
		<?php $this->load->view('assets/sidebar'); ?>
		<div class="section col-sm-12 col-md-11 col-lg-11">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-9">

						<div class="left-section-box clearfix">


						<?php if(@$this->session->flashdata('project_deleted')): ?>
							<div class="m-15">
								<div class="border-less-box alert alert-danger fade in">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
									<h4>Opps! No turning back now!</h4>
									<?php echo $this->session->flashdata('project_deleted');?>
								</div>
							</div>
						<?php endif; ?>

						<div class="clearfix"></div>


							<div class="box-head pad-10 clearfix">
								<div class="pull-right" style="margin-top: -15px;">
								<?php if($this->session->userdata('projects') >= 2): ?>

                  <?php if($this->session->userdata('is_admin') == 1): ?>

                   <div id="" class="pull-right">

                     <div class="btn-group">
                     <a href="<?php echo current_url(); ?>/add" class="btn btn-primary pull-right" ><i class="fa fa-briefcase"></i>&nbsp; Add New</a>



                     <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                         <span class="caret"></span>
                         <span class="sr-only">Toggle Dropdown</span>
                       </button>
                       <ul class="dropdown-menu" role="menu" style="display: none;">
                         <li><a href="<?php echo current_url(); ?>/add_company_project" class="">Company Project</a></li>
                       </ul>
                     </div>

                   </div>

                 <?php elseif($this->session->userdata('company_project') == 1): ?>
                  <a href="<?php echo current_url(); ?>/add_company_project" class="btn btn-primary pull-right"><i class="fa fa-briefcase"></i>&nbsp; Add New</a>

                 <?php else: ?>


									<a href="<?php echo current_url(); ?>/add" class="btn btn-primary pull-right"><i class="fa fa-briefcase"></i>&nbsp; Add New</a>


                <?php endif; ?>
								

                <?php endif; ?>
									<div class="clearfix"></div>
									<select class="form-control m-top-10 select-client-tbl right" style="float: right; width: 180px;">
										<option value="">Select Client</option>
										<?php $this->company->company_list('dropdown'); ?>
									</select>
									


                  <?php if($this->session->userdata('user_role_id') == 3 ||$this->session->userdata('user_role_id') == 16 ||$this->session->userdata('user_role_id') == 20 || $this->session->userdata('user_role_id') == 2 || $this->session->userdata('user_role_id') == 8 || $this->session->userdata('user_role_id') == 7 || $this->session->userdata('company_project') == 1 ): ?>

                  
                    <select class="form-control m-top-10 select-personal right"  style="float: right; width: 120px; margin-right: 5px;">
                      
                      <option value="ORD">View All</option>

                      <?php if($this->session->userdata('user_role_id') == 3 ){
                          echo '<option value="PM">Personal</option>';
                        }elseif($this->session->userdata('user_role_id') == 20 ){
                          echo '<option value="PM">Personal</option>';
                        }elseif($this->session->userdata('user_role_id') == 16 ){
                          echo '<option value="PM">Personal</option>';
                        }elseif($this->session->userdata('user_role_id') == 2 || $this->session->userdata('user_role_id') == 7 ){
                          echo '<option value="PA">Personal</option>';
                        }elseif($this->session->userdata('user_role_id') == 8){
                          echo '<option value="EST">Personal</option>';
                        }elseif($this->session->userdata('company_project') == 1 ){
                          echo '<option value="PM">Personal</option>';
                        }else{ }
                      ?>

                    </select>
                    <script type="text/javascript">
                      var select_personal = '';
                      $('select.select-personal option').each(function(){
                        value = this.value;
                      });
                      select_personal = value;

                     // alert(select_personal);

                     <?php if($this->session->userdata('default_projects_view_personal') == 1 ) : ?>
                     $('select.select-personal').val(select_personal); 
                   <?php endif; ?>

                   </script>

                  <?php endif; ?>


                  <div class="btn-group pull-right m-10" role="group"  >


                    <div class="btn-group" role="group">
                      <button id="btnGroupDrop1" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span id="focus_company_selection">AlxxCompany</span>
                        &nbsp;<span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">



                        <?php $status_link_get = '&'; ?>
                        <?php
                        foreach ($get_var as $key => $value) {
                          if($key != 'fompj'){
                            $status_link_get .= $key.'='.$value.'&';
                          }
                        }
                        $status_link_get = substr($status_link_get, 0, -1);
                        ?> 




                      
                          <li><a href="?fompj=0<?php echo $status_link_get; ?>">View All</a></li>


                              





                          <?php foreach ($focus as $key => $value): ?>                         
                            <li><a href="?fompj=<?php echo $value->company_id; ?><?php echo $status_link_get; ?>"><?php echo $value->company_name; ?></a></li>
                          <?php endforeach; ?>

 

                      </ul>
                    </div>
 


                  <?php if(isset( $_GET['fompj'] )  && $_GET['fompj']!= ''  ): ?>
                    <?php if($_GET['fompj'] == 5  ): ?>
                      <script type="text/javascript">$("#focus_company_selection").text("Focus Shopfit Pty Ltd");</script>
                    <?php elseif($_GET['fompj'] == 6 ): ?>
                      <script type="text/javascript">$("#focus_company_selection").text("Focus Shopfit NSW Pty Ltd");</script>
                    <?php elseif($_GET['fompj'] == 3197 ): ?>
                      <script type="text/javascript">$("#focus_company_selection").text("Focus Maintenance");</script>
                    <?php else: ?>
                      <script type="text/javascript">$("#focus_company_selection").text("All Focus Company");</script>      
                    <?php endif; ?>
                  <?php else: ?>

 
                    <script type="text/javascript">
                      var fcomP_id = <?php echo "$focus_id_main_display "; ?>;
                      if(fcomP_id == 5){
                        $("#focus_company_selection").text("Focus Shopfit Pty Ltd");
                      }else if(fcomP_id == 6){
                        $("#focus_company_selection").text("Focus Shopfit NSW Pty Ltd");
                      }else if(fcomP_id == 3197){
                        $("#focus_company_selection").text("Focus Maintenance");
                      }else{
                        $("#focus_company_selection").text("All Focus Company");
                      }
                    </script>
 
                  <?php endif; ?>
  

                    <div class="btn-group" role="group">
                      <button id="prj_status_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span id="btn_project_label_status">Project Status</span>
                        &nbsp;<span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" aria-labelledby="prj_status_btn">
                        <?php /* if(isset($_GET['fompj']) && $_GET['fompj']!= ''): ?>
                          <?php $fcomp_id = $_GET['fompj'];  ?>
                          <li><a href="?status=all&fompj=<?php echo $fcomp_id; ?>">All Status</a></li>
                          <li><a href="?status=wip&fompj=<?php echo $fcomp_id; ?>">WIP</a></li>
                          <li><a href="?status=quote&fompj=<?php echo $fcomp_id; ?>">Quotes</a></li>
                          <li><a href="?status=unset&fompj=<?php echo $fcomp_id; ?>">Unaccepted Quotes</a></li>
                          <li><a href="?status=invoiced&fompj=<?php echo $fcomp_id; ?>">Invoiced</a></li>
                          <li><a href="?status=paid&fompj=<?php echo $fcomp_id; ?>">Paid</a></li>
                          <li><a href="?status=warranty&fompj=<?php echo $fcomp_id; ?>">Warranty</a></li>
                        <?php else: ?>

                       

                      <?php endif; */ ?>


                        <?php $status_link_get = '&'; ?>
                        <?php
                          foreach ($get_var as $key => $value) {
                            if($key != 'status'){
                              $status_link_get .= $key.'='.$value.'&';
                            }
                          }
                          $status_link_get = substr($status_link_get, 0, -1);
                        ?>

                        <li><a href="?status=all<?php echo $status_link_get; ?>">All Status</a></li>
                        <li><a href="?status=wip<?php echo $status_link_get; ?>">WIP</a></li>
                        <li><a href="?status=quote<?php echo $status_link_get; ?>">Quotes</a></li>
                        <li><a href="?status=unset<?php echo $status_link_get; ?>">Unaccepted Quotes</a></li>
                        <li><a href="?status=invoiced<?php echo $status_link_get; ?>">Invoiced</a></li>
                        <li><a href="?status=paid<?php echo $status_link_get; ?>">Paid</a></li>
                        <li><a href="?status=warranty<?php echo $status_link_get; ?>">Warranty</a></li>




                      </ul>
                    </div>

 

                    <div class="btn-group  tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="<center>Filters projects based from the year created.</center>"  role="group">
                      <button id="prj_year_btn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span id="btn_project_label_status">Creation Year <em id="" class="creation_text_val"></em></span>
                        &nbsp;<span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" aria-labelledby="prj_year_btn" style="display: none;">
                        <?php $prj_link_get = '&'; ?>
                        <?php
                          foreach ($get_var as $key => $value) {
                            if($key != 'year'){
                              $prj_link_get .= $key.'='.$value.'&';
                            }
                          }
                          $prj_link_get = substr($prj_link_get, 0, -1);
                        ?>
                        <?php $curr_year_limit = date('Y')-2; ?>
                        <li><a href="?year=0<?php echo $prj_link_get; ?>"><?php echo $curr_year_limit; ?> to Present</a></li>
                        <?php
                          for ($i=$curr_year_limit-1; $i >= 2015; $i--) {
                            echo '<li><a href="?year='.$i.''.$prj_link_get.'">'.$i.'</a></li>';
                          }
                        ?>
                        <li class="tooltip-enabled" title="" data-html="true" data-placement="bottom" data-original-title="<center><i class='fa fa-info-circle'></i> &nbsp; Loading all projects will take longer time.</center>"><a href="?year=all<?php echo $prj_link_get; ?>">Show All</a></li>

                        <?php if(    isset($_GET['year']) && $_GET['year'] > 0    ): ?>
                            <script type="text/javascript">$('.creation_text_val').text('<?php echo $_GET["year"]; ?>'); </script>

                        <?php elseif(   isset($_GET['year']) &&   $_GET['year'] == 'all'  ): ?>
                            <script type="text/javascript">$('.creation_text_val').text(' All Projects '); </script>
                                
                        <?php else: ?>
                            <script type="text/javascript">$('.creation_text_val').text('<?php echo "$curr_year_limit to Present"; ?>'); </script>
                          
                        <?php endif; ?>

                      </ul>
                    </div>


                  </div>

                  



                  <?php if(isset( $_GET['status'] )  && $_GET['status']!= '' ): ?>
                    <?php if($_GET['status'] == 'all'): ?>
                      <script type="text/javascript">$("#btn_project_label_status").text("<?php echo ucwords($_GET['status']); ?> Status");</script>
                    <?php elseif($_GET['status'] == 'unset'): ?>
                      <script type="text/javascript">$("#btn_project_label_status").text("Unaccepted Quotes");</script>
                    <?php else: ?>
                      <script type="text/javascript">$("#btn_project_label_status").text("<?php echo ucwords($_GET['status']); ?> Projects");</script>
                    <?php endif; ?>
                  <?php else: ?>


<?php $project_status_view = $this->session->userdata('default_projects_landing'); ?>

<script type="text/javascript">

  var proj_stat_display = <?php echo $project_status_view; ?>;

  if (proj_stat_display == 0) {
    $("#btn_project_label_status").text("All Status");
  } else if(proj_stat_display == 1) {
    $("#btn_project_label_status").text("WIP Projects");
  }  else if(proj_stat_display == 2) {
    $("#btn_project_label_status").text("Quotes Projects");
  }   else if(proj_stat_display == 3) {
    $("#btn_project_label_status").text("Unaccepted Quotes");
  }   else if(proj_stat_display ==4) {
    $("#btn_project_label_status").text("Invoiced Projects");
  }  else if(proj_stat_display ==5) {
    $("#btn_project_label_status").text("Paid Projects");
  } 


</script>
<?php endif; ?>



<!-- 

                  <div id="" class="right m-top-10 m-right-5" style="float:right; width:105px;">
                 
                     <select class="form-control m-top-10 select_focus_id_main_display tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Selects projects to display based on focus company."  style=" float: right;    width: 100px;    margin: 0;">                      
                       <option value="">View All</option>
                       <option value="5">WA</option>
                       <option value="6">NSW</option>
                     </select>


                     <script type="text/javascript"> $('select.select_focus_id_main_display').val('<?php echo $focus_id_main_display; ?>'); </script>
                


                 </div>


									<select class="form-control m-top-10 select_table_status right"  style="float: right; width: 110px; margin-right: 5px;">
								   
                    <option value="all">View All</option>
										<option value="wip" selected >WIP</option>
										<option value="quote">Quotes</option>
                    <option value="unset">Unaccepted Quotes</option>
										<option value="invoiced">Invoiced</option>
										<option value="paid">Paid</option>
									</select>
 -->
                  <?php 



                  if(isset($get_table_status ) && $get_table_status != ''){
                   // echo '<script type="text/javascript">$("select.select_table_status").val("'.$get_table_status.'"); </script>';
                    $deff_stat = $get_table_status;
                   
                    if($get_table_status == 'unset'){
                      $curr_page_name = 'Unaccepted Quotes';
                    }else{
                      $curr_page_name =  ucwords(strtolower($get_table_status));
                    }

                  }else{

                    $project_status_view = $this->session->userdata('default_projects_landing');

                    if ($project_status_view == 0) {
                      //  echo '<script type="text/javascript">$("select.select_table_status").val("all"); </script>';
                        $curr_page_name = 'All';
                        $deff_stat = 'all';
                    } elseif ($project_status_view == 1) {
                   //     echo '<script type="text/javascript">$("select.select_table_status").val("wip"); </script>';
                        $curr_page_name = 'WIP';
                        $deff_stat = 'wip';
                    } elseif ($project_status_view == 2) {
                     //   echo '<script type="text/javascript">$("select.select_table_status").val("quote"); </script>';
                        $curr_page_name = 'Quotes';
                        $deff_stat = 'quote';
                    } elseif ($project_status_view == 3) {
                      //  echo '<script type="text/javascript">$("select.select_table_status").val("unset"); </script>';
                        $curr_page_name = 'Unaccepted Quotes';
                        $deff_stat = 'unset';
                    } elseif ($project_status_view == 4) {
                     //   echo '<script type="text/javascript">$("select.select_table_status").val("invoiced"); </script>';
                        $curr_page_name = 'Invoiced';
                        $deff_stat = 'invoiced';
                    } elseif ($project_status_view == 5) {
                     //   echo '<script type="text/javascript">$("select.select_table_status").val("paid"); </script>';
                        $curr_page_name = 'Paid';
                        $deff_stat = 'paid';
                    }



                  }

                  ?>





								</div>
								<label style="margin-top: 0px;"><?php echo $curr_page_name;   ?>  <?php echo $screen; ?></label>
<span class="fa fa-film pointer open_help_vids_mpd" data-toggle="modal" data-target="#help_video_group"> </span>
								<p>This is where the projects are listed. 



                </p>
								<!-- <p><a href="#" class="tooltip-test" title="Tooltip">This link</a> and <a href="#" class="tooltip-test" title="Tooltip">that link</a> should have tooltips on hover.</p>						 -->		
							</div>
							<div class="box-area pad-10">
								<table id="projectTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
									<thead> 
                    <tr> <th>Number</th> <th>Project Name</th> <th>Client</th> <th>Category</th> <th><?php echo ($get_table_status == 'warranty' ? 'Warranty Date' : 'Job Date'); ?></th> <th>Total Cost</th> <th>Personal</th> <th>Status</th></tr> 
                  </thead> 
									<tbody>
										<?php echo $this->projects->display_all_projects($deff_stat,0,$custom_q); ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="box">
							<div class="box-head pad-5">
								<label><i class="fa fa-info-circle fa-lg"></i> Information</label>
							</div>
							<div class="box-area pad-5">
								<p>
									The table can be sortable by the header. It has search feature using a sub text or keyword you are searching. Clicking the Name of the company will lead to the Company Details Screen.
								</p>
							</div>
						</div>
					</div>

					<div class="col-md-3">
            <div class="box">
              <div class="box-head pad-5">
                <label><i class="fa fa-info-circle fa-lg"></i> Color Codes: <br><br>
                <strong class="wip">WIP</strong> &nbsp; &nbsp;
                <strong class="invoiced">Invoiced</strong> &nbsp; &nbsp;
                <strong class="paid">Paid</strong></label> &nbsp; &nbsp;
                <strong class="unset">Unaccepted Quotes</strong></label> &nbsp; &nbsp;
                <strong class="quotes">Quotes</strong></label> &nbsp; &nbsp;
              </div>
            </div>
          </div>

					<?php //if($this->session->userdata('user_id') == 48):

              if($progress_reports == 1): ?>
            <div class="col-md-3">
              <div class="box">
                <div class="box-head pad-5">
                  <label><i class="fa fa-file-image-o fa-lg"></i> Recently Uploaded PR Images</label> &nbsp;

                  <div class="pull-right">
                    <select class="form-control input-sm" id="list_users_pr_images" >
                      <option value="all">View All</option><?php echo $this->projects->list_users_pr_images(); ?>
                    </select>
                  </div> 
                </div>

                <div class="box-area pattern-sandstone pad-10">
                  <div class="box-content box-list collapse in">
                    <ul id="list_recent_pr_images" style="overflow-y: scroll; height: 250px;">
                      <?php echo $this->projects->list_recent_pr_images(25); ?>
                    </ul>
                  </div>
              </div>
            </div>
          </div>
        <?php endif; ?>


          <?php if($this->session->userdata('is_admin') == 1 || $this->session->userdata('user_id') == 9): ?>
          <div class="col-md-3 pull-right">
            <div class="box">
              <div class="box-head pad-5">
                <label><i class="fa fa-history fa-lg"></i> Job Date Removed</label> &nbsp;

                <div class="input-group pull-right" style="width:160px;">

                 <a href="<?php base_url(); ?>projects/read_csv_logs" target="_blank" class="btn input-group-addon btn-success  tooltip-enabled" title="" data-html="true" data-placement="top" data-original-title="Print CSV Report" style="color:#fff;"><i class="fa fa-print" aria-hidden="true"></i></a>  
                   <select class="form-control input-sm" id="list_users_removed_jobdate" ><option value="all">View All</option><?php echo $this->projects->list_users_removed_jobdate(); ?></select>
                </div> 


              </div>
              <div class="box-area pattern-sandstone pad-10">
                <div class="box-content box-list collapse in">
                 <ul id="list_removed_jobdate_prj" style="overflow-y: scroll;    height: 270px;">
                  <?php echo $this->projects->list_removed_jobdate_prj(25); ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
      
      
					
				</div>				
			</div>
		</div>
	</div>
</div>
   
       


<?php $this->load->view('assets/logout-modal'); ?>

<!-- Modal -->
<div class="report_result hide hidden"></div>






<!-- Brands List -->
<div class="modal fade" id="brands_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Brands List</h4>
      </div>
      <div class="modal-body">



        <div class="input-group m-bottom-10">
          <span class="input-group-addon" id="">Add New Brand</span>
          <input type="text" placeholder="Brand Name" class="form-control" id="brand_name" name="brand_name" value="">
          <div id="" class="input-group-addon btn btn-success add_brand_btn" style="color:#fff;">Save</div>
        </div>


        <hr style="padding: 0;margin: 14px 0;">



<table id="table" class="table table-striped table-bordered   no-m" cellspacing="0" width="100%">
                    <thead> <tr> <th style="border: none;">Brands</th>  <th style="border: none;" class="text-right">Options &nbsp;  &nbsp; </th>  </tr> </thead> 
                  </table>


<div id="" class="" style="    overflow-y: auto;    max-height: 452px;">
                    <table id="table" class="table table-striped table-bordered  no-m " cellspacing="0" width="100%"> 
                      <tbody class="dynamic_table_result_request">

                       <?php echo $this->projects->list_all_brands(); ?>

                   </table>
                  </div>       </div>


      <div class="modal-footer"> 
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>         

      </div>
    </div>
  </div>
</div>
<!-- Brands List -->







<script type="text/javascript">

  $('select#list_users_removed_jobdate').on("change", function(e) {
    var pm_selection = $(this).val();
    if(pm_selection == 'all'){
      $('ul#list_removed_jobdate_prj li').show();
    }else{
      $('ul#list_removed_jobdate_prj li').hide();
      $('ul#list_removed_jobdate_prj li.list_rem_user_'+pm_selection).show();
    }
  });

  $('select#list_users_pr_images').on("change", function(e) {
    var pm_selection = $(this).val();
    if(pm_selection == 'all'){
      $('ul#list_recent_pr_images li').show();
    }else{
      $('ul#list_recent_pr_images li').hide();
      $('ul#list_recent_pr_images li.list_pr_img_'+pm_selection).show();
    }
  });

$('.add_brand_btn').click(function(){
  var brnd_name = $('input#brand_name').val();

  $('.dynamic_table_result_request').html('<tr><td>Reloading List</td></tr>');
   ajax_data(brnd_name,'projects/add_brand','.dynamic_table_result_request');

   $('input#brand_name').val('')
});


function delete_brand(obj){
  var elm_id = obj.id.trim();

  $('button#'+elm_id).parent().parent().remove();

    ajax_data(elm_id,'projects/delete_brand','');
}


function update_brand(obj){
  var elm_id = obj.id.trim();
  var elm_id_raw = elm_id.split("_");
  var elm_brnd_id = elm_id_raw[1];  

  $('span#brnd_name_'+elm_brnd_id).hide();
  $('input#edt_brnd_inpt_'+elm_brnd_id).show();
  $('button#del_'+elm_brnd_id).hide();
  $('button#edt_'+elm_brnd_id).hide();
  $('button#save_'+elm_brnd_id).show();
}

function edit_save(obj){
  var elm_id = obj.id.trim();
  var elm_id_raw = elm_id.split("_");
  var elm_brnd_id = elm_id_raw[1]; 

  $('button#del_'+elm_brnd_id).show();
  $('button#edt_'+elm_brnd_id).show();
  $('button#save_'+elm_brnd_id).hide();
  var update_brand_name = $('input#edt_brnd_inpt_'+elm_brnd_id).val();
  $('input#edt_brnd_inpt_'+elm_brnd_id).hide();
  $('span#brnd_name_'+elm_brnd_id).text(update_brand_name).show();

  var data = update_brand_name+'|'+elm_brnd_id;
  ajax_data(data,'projects/update_brand','');
}
  

</script>






<!-- wip_filter_modal -->
<div class="modal fade" id="wip_filter_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Projects Report Filters</h4>
      </div>
      <div class="modal-body">
      	<div class="input-group m-bottom-10">
      		<span class="input-group-addon" id="">
      			<i class="fa fa-briefcase"></i>
      		</span>
      		<select class="form-control m-bottom-10 report_company">
      			<option value="">Select Client</option>     		

      			
            <?php
              foreach ($clients_list->result_array() as $row){
                echo '<option value="'.$row['company_id'].'" >'.$row['company_name'].'</option>';
              }
            ?>
      		</select>
      	</div>

      	<style type="text/css">div.prj_status{ width: 100%; }</style>

      	<div class="input-group m-bottom-10">
      		<span class="input-group-addon" id="">
      			<i class="fa fa-user"></i>
      		</span>
      		<select class="form-control select-pm-tbl m-bottom-10">
      			<option value="">Select Project Manager</option>
      			<?php /*
      			foreach ($pms->result_array() as $row){
              echo '<option value="'.$row['user_first_name'].' '.$row['user_last_name'].'|'.$row['user_id'].'" >'.$row['user_first_name'].' '.$row['user_last_name'].'</option>';
      			}
      		*/	?>
      		
      	 
         <?php
         $project_manager_q = $this->user_model->fetch_user_by_role(3);
         $project_manager = $project_manager_q->result();     foreach ($project_manager as $row){    

          echo '<option value="'.$row->user_first_name.' '.$row->user_last_name.'|'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
        }?>


         <?php
         $project_manager_q = $this->user_model->fetch_user_by_role(20);
         $project_manager = $project_manager_q->result();     foreach ($project_manager as $row){    
           
          echo '<option value="'.$row->user_first_name.' '.$row->user_last_name.'|'.$row->user_id.'">'.$row->user_first_name.' '.$row->user_last_name.'</option>';
        }?>
      		</select>
      	</div>


      	<div class="tooltip-enabled box-area clearfix input-group m-bottom-10" data-original-title="Notice: Selecting 'Not WIP' refers to projects that is never been accepted by the client as real project. 'WIP' are the projects currenlty on WIP. 'Invoiced' these are the projets are fully invoiced. 'Paid' are fully paid projects. ">
      		

      		<span class="input-group-addon" id="">
      			<i class="fa fa-list-alt"></i>
      		</span>
<select class="form-control prj_status m-bottom-10" id="prj_status">
      			 <?php // <option selected="selected" value="notwip">Not WIP</option> ?>
           <option selected="selected" value="quote">Quotes</option>
           <option value="unaccepted">Unaccepted Quotes</option>
      			<option value="wip">Currently WIP</option>
      			<option value="invoiced">Invoiced</option>
      			<option value="paid">Paid</option>
      		</select>
      	</div>




        <script type="text/javascript">
 



  $('select#prj_status').on("change", function(e) {

    var prj_status = $(this).val();
 

    if(prj_status == 'unaccepted'){
      $('.for_unaccepted_selection').show();
      $('.for_non_unaccepted').hide();



    }else{
      $('.for_unaccepted_selection').hide();
      $('.for_non_unaccepted').show();


    }

      $('#un_acepted_start_date').val('');
      $('#un_acepted_end_date').val(''); 
 

      $('#start_date_start').val('');
      $('#start_date').val('');

      $('#finish_date_start').val('');
      $('#finish_date').val('');

      $('#date_created_start').val('');
      $('#date_created').val(''); 

  });


</script>
      	

      	<div class="box-area clearfix  m-bottom-10">
      		<select class="form-control select-cat-tbl chosen-multi" id="select-cat-tbl" multiple="multiple">
      			<option selected="selected" value="Kiosk">Kiosk</option>
      			<option selected="selected" value="Full Fitout">Full Fitout</option>
      			<option selected="selected" value="Refurbishment">Refurbishment</option>
      			<option selected="selected" value="Strip Out">Strip Out</option>
            <option selected="selected" value="Design Works">Design Works</option>
      			<option selected="selected" value="Minor Works">Minor Works (Under $20,000.00)</option>
      			<option selected="selected" value="Maintenance">Maintenance</option>
<option selected="selected" value="Joinery Only">Joinery Only</option>
            <option  value="Company">Company</option>
      		</select>
      	</div>



<div id="" class="for_unaccepted_selection" style="display:none;">

        <hr style="padding: 0;margin: 14px 0;">
        <div class="input-group m-bottom-10">
          <span class="input-group-addon" id="">
            <i class="fa fa-calendar"></i> Un-Acepted Date A*
          </span>
          <input type="text" data-date-format="dd/mm/yyyy" placeholder="From" class="form-control datepicker" id="un_acepted_start_date" name="un_acepted_start_date" value="" >
        </div>



        <div class="input-group m-bottom-10">
          <span class="input-group-addon" id="">
            <i class="fa fa-calendar"></i> Un-Acepted Date B*
          </span>
          <input type="text" data-date-format="dd/mm/yyyy" placeholder="To" class="form-control datepicker" id="un_acepted_end_date" name="un_acepted_end_date" value="" >
        </div>

        <hr style="padding: 0;margin: 14px 0;">
</div>


<div id="" class="for_non_unaccepted">
        <div class="input-group m-bottom-10">
          <span class="input-group-addon" id="">
            <i class="fa fa-calendar"></i> Site Start A
          </span>
          <input type="text" data-date-format="dd/mm/yyyy" placeholder="From" class="form-control datepicker" id="start_date_start" name="start_date_start" value="" >
        </div>



        <div class="input-group m-bottom-10">
          <span class="input-group-addon" id="">
            <i class="fa fa-calendar"></i> Site Start B
          </span>
          <input type="text" data-date-format="dd/mm/yyyy" placeholder="To" class="form-control datepicker" id="start_date" name="start_date" value="" >
        </div>

</div>
      	<div class="input-group m-bottom-10">
      		<span class="input-group-addon" id="">$</span>
      		<input type="text" placeholder="Less Than Project Total Range" class="form-control number_format" id="cost_total" name="cost_total" value="">
      	</div>
<div id="" class="for_non_unaccepted">
        <hr style="padding: 0;margin: 14px 0;">

        <div class="input-group m-bottom-10">
          <span class="input-group-addon" id="">
            <i class="fa fa-calendar"></i> Site Finish A
          </span>
          <input type="text" data-date-format="dd/mm/yyyy" placeholder="From" class="form-control datepicker" id="finish_date_start" name="finish_date_start" value="" >
        </div>


        <div class="input-group m-bottom-10">
          <span class="input-group-addon" id="">
            <i class="fa fa-calendar"></i> Site Finish B
          </span>
          <input type="text" data-date-format="dd/mm/yyyy" placeholder="To" class="form-control datepicker" id="finish_date" name="finish_date" value="" >
        </div>


        <hr style="padding: 0;margin: 14px 0;">

        <input type="hidden" id="doc_type" name="doc_type" value="Projects" >

        <div class="input-group m-bottom-10">
          <span class="input-group-addon" id="">
            <i class="fa fa-calendar"></i> Project Date Created A
          </span>
          <input type="text" data-date-format="dd/mm/yyyy" placeholder="From" class="form-control datepicker" id="date_created_start" name="date_created_start" value="" >
        </div>


        <div class="input-group m-bottom-10">
          <span class="input-group-addon" id="">
            <i class="fa fa-calendar"></i> Project Date Created B
          </span>
          <input type="text" data-date-format="dd/mm/yyyy" placeholder="To" class="form-control datepicker" id="date_created" name="date_created" value="" >
        </div>

</div>
      	<div class="input-group m-bottom-10">
      		<span class="input-group-addon" id="">Sort</span>         
      		<select class="wip_sort form-control" id="wip_sort" name="wip_sort" title="invoice_sort*">
      			<option value="clnt_asc">Client Name A-Z</option>  
      			<option value="clnt_desc">Client Name Z-A</option>
      			<option value="srtrt_d_asc">Start Date Ascending</option> 
      			<option value="srtrt_d_desc">Start Date Descending</option>
      			<option value="fin_d_asc">Finish Date Ascending</option> 
      			<option value="fin_d_desc">Finish Date Descending</option>    
      			<option value="prj_num_asc" selected="selected" >Project Number Ascending</option>  
      			<option value="prj_num_desc">Project Number Descending</option>
            <option value="qte_num_asc">Quote Deadline Ascending</option>
            <option value="qte_num_desc">Quote Deadline Descending</option>
      		</select>       
      	</div>

        <div class="input-group m-bottom-10">
          <span class="input-group-addon" id="">Document Type</span>         
          <select class="output_file form-control" id="output_file" name="output_file" title="output_file*">
            <option value="pdf">PDF</option>
            <option value="csv">CSV</option>                               
          </select>       
        </div>

      	<div class="clearfix">
      		<button type="button" class="btn btn-primary print-wip pull-right" id="">Submit</button> <!-- data-dismiss="modal" -->
      		<button type="button" class="btn btn-default pull-right m-right-10" data-dismiss="modal">Cancel</button> 
      	</div>



      </div>
    </div>
  </div>
</div>
<!-- wip_filter_modal -->


<style type="text/css">
  .tooltip-inner{
    text-align:left;
  }

  .tooltip-inner {
    max-width: 250px;
  }
</style>

<script type="text/javascript">
    
  $(window).bind("pageshow", function() {
    // update hidden input field
    $('select.select-client-tbl').val('');

  });

  setTimeout(function(){  

    $("#projectTable_wrapper #projectTable_filter input").attr('autocomplete', 'new-password'); 
  },500);

  var app = new Vue({
    el: '#brand_app',
    data: {
    },
    mounted: function(){
    },

    methods: {
      getImageLogo: function(){
        $.post(baseurl+"induction_health_safety/get_brand_logo",
        {
          brand_id: brand_id
        },
        function(result){
          if(result == 1){
            $("#brandLogo").show();
            $("#brandLogo").attr("src",baseurl+'uploads/brand_logo/'+brand_id+'.jpg?'+new Date().getTime());
          }else{
            $("#brandLogo").hide();
            $("#brandLogo").attr("src","");
          }
          
        });
      },
    },

  });

  function view_brand(obj){
    var elm_id = obj.id.trim();
    var elm_var = elm_id.split('_');
    brand_id = elm_var[1];
    $("#brand_id").val(brand_id);
    var brand_name = $("#brnd_name_"+brand_id).text();
    $("#BrandLabel").text(brand_name);
    $("#brand_logo_view").modal('show');
    app.getImageLogo();
  }
</script>


<!-- _________________________________ HELP VIDEO SETUP _________________________________ -->
<?php $this->load->module('help_videos'); ?>
<div id="help_video_group" class="modal fade" tabindex="-1" data-width="760" >
  <div class="modal-dialog" style="width:85%;">
    <div class="modal-content">
      <div class="modal-header">
        <ul id="myTab" class="nav nav-tabs pull-right" style="border-bottom: 0;">
          <li class="now_playing_tab_btn"><a href="#now_playing" style="color:#555; display:none;" data-toggle="tab"><i class="fa fa-globe fa-lg"></i> Now Playing</a></li>
          <li class="help_videos_tab_btn active"><a href="#help_videos" style="color:#555;" data-toggle="tab" tabindex="20"><i class="fa fa-inbox fa-lg"></i> Videos</a></li> 
        </ul>
        <h4 class="modal-title"><em id="" class="fa fa-film"></em> Help Videos </h4>
      </div>
      <div class="modal-body" style="margin:0 !important; padding:0 !important;">
        <div class="tab-content">
          <div id="now_playing" class="tab-pane fade clearfix  in">
            <iframe style="width: 100%;height: 70%;background-repeat: no-repeat;background-color:#000;background-image: url('<?php echo base_url(); ?>uploads/misc/loading_bub.gif');background-position: center;background-size: 50px;" class="group_video_frame" ></iframe>
          </div>
          <div id="help_videos" class="tab-pane fade clearfix active in">
            <div id="" class="m-10 p-bottom-10 clearfix">
            <p id="" class="m-left-5 p-bottom-10 m-top-5 clearfix" style="font-weight: bold;    font-size: 16px;    border-bottom: 1px solid #ccc;">Project Screen Videos</p>
              <?php $cat_keyword = 'projects'; $sub_cat_keyword = 'index'; ?>
              <?php $this->help_videos->get_help_videos($cat_keyword,$sub_cat_keyword); ?>
            </div>
          </div>
        </div>
      </div> 
    </div>
  </div>
</div>

<script type="text/javascript">
$('.mod_video_toggle').click(function(){
 $('.group_video_frame').attr('src','');
  var video_details_arr = $(this).find('.video_details').text().split('`');
  setTimeout(function(){
    $('.group_video_frame').attr('src',video_details_arr[1]);
  },2000);
  $('li.now_playing_tab_btn a').show().trigger('click');
});

$('.open_help_vids_mpd').click(function(){
  $('li.help_videos_tab_btn a').trigger('click');
    $('li.now_playing_tab_btn a').hide();
});
</script>
<!-- _________________________________ HELP VIDEO SETUP _________________________________ -->







<?php $this->bulletin_board->list_latest_post(); ?>