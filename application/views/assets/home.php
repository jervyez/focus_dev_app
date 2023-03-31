<!DOCTYPE>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/main.css">
		<link href="css/font-awesome.min.css" rel="stylesheet">
        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        
        <link href="http://c3js.org/css/c3-e07e76d4.css" rel="stylesheet" type="text/css">
		<script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
		<script src="http://c3js.org/js/c3.min-be9bea56.js"></script>
		
		<script src="http://maps.googleapis.com/maps/api/js?v=3&amp;sensor=false"></script>
    	<script type="text/javascript" src="js/maps/data.js"></script>
    	<script type="text/javascript" src="js/maps/markerclusterer_packed.js"></script>
    	
    	
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <div class="navbar navbar-inverse navbar-fixed-top top-nav" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand logo" href="#" ><em><i class="fa fa-tachometer"></i> Sojourn</em></a>
        </div>
        
        <div class="navbar-collapse collapse">
        	<ul class="nav navbar-nav">
            <li class="dropdown">
              <a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-coffee"></i> Admin Controls <b class="caret"></b></a>
              <ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
                <li><a role="menuitem" tabindex="-1" href="#">Action</a></li>
                <li><a role="menuitem" tabindex="-1" href="#">Another action</a></li>
                <li><a role="menuitem" tabindex="-1" href="#">Something else here</a></li>
                <li class="divider"> </li>
                <li><a role="menuitem" tabindex="-1" href="#">Separated link</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" id="drop2" role="button" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-phone"></i> Contact Person <b class="caret"></b></a>
              <ul class="dropdown-menu" role="menu" aria-labelledby="drop2">
                <li><a role="menuitem" tabindex="-1" href="#">Action</a></li>
                <li><a role="menuitem" tabindex="-1" href="#">Another action</a></li>
                <li><a role="menuitem" tabindex="-1" href="#">Something else here</a></li>
                <li class="divider"> </li>
                <li><a role="menuitem" tabindex="-1" href="#">Separated link</a></li>
              </ul>
            </li>
          </ul>
          
          <ul class="nav navbar-nav navbar-right">
            <li id="fat-menu" class="dropdown">
              <a href="#" id="drop3" role="button" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-magic"></i> Virtual Tour <b class="caret"></b></a>
              <ul class="dropdown-menu" role="menu" aria-labelledby="drop3">
                <li><a role="menuitem" tabindex="-1" href="#">Lets start now!</a></li>
                <li><a role="menuitem" tabindex="-1" href="#">Another action</a></li>
                <li><a role="menuitem" tabindex="-1" href="#">Something else here</a></li>
                <li class="divider"> </li>
                <li><a role="menuitem" tabindex="-1" href="#">Separated link</a></li>
              </ul>
            </li>
            
            <li id="fat-menu" class="dropdown">
              <a href="#" id="drop3" role="button" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> Jane Smith <b class="caret"></b></a>
              <ul class="dropdown-menu" role="menu" aria-labelledby="drop3">
                <li role="presentation"><a role="menuitem" tabindex="-1" href="http://twitter.com/fat">Action</a></li>
                <li role="presentation"><a role="menuitem" tabindex="-1" href="http://twitter.com/fat">Another action</a></li>
                <li role="presentation"><a role="menuitem" tabindex="-1" href="http://twitter.com/fat">Something else here</a></li>
                <li role="presentation" class="divider"></li>
                <li role="presentation"><a role="menuitem" tabindex="-1" href="http://twitter.com/fat"><i class="fa fa-sign-out"></i> Sign Out</a></li>
              </ul>
            </li>
          </ul>
          
          
          
        </div><!--/.navbar-collapse -->
      </div>
    </div>
    
			
      <div class="container-fluid head-control">
			<div class="container-fluid">				
				<div class="row">

					<div class="col-md-6 col-sm-4 col-xs-12 pull-left">
						<header class="page-header">
							<h3>Dashboard
							<br>
							<small>Monday, May 26, 2014</small></h3>
						</header>
					</div>

					<div class="page-nav-options col-md-6 col-sm-8 col-xs-12 pull-right hidden-xs">
							<ul class="nav nav-tabs navbar-right">
								<li class="active">
									<a href="#"><i class="icon-home"></i>Home</a>
								</li>
								<li>
									<a href="#" class="btn-small">Clients</a>
								</li>
								<li>
									<a href="#" class="btn-small">Projects</a>
								</li>
								<li>
									<a href="#" class="btn-small">WIP</a>
								</li>
							</ul>
					</div>

				</div>
			</div>
		</div>
  
	 <!-- /container-fluid --> 

    <div class="container-fluid">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-sm-12 col-md-1 col-lg-1" id="leftCol" >
        	<div class="nav nav-stacked affix-top" id="sidebar">
        		<div class="side-tools clearfix"  id="my-other-element">
