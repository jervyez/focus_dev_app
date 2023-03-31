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


							<div class="box-head pad-10 clearfix">
								<div class="pull-right" style="margin-top: -15px;">							 
									<div class="clearfix"></div>
								</div>
								<label><?php echo $post_detail['dn_title']; ?> &nbsp; </label>

                <div class="btn btn-primary btn-xs tooltip-enabled" data-original-title="Posted By" ><i class="fa fa-user-o"></i> <?php echo $post_detail['posted_by']; ?></div><div class="m-left-5 btn btn-info btn-xs  tooltip-enabled" data-original-title="Date Posted" ><i class="fa fa-calendar-o  "></i> <?php echo $post_detail['dn_date_posted']; ?></div><?php if($post_detail['is_bug_report'] == 1): ?><div class="m-left-5 btn btn-warning btn-xs" ><i class="fa fa-bug "></i> Bug Report</div><?php endif; ?>

								<div class="pull-right"> <a class="btn btn-danger pull-left  m-right-15" href=" <?php echo site_url(); ?>dev_notes/delete_post/<?php echo $post_detail['dn_id']; ?>"><i class="fa fa-trash" aria-hidden="true"></i> Delete</a></div>
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


              <div class="clearfix box-area pad-10 m-left-10 m-right-10 pad-top-10 box-area-po add_d_note_section" >

            <form  role="form" method="post" action="<?php echo site_url(); ?>dev_notes/update_post">

                <div class="row">

                  <div class="col-sm-6">
                    <div class="input-group ">
                      <span class="input-group-addon"><i class="fa fa-pencil-square-o  "></i></span>
                      <input type="text" class="form-control" placeholder="Title" name="notes_title" id="notes_title" maxlength="65" tabindex="2" value="<?php echo $post_detail['dn_title']; ?>"> 
                    </div>
                  </div>

 

                  <input type="hidden" name="post_id" value="<?php echo $post_detail['dn_id']; ?>">


                  <div class="col-sm-2">
                    <div class="input-group ">
                      <span class="input-group-addon"><i class="fa fa-user-plus" aria-hidden="true"></i></span>
                      <select class="form-control" id="dn_assnmt" name="dn_assnmt" tabindex="3">
                      <option value="0" disabled selected>Assignment</option>
                      <?php
                        foreach ($programmer as $prg ) {
                          echo '<option value="'.$prg->user_id.'">'.$prg->user_first_name.'</option>';
                        }
                      ?>                             
                      </select>
                      <script type="text/javascript">$('select#dn_assnmt').val('<?php echo ($this->request->getPost('dn_assnmt') ? $this->request->getPost('dn_assnmt') : $post_detail['dn_prgm_user_id'] ); ?>');</script>
                    </div>
                  </div>


                  <div class="col-sm-3">
                    <div class="input-group ">
                      <span class="input-group-addon">Commencement Date <i class="fa fa-calendar  "></i></span>
                       <input tabindex="4" type="text" data-date-format="dd/mm/yyyy" placeholder="DD/MM/YYYY" class="form-control datepicker" id="date_stamp" name="date_stamp" value="<?php echo $post_detail['dn_date_commence']; ?>"> 
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
                          <textarea class="form-control" id="project_notes" rows="15" tabindex="5" name="comments"  placeholder="Details"  style="resize: vertical; z-index: auto; position: relative; line-height: 20px; font-size: 14px; transition: none; background: transparent !important;"><?php echo  ($this->request->getPost('comments') ? $this->request->getPost('comments') : $post_detail['dn_post_details'] ); ?></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>


                  <div class="row m-top-10">
 

                  <a href="<?php echo site_url(); ?>dev_notes" class="btn btn-default pull-right" >Return</a>




                  <div class="col-sm-3">
                    <div class="input-group ">
                      <span class="input-group-addon">Completion Date <i class="fa fa-calendar  "></i></span>
                       <input tabindex="6" type="text" data-date-format="dd/mm/yyyy" placeholder="DD/MM/YYYY" class="form-control datepicker" id="dn_date_complete" name="dn_date_complete" value="<?php echo $post_detail['dn_date_complete']; ?>">
                    </div>
                  </div>


                  <div class="col-sm-2">
                    <select class="form-control" id="dn_section" name="dn_section" tabindex="7">
                      <option value="" disabled>Section</option>
                      <?php
                        foreach ($sections as $sectn ) {
                          echo '<option value="'.$sectn->dn_section_id.'">'.$sectn->dn_section_label.'</option>';
                        }
                      ?>
                    </select>

                  </div>
                  <script type="text/javascript">$('select#dn_section').val('<?php echo ($this->request->getPost('dn_section') ? $this->request->getPost('dn_section') : $post_detail['dn_section_id'] ); ?>');</script>


                  <div class="col-sm-3">
                    <div class="input-group ">
                      <span class="input-group-addon"><i class="fa fa-flag  " aria-hidden="true"></i></span>
                      <select class="form-control" id="dn_category" name="dn_category" tabindex="8">
                      <option value="" disabled selected>Category</option>
                        <option value="Urgent">Urgent</option>
                        <option value="Important">Important</option>
                        <option value="When Time Permits">When Time Permits</option>   
                        <option value="Maybe">Maybe</option>
                      </select>
                    </div>
                  </div>

                      <script type="text/javascript">$('select#dn_category').val('<?php echo ($this->request->getPost('dn_category') ? $this->request->getPost('dn_category') : $post_detail['dn_category'] ); ?>');</script>


                  <div class="col-sm-2">
                    <input type="submit" class="btn btn-info" value="Update" tabindex="9">
                  </div>




                  </div>


              </form>

                  <hr />





 <form  role="form" method="post" action="<?php echo site_url(); ?>dev_notes/post_comment">

                <div class="row">

                <p><i class="fa fa-comments-o fa-lg" aria-hidden="true"></i> <label class="h4">Comments</label></p>

                <div id="" class="clearfix">
                  
                  <?php $post_id = $post_detail['dn_id'];  echo $this->dev_notes->list_post_comments($post_id); ?>
                </div>

                
 


                <input type="hidden" name="dn_tread_id" value="<?php echo $post_detail['dn_id']; ?>">



                </div>

                <div id="" class="row  pad-5">
                  <div class="box ">
                    <div class="box-head pad-top-3 pad-left-10 pad-bottom-3">
                      <label for="" style="font-weight: normal;"><i class="fa fa-commenting"></i> &nbsp;Post Comments</label>
                    </div>

                    <div class="box-area pad-5 clearfix">
                      <div class="clearfix ">
                        <div class="">
                          <textarea class="form-control" id="dev_comments" rows="3" tabindex="1"  name="comments"  placeholder="Details"  style="resize: vertical; z-index: auto; position: relative; line-height: 20px; font-size: 14px; transition: none; background: transparent !important;"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>


                  <div class="row m-top-5">
 
 
   


                  <div class="col-sm-2">
                    <input type="submit" class="btn btn-info" value="Post Comment">
                  </div>




                  </div>


              </form>




              </div>


 


						</div>
					</div>

 
 
              <div class="col-md-3 m-top-5 hide">

                <div class="box">
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



                <div class="box">
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

      <form method="post"  action="<?php echo site_url(); ?>dev_notes/update_post">
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

$('#dev_comments').focus();

$('.add_d_note').click(function(){
  $('.add_d_note_section').show();
  $(this).hide();
});

$('.cancel_post_dn').click(function(){
  $('.add_d_note_section').hide();
  $('.add_d_note').show();
});


 $(".post_content").each(function(){

//    alert( $(this).height() );


    if( $(this).height() > 110 ){
        $(this).css( "height" , 110 );
        $(this).css(  "overflow" , 'hidden' );

         $(this).next().show();
    }


//        alert($(this).text())
    });


 $('.show_text').click(function(){ 

  var text_show = $(this).text();
  var div_height = $(this).prev()[0].scrollHeight;


  if(text_show == 'Show More'){


    $(this).prev().animate({height: div_height}, 500);
    $(this).text('Show Less');

  }else{
    $(this).text('Show More');
    $(this).prev().animate({height: "110px"}, 500);
  }



 });

</script>