<?php use App\Modules\Dev_notes\Controllers\Dev_notes; ?>
<?php $this->dev_notes = new Dev_notes(); ?>

<!-- title bar -->
<div class="container-fluid head-control">
	<div class="container-fluid">
		<div class="row">

			<div class="col-md-6 col-sm-4 col-xs-12 pull-left">
				<header class="page-header">
					<h3><?php $datestring = "l, F d, Y"; $time = time(); //use time() for timestamp  ?>
            <?php echo $screen; ?> Screen<br><small><?php echo date($datestring, $time); #echo date("l, F d, Y"); ?></small>
          </h3>
				</header>
			</div>

			<div class="page-nav-options col-md-6 col-sm-8 col-xs-12 pull-right hidden-xs">
				<ul class="nav nav-tabs navbar-right">
					<li>
						<a href="<?php echo site_url(); ?>"><i class="fa fa-home"></i> Home</a>
					</li>
				</ul>
			</div>

		</div>
	</div>
</div>
<!-- title bar -->

<div class="container-fluid">
	<!-- Example row of columns -->
	<div class="row">				
		<?php echo view('assets/sidebar'); ?>
		<div class="section col-sm-12 col-md-11 col-lg-11">
			<div class="container-fluid">

				<div class="row">

          <?php if($this->session->get('is_admin') == 1 ): ?>

           <div class="col-md-9">

           <?php else: ?>

           <div class="col-md-12">

           <?php endif; ?>

						<div class="left-section-box clearfix">


						<div class="clearfix"></div>


							<div class="box-head pad-10 clearfix"  style="padding: 7px;">
								<div class="pull-right" style="margin-top: -15px;">							 
									<div class="clearfix"></div>
								</div>
								<label><?php echo $screen; ?></label>

								<div class="pull-right"> 
                <button class="btn btn-primary pull-left  m-right-15 add_d_note"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Add</button>    
            
 
                  <div class="pull-right   pad-left-15  clearfix box-tabs"  style="margin-bottom: -10px;">  
                    <ul id="myTab" class="nav nav-tabs pull-right">
                      <li class="active"><a  href="#development" id="development_btn" role="tab" data-toggle="tab" >Development</a></li>
                      <li class=""><a  href="#bugs" id="bugs_btn" role="tab" data-toggle="tab" >Bugs</a></li>
                      
                    </ul>
                  </div> 

    <select style="width:150px;" id="select-status-dnotes-tbl" class="select-status-dnotes-tbl form-control pull-right  ">
                  <option value="Completed">Completed</option>                  
                   <option selected value="Outstanding">Outstanding</option>
                   <option value="">View All</option>
                </select>


                </div>
							</div>

              <?php if(@$this->session->getFlashdata('error')): ?>
                <div class="no-pad-t m-bottom-10 m-left-10">
                  <div class="border-less-box alert alert-danger fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>                    
                    <?php echo $this->session->getFlashdata('error');?>
                  </div>
                </div>
              <?php endif; ?>

              <?php if(@$this->session->getFlashdata('success_post')): ?>
                <div class="no-pad-t m-bottom-10 m-left-10">
                  <div class="border-less-box alert alert-success fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>                   
                    <?php echo $this->session->getFlashdata('success_post');?>
                  </div>
                </div>
              <?php endif; ?>


              <div class="clearfix box-area pad-10 m-left-10 m-right-10 pad-top-10 box-area-po add_d_note_section" style="display:none;">

            <form  role="form" method="post" action="./dev_notes/post">

                <div class="row">

                  <div class="col-sm-6">
                    <div class="input-group ">
                      <span class="input-group-addon"><i class="fa fa-pencil-square-o  "></i></span>
                      <input required type="text" class="form-control" placeholder="Title" name="notes_title" id="notes_title" maxlength="50" tabindex="1" value=""> 
                    </div>
                  </div>




                  <div class="col-sm-2">
                    <div class="input-group ">
                      <span class="input-group-addon"><i class="fa fa-user-plus" aria-hidden="true"></i></span>
                      <select class="form-control" id="dn_assnmt" name="dn_assnmt" tabindex="2">
                      <option value="" disabled selected>Assignment</option>
                      <?php 
                      foreach ($programmer as $prg ) {
                         

                        echo '<option value="'.$prg->user_id.'">'.$prg->user_first_name.'</option>';
                      
                      }


                      ?>                             
                      </select>
                    </div>
                  </div>


                  <div class="col-sm-3">
                    <div class="input-group ">
                      <span class="input-group-addon">Commencement Date <i class="fa fa-calendar  "></i></span>
                       <input tabindex="3" type="text" data-date-format="dd/mm/yyyy" placeholder="DD/MM/YYYY" class="form-control datepicker" id="date_stamp" name="date_stamp" value="">
                    </div>
                  </div>




                </div>

                <div id="" class="row  pad-5">
                  <div class="box ">
                    <div class="box-head pad-top-3 pad-left-10 pad-bottom-3">
                      <label for="" style="font-weight: normal;"><i class="fa fa-file-word-o  "></i> &nbsp;Project Notes</label>
                    </div>

                    <div class="box-area pad-5 clearfix">
                      <div class="clearfix ">
                        <div class="">
                          <textarea required class="form-control" id="project_notes" rows="10" tabindex="4" name="comments"  placeholder="Details"  style="resize: vertical; z-index: auto; position: relative; line-height: 20px; font-size: 14px; transition: none; background: transparent !important;"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>


                  <div class="row m-top-10">

                  <input type="reset" class="btn btn-warning pull-right cancel_post_dn" value="Cancel">

                  <div class="col-sm-2">
                   <div class="input-group ">
                   <span class="input-group-addon">Is a Bug Report?</span>
                    <select required class="form-control" id="dn_bugs" name="dn_bugs"  >
                      <option  value="0" selected>No</option>
                      <option value="1"> Yes</option>                       
                    </select>
                  </div>
                </div>


                  <div class="col-sm-2">
                    <select required class="form-control" id="dn_section" name="dn_section" tabindex="5">
                      <option  value="" disabled selected>Section</option>
                      <?php
                        foreach ($sections as $sectn ) {
                          echo '<option value="'.$sectn->dn_section_id.'">'.$sectn->dn_section_label.'</option>';
                        }
                      ?>                             
                    </select>
                  </div>


                  <div class="col-sm-3">
                    <div class="input-group ">
                      <span class="input-group-addon"><i class="fa fa-flag  " aria-hidden="true"></i></span>
                      <select required class="form-control" id="dn_category" name="dn_category" tabindex="6">
                      <option value="" disabled selected>Category</option>
                        <option value="Urgent">Urgent</option>
                        <option value="Important">Important</option>
                        <option value="When Time Permits">When Time Permits</option>   
                        <option value="Maybe">Maybe</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-2">
                    <input type="submit" class="btn btn-success" value="Save" tabindex="7">
                  </div>




                  </div>


              </form>

                  <hr />



              </div>


 

