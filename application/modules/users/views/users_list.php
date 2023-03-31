<?php //date_default_timezone_set("Australia/Perth");  // date is set to perth and important setting for diff timezone acounts ?>
<?php $this->session = \Config\Services::session(); ?>
<?php $leave_requests = $this->session->get('leave_requests'); ?>
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
					<li>
						<a href="<?php echo site_url(); ?>/users/company_matrix"><i class="fa fa-university"></i> Org. Chart</a>
					</li>


					<?php if($this->session->get('users') > 0 || $this->session->get('is_admin') ==  1): ?>
						<li>
							<a href="<?php echo site_url(); ?>/users/account/<?php echo $this->session->get('user_id'); ?>"><i class="fa fa-cog"></i> My Account</a>
						</li>
					<?php endif; ?>
					<?php if($this->session->get('is_admin') == 1 ): ?>
						<li>
							<a href="<?php echo site_url(); ?>users/user_logs">User Logs</a>
						</li>
					<?php endif; ?>
						<li>
							<a href="<?php echo site_url(); ?>/users/leave_details/<?php echo $this->session->get('user_id'); ?>">My Leave Requests</a>
						</li>
					<?php if ($leave_requests == 1): ?>
						<li>
							<a href="<?php echo site_url(); ?>/users/leave_approvals/<?php echo $this->session->get('user_id'); ?>">Leave Approvals</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>

		</div>
	</div>
</div>
<!-- title bar -->


												<style type="text/css">
													.gray{
														background: #555 !important;
														border-color: #000 !important;
													}

													.gray .box-widg-head{
														background: #9e9e9e !important;
													}

													.wid-type-g{
														background: #4caf50; border: 1px solid #4caf50;													
														color: #fff !important;
													}

													.wid-type-g .box-widg-head{
														background: #61d465 !important;
														color: #fff !important;
													}
												</style>


<div class="container-fluid">
	<!-- Example row of columns -->
	<div class="row">
		<?php echo view('assets/sidebar'); ?>
		<div class="section col-sm-12 col-md-11 col-lg-11">
			<div class="container-fluid basic">



				<div class="row">
					
					<div class="col-md-12">
						<div class="left-section-box">
							<div class="box-head pad-10 clearfix">						

								<div class="col-md-6">
									<label><?php echo $screen; ?></label>

