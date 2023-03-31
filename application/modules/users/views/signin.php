<?php $this->session = \Config\Services::session(); ?>
<?php //review_code ?>
<?php 
/*
	$this->load->module('etc');
	$this->etc->remind_day_left();
	$this->etc->remind_not_recv();
	$this->etc->remind_hr_left();
*/
?>
<?php //review_code ?>
<div class="sign_in_bg" style="background-image:url('<?php echo $bg_file; ?>') !important; height: 99.9%; margin-top: 0px !important;">
    <?php /*
<script src="<?php echo base_url(); ?>js/snow-it.min.js"></script>
*/ ?>
	<script type="text/javascript">
/*	$.fn.snowit({

  // min size of snowflake
  minSize : 10,

  // max size of snowflake
  maxSize : 50,

  // the number of flakes generated
  total : 100,

  // speed of flakes when fall down
  speed: 75,

  // color of snowflake
  flakeColor : "#FFFFFF"
  
});*/
	</script>

	<?php /* if(isset($error)): ?>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-6 col-md-offset-3">
				<div class="border-less-box alert alert-danger fade in">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true"> × </button>
					<h4>Oh snap! You got an error!</h4>
					<?php echo $error; ?>
				</div>
			</div>
		</div>
	</div>
	<?php endif;*/ ?>

	
	<?php if(isset($signin_error)): ?>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-6 col-md-offset-3">
				<div class="border-less-box alert alert-danger fade in">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true"> × </button>
					<h4>Error!</h4>
					<?php echo $signin_error; ?>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

<?php //review_code ?>
<?php /* ?>
	<?php if(@$this->session->flashdata('new_pass_msg')): ?>
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-md-6 col-md-offset-3">
				<div class="border-less-box alert alert-success fade in">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true"> × </button>
					<h4>The new password is been set!</h4>
					<?php echo $this->session->flashdata('new_pass_msg');?>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
<?php */ ?>
<?php //review_code ?>

	<div class="container pad-20">
		<div class="row">
			<div class="col-xs-12 col-md-6 col-md-offset-3 sign_form" style="    background: rgba(255, 255, 255, 0.7)">
				<h1 class="text-center">Sign in</h1>
				<div class="well"  style="border: 2px solid #b5b5b5; border-radius: 8px;">
					<form class="form-horizontal" method="post" action="">					
						
						<div class="form-group">
							<label for="inputUserName" class="col-sm-3 control-label">User Name</label>
							<div class="col-sm-9">

								<?php if($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
									<div class="input-group <?= $signin_error ? 'has-error has-feedback' : ''; ?>">
								<?php else: ?>
										<div class="input-group ">
								<?php endif; ?>


								





<?php 

/*if ($validation->hasError('username')) {
    echo $validation->getError('username');
}*/ ?>
					
									<span class="input-group-addon"><i class="fa fa-user"></i></span>
									<input type="text" id="inputUserName" name="user_name" class="form-control"  value="<?= set_value('user_name') ?? null ?>" placeholder="<?= $_SERVER['REQUEST_METHOD'] === 'POST' ? $validation->getError('user_name') : 'User name';  ?>">

								</div>

							</div>
						</div>
											
						<div class="form-group">
							<label for="inputPassword3" class="col-sm-3 control-label">Password</label>
							<div class="col-sm-9">
						


								<?php if($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
									<div class="input-group <?= $signin_error ? 'has-error has-feedback' : ''; ?>">
								<?php else: ?>
										<div class="input-group ">
								<?php endif; ?>



									<span class="input-group-addon"><i class="fa fa-unlock-alt"></i></span>
							

									<input type="password" id="inputPassword" name="password" class="form-control"  value="" placeholder="<?= $_SERVER['REQUEST_METHOD'] === 'POST' ? $validation->getError('password') : 'Password';  ?>">



								</div>
							</div>
						</div>
											
						<div class="form-group">
							<label for="inputPassword3" class="col-sm-2 control-label"></label>
							<div class="col-sm-10">
								<div class="input-group <?php // if(form_error('password')){ echo 'has-error has-feedback';} ?>">
									<input type="checkbox" name="remember" id="remember" class="remember">&nbsp;
									<label for="remember" class="control-label"> Remember me</label>
								</div>
								<button style="margin-top: 5px;" type="submit" class="btn btn-primary pull-right" onclik = "sign_in()"><i class="fa fa-sign-in"></i> Sign in</button>
							</div>
						</div>	
						
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
	body {
		margin-top: 0 !important;
		padding-top: 0 !important;
	}
	
	.lis-flake {
	    -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */
    z-index: 30;
    border: 0 !important;
    
}
</style>