<div id="myTabContent" class="tab-content">


							<div class="clearfix box-area pad-10 m-left-5 m-right-5 pad-top-10 box-area-po tab-pane active" id="development">
                

                <table id="dataTable_development" class="table table-striped table-bordered dataTable no-footer" cellspacing="0" width="100%">
                  <thead> <tr> <th style="display:none;">ID</th> <th>Title</th> <th>Category</th> <th>Status</th> <th>Date Posted</th> <th>Assignment</th> <th>Section</th>  <th>unix_date</th> </tr> </thead> 
                   
                  <tbody>
                    

                    <?php  $this->dev_notes->list_post(); ?>

                  </tbody>
                </table>

              </div>


              <div class="clearfix box-area pad-10 m-left-5 m-right-5 pad-top-10 box-area-po tab-pane" id="bugs">




                <table id="dataTable_noCustom_dnotes_bugs" class="table table-striped table-bordered dataTable no-footer" cellspacing="0" width="100%">
                  <thead> <tr> <th style="display:none;">ID</th> <th>Title</th> <th>Category</th> <th>Status</th> <th>Date Posted</th> <th>Assignment</th> <th>Section</th> <th>unix_date</th></tr> </thead> 
                   
                  <tbody>
                    

                    <?php  $this->dev_notes->list_post(1); ?>

                  </tbody>
                </table>



              </div>


              <div class="added_drop hide">
                <div class="pull-right">
                  <select class="form-control m-left-10 sort_table_data" style="height: 30px;" onchange="setSortDev(this.value)" >
                    <option value="0" style="display:none">Sort Date Posted</option>
                    <option value="1">Date Posted Asc</option>
                    <option value="2">Date Posted Desc</option>
                  </select>
                </div>
              </div>


              <script type="text/javascript">

               setTimeout(function(){ 
                var addedSelect = $('.added_drop').html();

              $('#development #dataTable_development_filter').prepend(addedSelect);
            
              },1000);
             </script>



							</div>
						</div>
					</div>

 
 
          <div class="col-md-3 m-top-5">

          <form class="form-horizontal" role="form" method="post" action="<?php echo site_url(); ?>dev_notes/add_section">
              <div class="">
                <div class="box">
                  <div class="box-head pad-5">
                    <label><i class="fa fa-list-ul fa-lg" aria-hidden="true"></i> Sections</label> 
                    <div class="edit_section btn btn-xs pull-right btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</div>
                    <div class="cancel_edit_section btn btn-xs pull-right btn-info" style="display:none;">Cancel</div>
                  </div>

                  <div class="box-area clearfix pad-5">
                    