<span class="fa fa-film pointer play_details_vids open_help_vids_mpd" data-toggle="modal" data-target="#help_video_group"> </span>
									<!-- <span> (<a href="#" data-placement="right" class="popover-test" title="" data-content="Hello there mate! Welcome to the users screen." data-original-title="Welcome">?</a>)</span> -->
									<p class="hide"><a href="#" class="tooltip-test" title="Tooltip">This link</a> and <a href="#" class="tooltip-test" title="Tooltip">that link</a> should have tooltips on hover.</p>

									<p style="font-size: 14px; font-weight: bold;"><span style="color: blue;"><?php echo $overall_total_users->overall; ?> Overall Users</span> | <span style="color: green;"><?php echo $local_total_users->local; ?> Local (Australian) Users</span> | <span style="color: red;"><?php echo $offshore_total_users->offshore; ?> Offshore (Manila) Users</span></p>

								</div>

								<div class="col-md-6">

									<?php if($this->session->get('is_admin') == 1 || $this->session->get('users') > 1): ?><a href="./users/add" class="btn btn-primary pull-right"><i class="fa fa-briefcase"></i>&nbsp; Add New</a><?php endif; ?>
										
								</div>

							</div>
							<div class="box-area clearfix">


								<?php if(@$this->session->getFlashdata('new_focus_company')): ?>
									<div class="no-pad-t m-bottom-10 pad-left-10">
										<div class="border-less-box alert alert-success fade in">
											<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
											<h4>Congratulations!</h4>
											<?php echo $this->session->getFlashdata('new_focus_company');?>
										</div>
									</div>
								<?php endif; ?>
								
									<div class="row clearfix pad-left-15  pad-right-15 pad-bottom-10">

										<?php $focus_comp_group_checker = ''; $counter = 0; ?>	
										<?php $focus_comp_group = ''; ?>

										
										<?php $wid_type = ''; ?>

										
										<?php foreach($users as $key => $user): ?>
											<?php $focus_comp_group = $user->company_name; $gray_color = '';?>






 


												<?php if($focus_comp_group_checker == ''): ?>
													<?php $focus_comp_group_checker = $user->company_name; $wid_type = 'c'; ?>
													<div class="col-md-12 col-sm-12 col-lg-12 col-xs-12"><div class="text-center pad-top-10"><h4>FSF Group Pty Ltd 

														<br><small style="font-weight: bold;">(<?php echo $total_fsf_group_users->fsf_group;?> Total | <?php echo $total_fsf_group_users_local->fsf_group;?> Local | <?php echo $total_fsf_group_users_offshore->fsf_group;?> Offshore)</small>

													</h4></div> </div>
												<?php endif; ?>



												<?php if($focus_comp_group != $focus_comp_group_checker  ): ?>
													<?php $focus_comp_group_checker = $user->company_name; $wid_type = 'b'; ?>

													<?php if($counter > 1): ?>
													<div class="clearfix"></div>
													<p>&nbsp;<br /></p>

													<div class="col-md-12 col-sm-12 col-lg-12 col-xs-12"><div class="text-center"><h4><?php 

														echo $user->company_name; 

														if( $user->company_name == 'Focus Shopfit Pty Ltd'){ echo ' <br><small style="font-weight: bold;">('.$total_focus_wa_users->focus_wa.' Total | '.$total_focus_wa_users_local->focus_wa.' Local | '.$total_focus_wa_users_offshore->focus_wa.' Offshore)</small>'; }
														if( $user->company_name == 'Focus Shopfit NSW Pty Ltd'){ echo ' <br><small style="font-weight: bold;">('.$total_focus_nsw_users->focus_nsw.' Total | '.$total_focus_nsw_users_local->focus_nsw.' Local | '.$total_focus_nsw_users_offshore->focus_nsw.' Offshore)</small>'; }
														if( $user->company_name == 'Focus Maintenance'){ echo ' <br><small style="font-weight: bold;">('.$total_focus_maintenance_users->focus_maintenance.' Total | '.$total_focus_maintenance_users_local->focus_maintenance.' Local | '.$total_focus_maintenance_users_offshore->focus_maintenance.' Offshore)</small>'; }

													?></h4></div> </div>
													<?php endif; ?>
												<?php endif; ?>

												<?php if( $user->company_name == 'Focus Shopfit NSW Pty Ltd'){ $wid_type = 'a'; } ?>
												<?php if( $user->company_name == 'Focus Maintenance'){ $wid_type = 'g'; } ?>

 																
											<?php if($user->is_third_party == 1 ){ $gray_color = 'gray';	} ?>
 

												<div class="col-md-6 col-sm-6 col-lg-4 col-xs-12 box-widget">
													<div class="box wid-type-<?php echo $wid_type.' '.$gray_color; ?>">
														<div class="widg-head box-widg-head pad-5"><?php echo $user->department_name; ?> <span class="sub-h pull-right"><?php echo $user->company_name; ?></span></div>							
														<?php if($user->user_profile_photo == ''): ?>
															<div class="box-area pad-5 text-center">
																<i class="fa <?php echo ($user->role_types == 'Administrator' ? 'fa-user-secret' : 'fa-user'); ?> pull-left fa-4x widg-icon-inside"></i>
																							

																<?php if($user->user_id == 9): ?>
																	<p>Role: Operations Manager <?php echo $this->users->showAvailableLeave($user->user_id); ?></p>
																<?php else: ?>
																	<p>Role: <?php echo $user->role_types; ?> <?php echo $this->users->showAvailableLeave($user->user_id); ?></p>
																<?php endif; ?>




																<h2><?php echo $user->user_first_name; ?></h2>
																<p><?php echo $user->user_last_name; ?></p>
																<hr class="pad-5">
																<a href="<?php echo site_url(); ?>/users/account/<?php echo $user->user_id; ?>"><p>view details</p></a>
															</div>

														<?php else: ?>


															<div class="box-area pad-5 text-left">
																<div style="float: left; overflow: hidden; height: 90px; ">
																	<img src="<?php echo site_url(); ?>/uploads/users/<?php echo $user->user_profile_photo; ?>" style="margin: 5px 5px; width: 85px;">
																</div>
																<?php if($user->app_access_type !== ""): ?>
																<span class ="pull-right" style = "position:relative">App Version: <?php echo $user->app_version; ?></span>	
																<?php endif; ?>	
																						

																<?php if($user->user_id == 9): ?>
																	<p>Role: Operations Manager <?php echo $this->users->showAvailableLeave($user->user_id); ?></p>
																<?php else: ?>
																	<p>Role: <?php echo $user->role_types; ?> <?php echo $this->users->showAvailableLeave($user->user_id); ?></p>
																<?php endif; ?>
																

																<h2><?php echo $user->user_first_name; ?></h2>
																<p><?php echo $user->user_last_name; ?></p>
																<hr class="pad-5">
																<a class="text-center" href="<?php echo site_url(); ?>/users/account/<?php echo $user->user_id; ?>"><p>view details</p></a>
															</div>

														<?php endif; ?>


													</div>
												</div>

												<?php $counter++; ?>


					

										<?php endforeach; ?>						
										

									</div>
					
							



							</div>
						</div>
					</div>					

					<!--<div class="col-md-3">
						
						<div class="box">
							<div class="box-head pad-5">
								<label><i class="fa fa-info-circle fa-lg"></i> Users Currently Logged-In</label>
								<button type = "button" class = "btn btn-primary btn-sm pull-right" id = "btn_logout_user">Log-out User</button>
							</div>
							<div class="box-area pad-10" id="login_user_list" style = "height: 200px; overflow: auto">								
							</div>
						</div>
					</div>-->
					
					<div class="col-md-3 hide">						
						<div class="box">
							<div class="box-head pad-10">
								<label><i class="fa fa-history fa-lg"></i> History</label>
							</div>
							<div class="box-area pattern-sandstone pad-5">
								<div class="box-content box-list collapse in">
									<ul>
										<li>
											<div>
												<a href="#" class="news-item-title">You added a new company</a>
												<p class="news-item-preview">May 25, 2014</p>
											</div>
										</li>
										<li>
											<div>
												<a href="#" class="news-item-title">Updated the company details and contact information for James Tiling Co.</a>
												<p class="news-item-preview">May 20, 2014</p>
											</div>
										</li>
									</ul>
									<div class="box-collapse">
										<a style="cursor: pointer;" data-toggle="collapse" data-target=".more-list"> Show More </a>
									</div>
									<ul class="more-list collapse out">
										<li>
											<div>
												<a href="#" class="news-item-title">Duis aute irure dolor in reprehenderit</a>
												<p class="news-item-preview">
													Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore.
												</p>
											</div>
										</li>
										<li>
											<div>
												<a href="#" class="news-item-title">Duis aute irure dolor in reprehenderit</a>
												<p class="news-item-preview">
													Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore.
												</p>
											</div>
										</li>
										<li>
											<div>
												<a href="#" class="news-item-title">Duis aute irure dolor in reprehenderit</a>
												<p class="news-item-preview">
													Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore.
												</p>
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





