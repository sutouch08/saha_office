
<!DOCTYPE html>
<html lang="th">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />

		<title><?php echo $this->title; ?></title>
		<meta name="description" content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.ico">

		<?php $this->load->view('include/header_include'); ?>

    <style>
		.ui-helper-hidden-accessible {
			display:none;
		}

		.ui-autocomplete {
    max-height: 250px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }
	</style>
	</head>
	<body class="no-skin" onload="checkError()">
		<div id="loader" style="position:absolute; padding: 15px 25px 15px 25px; background-color:#fff; opacity:0.0; box-shadow: 0px 0px 25px #CCC; top:-20px; display:none; z-index:10;">
        <center><i class="fa fa-spinner fa-5x fa-spin blue"></i></center><center>กำลังทำงาน....</center>
		</div>
		<?php if($this->session->flashdata('error')) : ?>
							<input type="hidden" id="error" value="<?php echo $this->session->flashdata('error'); ?>" />
		<?php endif; ?>
		<?php if($this->session->flashdata('success')) : ?>
							<input type="hidden" id="success" value="<?php echo $this->session->flashdata('success'); ?>" />
		<?php endif; ?>
		<!-- #section:basics/navbar.layout -->
		<div id="navbar" class="navbar navbar-default">
			<script type="text/javascript">
				var BASE_URL = '<?php echo base_url(); ?>';
			</script>
			<div class="navbar-container" id="navbar-container">
				<?php if(! isset($_GET['nomenu'])) : ?>
				<!-- #section:basics/sidebar.mobile.toggle -->
				<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
					<span class="sr-only">Toggle sidebar</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<?php endif; ?>
				<div class="navbar-header pull-left">
					<a href="<?php echo base_url(); ?>" class="navbar-brand" style="min-width:167px;">
						<small>
							<?php echo getConfig('COMPANY_NAME'); ?>
						</small>
					</a>
				</div>

					<?php $this->load->view('include/top_menu'); ?>
				<div class="navbar-buttons navbar-header pull-right" role="navigation">
					<ul class="nav ace-nav">

						<li class="salmon">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">

								<span class="user-info">
									<small>Welcome</small>
									<?php echo get_cookie('uname'); ?>
								</span>

								<i class="ace-icon fa fa-caret-down"></i>
							</a>

							<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-caret dropdown-close">

								<li>
									<a href="JavaScript:void(0)" onclick="changeUserPwd('<?php echo get_cookie('uid'); ?>')">
										<i class="ace-icon fa fa-keys"></i>
										เปลี่ยนรหัสผ่าน
									</a>
								</li>
								<li class="divider"></li>

								<li>
									<a href="<?php echo base_url(); ?>authentication/logout">
										<i class="ace-icon fa fa-power-off"></i>
										ออกจากระบบ
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</div>
				<!-- /section:basics/navbar.dropdown -->
			</div><!-- /.navbar-container -->
		</div>

		<!-- /section:basics/navbar.layout -->
		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>
			<!-- #section:basics/sidebar -->
			<div id="sidebar" class="sidebar responsive <?php echo get_cookie('sidebar_layout'); ?>" data-sidebar="true" data-sidebar-scoll="true" data-sidebar-hover="true">
				<script type="text/javascript">
					try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
				</script>
						<!--- side menu  ------>
				<?php $this->load->view("include/side_menu"); ?>

				<!-- #section:basics/sidebar.layout.minimize -->
				<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse" onclick="toggle_layout()">
					<i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
				</div>

			</div>
			<!-- /section:basics/sidebar -->
			<div class="main-content">
				<div class="main-content-inner">
					<div id="sidebar2" class="sidebar h-sidebar navbar-collapse collapse" data-sidebar="true" data-sidebar-scoll="true"
					data-sidebar-hover="true" aria-expanded="false" style="height:1px;">
      <!-- second sidebar, horizontal -->

    			</div>
					<div class="page-content">

								<!-- PAGE CONTENT BEGINS -->