<div class="box-content box-list collapse in section_items">
                      <ul>
                        

                      <?php
                        foreach ($sections as $sectn ) {
                          echo '<li><div id="" class="m-top-5 m-bottom-5"><span> &bull; </span> <span class="section_labels" >'.$sectn->dn_section_label.'</span> ';
                          echo ' <a  style="display:none;" href="'.site_url().'dev_notes/delete_section/'.$sectn->dn_section_id.'" class="edit_tools_section btn btn-xs pull-right btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                          echo ' <div  style="display:none;" id="save_edit_'.$sectn->dn_section_id.'" class="btn btn-info pull-right m-right-5 btn-xs edit_tools_section save_edit_tools_section"><i class="fa fa-floppy-o"></i></div> ';
                          echo ' <input  type="text" class="form-control input-sm input_section_update" value="'.$sectn->dn_section_label.'" placeholder="'.$sectn->dn_section_label.'" id="input_edit_'.$sectn->dn_section_id.'" style=" display:none;   width: 70%;  ">  ';
                          echo ' </div></li>';
                        }
                      ?> 
 

                      </ul>

                    </div>



                    <div class="clearfix m-top-10 m-bottom-10">                    
                      <div class="col-sm-9">
                        <input type="text" class="form-control upper_c_each_word" value="" placeholder="Section Name" name="dn_n_section" id="dn_n_section">
                      </div> 
                      <div class="col-sm-3">
                        <button type="submit" name="submit" class="btn btn-success pull-right m-right-5"><i class="fa fa-floppy-o"></i> Submit</button>           
                      </div></div>


<script type="text/javascript">
  $('.edit_section').click(function(){
    $('.edit_tools_section').show();
    $('.cancel_edit_section').show();
    $(this).hide();

    $('input.input_section_update').show();

    $("div.section_items").find('span').each(function( index ) {
     $(this).hide();
  });


  });


  $('.cancel_edit_section').click(function(){
    $('.edit_tools_section').hide();
    $('.edit_section').show();
    $('input.input_section_update').hide();
    $(this).hide();
    $("div.section_items").find('span').each(function( index ) {
       $(this).show();
    });

  });


  $('.save_edit_tools_section').click(function(){


    $('#loading_modal').modal({"backdrop": "static", "show" : true} );


    $('.edit_tools_section').hide();
    $('.edit_section').show();
    $('input.input_section_update').hide();
      $('.cancel_edit_section').hide();
    $("div.section_items").find('span').each(function( index ) {
       $(this).show();
    });

    var org_text = $(this).prev().prev().text();
     
    var section_label = $(this).next().val();
    $(this).prev().prev().text(section_label);

    var sec_data = $(this).attr('id').split('_');
 

    if( org_text != section_label ){
      var data = section_label+'|'+sec_data[2];
     ajax_data(data,'dev_notes/update_section','');
    } 

     setTimeout(function(){ 
      $('#loading_modal').modal('hide');
    },1000);



/*
    dynamic_value_ajax(data,'update_name_company');


    $("#save_company_name").click(function(){
      var comp_id = $('#company_id_data').val();
      var comp_name = $('#company_name_data').val();
      var data = comp_id+'|'+comp_name;
      if(comp_name!=''){
        dynamic_value_ajax(data,'update_name_company');
      }
    });**/





  });