<!-- _________________________________ HELP VIDEO SETUP _________________________________ -->
<?php 
	use App\Modules\Help_videos\Controllers\Help_videos;
	$this->help_videos = new Help_videos();
?>
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
            <iframe style="width: 100%;height: 70%;background-repeat: no-repeat;background-color:#000;background-image: url('<?php echo site_url(); ?>/uploads/misc/loading_bub.gif');background-position: center;background-size: 50px;" class="group_video_frame" ></iframe>
          </div>
          <div id="help_videos" class="tab-pane fade clearfix active in">
            <div id="" class="m-10 p-bottom-10 clearfix">

<div id="" class="details_video hp_vids_tmbs clearfix" style="display:none;">
<p id="" class="m-left-5 p-bottom-10 m-top-5 clearfix" style="font-weight: bold;    font-size: 16px;    border-bottom: 1px solid #ccc;">Details Videos</p>
<?php $cat_keyword = 'users'; $sub_cat_keyword = 'index'; ?>
<?php $this->help_videos->get_help_videos($cat_keyword,$sub_cat_keyword); ?>
</div>




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

$('.play_invoice_vids').click(function(){
	$('.hp_vids_tmbs').hide();
	$('.invoice_video').show();
});

$('.play_details_vids').click(function(){
	$('.hp_vids_tmbs').hide();
	$('.details_video').show();
});

$('.play_works_vids').click(function(){
	$('.hp_vids_tmbs').hide();
	$('.works_video').show();
});

$('.play_variation_vids').click(function(){
	$('.hp_vids_tmbs').hide();
	$('.variation_video').show();
});


$('.open_help_vids_mpd').click(function(){
	$('li.help_videos_tab_btn a').trigger('click');
  	$('li.now_playing_tab_btn a').hide();
});


</script>

<?php if(  $this->session->get('is_admin') != 1  ): ?>
<?php if(  $this->session->get('user_role_id') != 5 &&  $this->session->get('user_role_id') != 6 &&  $this->session->get('user_role_id') != 4  ): ?>
	<style type="text/css">

	#vid_id_21,#vid_id_22{
		display: none;
		visibility: hidden;
	}

	</style>
<?php endif; ?>
<?php endif; ?>

<!-- _________________________________ HELP VIDEO SETUP _________________________________ -->

<?php //review_code ?>
<?php 
	use App\Modules\Bulletin_board\Controllers\Bulletin_board;
	$this->bulletin_board = new Bulletin_board();
?>
<?php //review_code ?>
<?php $this->bulletin_board->list_latest_post(); ?>
<?php echo view('assets/logout-modal'); ?>