<ul>
							<li>
								<a href="#"> <i class="fa fa-tachometer fa-3x"></i> <label class="control-label">Dashboard</label> </a>
							</li>
							<li>
								<a href="#"> <i class="fa fa-calendar fa-3x"></i>  <label class="control-label">Clients</label></a>
							</li>
							<li>
								<a href="#"> <i class="fa fa-map-marker fa-3x"></i>  <label class="control-label">Projects</label></a>
							</li>
							<li>
								<a href="#"> <i class="fa fa-tasks fa-3x"></i>  <label class="control-label">WIP</label></a>
							</li>
							<li>
								<a href="#"> <i class="fa fa-list-alt fa-3x"></i> <label class="control-label">Invoice</label></a>
							</li>
							<li>
								<a href="#"> <i class="fa fa-bar-chart-o fa-3x"></i> <label class="control-label">Reports</label></a>
							</li>
							<li>
								<a href="#" class=""> <i class="fa fa-cogs fa-3x"></i>  <label class="control-label">Settings</label></a>
							</li>
						</ul>
          </div>
          </div>
        </div>
        <div class="section col-sm-12 col-md-11 col-lg-11">
          <div class="container-fluid">
          	
          	<div class="row">
	<div class="col-xs-12">
	<div class="border-less-box alert alert-success fade in">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <h4>Oh snap! You got an error!</h4>
      <p>Change this and that and try again. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum.</p>
      <p>
        <button type="button" class="btn btn-success" id="loading-example-btn"  data-loading-text="Loading..." >Take this action</button>
        <button type="button" class="btn btn-default">Or do this</button>
      </p>
    </div>
    </div>
</div>