</script>


                  </div>
                </div>    
              </form>




                <div class="box hide">
                  <div class="box-head pad-5">
                    <label><i class="fa fa-history fa-lg"></i> Development Status</label>
                  </div>
                  <div class="box-area pattern-sandstone pad-10">

                    <div class="box-content box-list collapse in">
                      <ul>
                        <li>
                          <div><a href="#" class="news-item-title">Build a new Module</a><p>Status: <strong>Completed</strong></p><p class="news-item-preview">May 25, 2014</p></div>
                        </li>
                        <li>
                          <div><a href="#" class="news-item-title">Bugs found please check!</a><p class="news-item-preview">May 20, 2014</p></div>
                        </li>
                      </ul>
                      <div class="box-collapse m-10">
                        <a style="cursor: pointer;" data-toggle="collapse" data-target=".more-list"> More Details</a>
                      </div>
                      <ul class="more-list collapse out m-top-10">
                        <li>
                          <div><a href="#" class="news-item-title">Duis aute irure dolor in reprehenderit</a><p class="news-item-preview">Lorem ipsum dolor si labore et dolore.</p></div>
                        </li>
                        <li>
                          <div><a href="#" class="news-item-title">Duis aute irure dolor in reprehenderit</a><p class="news-item-preview">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore.</p></div>
                        </li>
                        <li>
                          <div><a href="#" class="news-item-title">Duis aute irure dolor in reprehenderit</a><p class="news-item-preview">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore.</p></div>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>





                <div class="box hide">
                  <div class="box-head pad-5">
                   <label>Programmer Status</label>
                  </div>

                  <div class="box-area clearfix pad-5">
                    <div class="box-content box-list collapse in">
                      <ul>
                        <li>
                          <div>
                            <p class="news-item-title"><strong>Jervy</strong></p>
                            <p>Assigned: <strong>10</strong><br />Completed: <strong>3</strong><br />Outstanding: <strong>7</strong></p>
                          </div>
                        </li>
                        <li>
                          <div>
                            <p class="news-item-title"><strong>Mark</strong></p>
                            <p>Assigned: <strong>7</strong><br />Completed: <strong>5</strong><br />Outstanding: <strong>2</strong></p>
                          </div>
                        </li>
                        <li>
                          <div>
                            <p class="news-item-title"><strong>Mike</strong></p>
                            <p>Assigned: <strong>3</strong><br />Completed: <strong>1</strong><br />Outstanding: <strong>2</strong></p>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>




              </div>    
             
  
 
					
				</div>				
			</div>
		</div>
	</div>

</div>

<?php echo view('assets/logout-modal'); ?>



<!-- Modal -->
<div class="modal fade" id="edit_post" tabindex="-1" role="dialog" aria-labelledby="edit_post" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="post"  action="<?php echo site_url(); ?>bulletin_board/update_post">
      <div class="modal-header">

      	<div class="pull-right">            		
      		<input class="check-swtich set_urgent_edit" type="checkbox" name="set_urgent_edit" id="set_urgent_edit" data-on-text="Yes" data-off-text="No" data-label-text="Is it urgent?">
      	</div>
        <h4 class="modal-title" id="myModalLabel">Edit Post</h4>
      </div>
      	<div class="modal-body pad-10">
      		<div class="container-fluid">
      			<div class="row">

      				<div class="col-sm-12">
      					<div class="input-group m-bottom-10">
      						<span class="input-group-addon" id=""><strong>Title</strong></span>
      						<input type="text" placeholder="Post Title" class="form-control" id="post_title" name="post_title" value="">
      					</div>

      					<div class="box m-top-15 ">
      						<div class="box-head pad-5">
      							<label for="email_msg_no_insurance">Content</label>
      						</div>

      						<div class="box-area pad-5 clearfix ">
      							<div class="clearfix ">
      								<div class="">
      									<textarea class="form-control" id="post_content" name="post_content" rows="10"></textarea>												
      								</div>
      							</div>
      						</div>
      					</div>

      					<input type="hidden" name="expiry_date" id="expiry_date">
      					<input type="hidden" name="post_id" id="post_id">
      					<input type="hidden" name="is_urgent" id="is_urgent">

      				</div>

      			</div>
      		</div>
      	</div>
      	<div class="modal-footer">
      		<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      		<button type="submit" class="btn btn-success">Save Changes</button>
      	</div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">

$('.add_d_note').click(function(){
  $('.add_d_note_section').show();
  $('input#notes_title').focus();
  $(this).hide();
});

$('.cancel_post_dn').click(function(){
  $('.add_d_note_section').hide();
  $('.add_d_note').show();
});



  

</script>