<div class="row">
							<div class="col-md-4">
								<div class="box">
									<div class="box-head pad-5"><label><i class="fa fa-bookmark"></i> Shortcuts</label></div>
									<div class="box-area pad-5 no-pad-b">
										<div class="btn-group-box">
											<button class="btn">
												<i class="fa fa-tachometer fa-2x"></i>
												<br>
												Dashboard
											</button>
											<button class="btn">
												<i class="fa fa-user fa-2x"></i>
												<br>
												Account
											</button>
											<button class="btn">
												<i class="fa fa-search fa-2x"></i>
												<br>
												Search
											</button>
											<button class="btn">
												<i class="fa fa-list-alt fa-2x"></i>
												<br>
												Reports
											</button>
											<button class="btn">
												<i class="fa fa-bar-chart-o fa-2x"></i>
												<br>
												Charts
											</button>
											<button class="btn">
												<i class="fa fa-list-alt fa-2x"></i>
												<br>
												Reports
											</button>
											<button class="btn start-tour">
												<i class="fa fa-magic fa-2x"></i>
												<br>
												Start Tour
											</button>
											<button class="btn" data-toggle="modal" data-target="#myModal">
												<i class="fa fa-caret-square-o-up fa-2x"></i>
												<br>
												Pop Modal
											</button>											
										</div>
									</div>
								</div>								
							</div>
							
							<div class="col-md-4">
								<div class="box">
									<div class="box-head pad-5"><label><i class="fa fa-tags fa-lg"></i> Title</label></div>
									<div class="box-area pattern-sandstone pad-5">
										<p>
											Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
										</p>
									</div>
								</div>								
							</div>
							
							<div class="col-md-4">
								<div class="left-section-box">
									<div class="box-head pad-10"><label>Welcome</label></div>
									<div class="box-area pad-10">
										<p>
											Welcome message goes here. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
										</p>
									</div>
								</div>								
							</div>							
							
						</div>
						
						<div class="row">
							
							<div class="col-md-6">
								<div class="box">
									<div class="box-head pad-5"><label><i class="fa fa-table fa-lg"></i> Chart</label></div>
									<div class="box-area pad-5">
										<div id="chart1"></div>
									</div>
								</div>								
							</div>	
							
							<div class="col-md-6">
								<div class="box" id="my-element">
									<div class="box-head pad-5"><label><i class="fa fa-map-marker fa-lg"></i> Maps</label></div>
									<div class="box-area">
										<div id="map-container"><div id="map"></div></div>
									</div>
								</div>								
							</div>	
							
							
							
							<!-- <div class="col-md-4">
								<div class="box">
									<div class="box-area pattern-sandstone pad-5 ">
										<div class="numbers-control-text">
											<p class="value">
												100
											</p>
											<p class="text">
												Total Project Revenue
											</p>
											<i class="fa fa-money  "> </i>
										</div>
									</div>
								</div>							
							</div> -->		
						</div>
						
						<div class="row">
							
							
							<div class="col-md-4">
								<div class="box">
									<div class="box-head pad-10"><label><i class="fa fa-th-list fa-lg"></i> Lists</label></div>
									<div class="box-area pattern-sandstone pad-5">
										
										
										<div class="box-content box-list collapse in">
									<ul>
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
									<div class="box-collapse">
										<a style="cursor: pointer;" data-toggle="collapse" data-target=".more-list">
											Show More
										</a>
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
							
							<div class="col-md-4">
								<div class="box">
									<div class="box-area pattern-sandstone">
										<div class="box-content box-table">
											<table id="sample-table" class="table table-hover table-bordered tablesorter">
												<thead>
													<tr>
														<th class="header">Version</th>
														<th class="header">Browser</th>
														<th class="td-actions header"></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>7.0</td>
														<td>Internet
														Explorer</td>
														<td class="td-actions"><a href="javascript:;" class="btn btn-small btn-info"> <i class="btn-icon-only icon-ok"></i> </a><a href="javascript:;" class="btn btn-small btn-danger"> <i class="btn-icon-only icon-remove"></i> </a></td>
													</tr>
													<tr>
														<td>4.0</td>
														<td>Firefox</td>
														<td class="td-actions"><a href="javascript:;" class="btn btn-small btn-info"> <i class="btn-icon-only icon-ok"></i> </a><a href="javascript:;" class="btn btn-small btn-danger"> <i class="btn-icon-only icon-remove"></i> </a></td>
													</tr>
													<tr>
														<td>Latest</td>
														<td>Safari</td>
														<td class="td-actions"><a href="javascript:;" class="btn btn-small btn-info"> <i class="btn-icon-only icon-ok"></i> </a><a href="javascript:;" class="btn btn-small btn-danger"> <i class="btn-icon-only icon-remove"></i> </a></td>
													</tr>
													<tr>
														<td>Latest</td>
														<td>Chrome</td>
														<td class="td-actions"><a href="javascript:;" class="btn btn-small btn-info"> <i class="btn-icon-only icon-ok"></i> </a><a href="javascript:;" class="btn btn-small btn-danger"> <i class="btn-icon-only icon-remove"></i> </a></td>
													</tr>
													<tr>
														<td>11</td>
														<td> Opera</td>
														<td class="td-actions"><a href="javascript:;" class="btn btn-small btn-info"> <i class="btn-icon-only icon-ok"></i> </a><a href="javascript:;" class="btn btn-small btn-danger"> <i class="btn-icon-only icon-remove"></i> </a></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>								
							</div>	
							
							<div class="col-md-4">
								<div class="box">
									<div class="box-head pad-5"><label><i class="fa fa-bar-chart-o fa-lg"></i> Donut Chart</label></div>
									<div class="box-area pattern-sandstone pad-5">
										<div id="chart2"></div>
									</div>
								</div>								
							</div>						
							
						</div>
					</div>	
					</div>	
				</div>	
				
				
					
<hr/>
      
      
      
      
  	<!-- sample modal content -->
  <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Modal Heading</h4>
        </div>
        <div class="modal-body">
          <h4>Text in a modal</h4>
          <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>

          <h4>Popover in a modal</h4>
          <p>This ( <a href="#" data-placement="top" class="popover-test" title="A Title" data-content="And here's some amazing content. It's very engaging. right?">?</a> ) should trigger a popover on click.</p>

          <h4>Tooltips in a modal</h4>
          <p><a href="#" class="tooltip-test" title="Tooltip">This link</a> and <a href="#" class="tooltip-test" title="Tooltip">that link</a> should have tooltips on hover.</p>

          <hr>

          <h4>Overflowing text to show scroll behavior</h4>
          <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
          <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
          <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
          <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
          <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
          <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
          <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
          <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
          <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>

      </div>
      <!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
  	

      <footer>
        <p class="text-center">&copy; Company 2014</p>
      </footer>
    </div> <!-- /container -->        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>

        <script src="js/vendor/bootstrap.min.js"></script>
        
        <script src="js/bootstrap-tour.min.js"></script>
		<link href="css/bootstrap-tour.min.css" rel="stylesheet">

        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>

        <!-- Google Analytics: change UA-xxxxx-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X');ga('send','pageview');
        </script>
        
        <script type="text/javascript">
      // function initialize(){
//         
//         
      // }
      // google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    </body>
</html>
