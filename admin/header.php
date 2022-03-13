<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';

// Check if the user is logged in or not
if(!isset($_SESSION['user'])) {
	header('location: login.php');
	exit;
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_currency WHERE default_currency=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	define('ADMIN_CURRENCY_CODE',$row['currency_code']);
	define('ADMIN_CURRENCY_SYMBOL',$row['currency_symbol']);
	define('ADMIN_CURRENCY_POSITION',$row['currency_position']);
	define('ADMIN_CURRENCY_VALUE',$row['currency_value_per_usd']);
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Admin Panel</title>

	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/ionicons.min.css">

	<link rel="stylesheet" href="../public_files/css/bootstrap-datetimepicker.css"></link>
	<link rel="stylesheet" href="../public_files/css/bootstrap-datetimepicker.min.css"></link>
	
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/datepicker3.css">

	
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/all.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/select2.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/dataTables.bootstrap.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/jquery.fancybox.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/AdminLTE.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/_all-skins.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/on-off-switch.css"/>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
	<link rel="stylesheet" href="../public_files/bootstrap-tagsinput-latest/src/bootstrap-tagsinput.css">
	
	<script src="<?php echo BASE_URL; ?>js/jquery-2.2.4.min.js"></script>
	<script src="<?php echo BASE_URL; ?>js/bootstrap.min.js"></script>
	<script src="<?php echo BASE_URL; ?>js/jquery.dataTables.min.js"></script>
	<script src="<?php echo BASE_URL; ?>js/dataTables.bootstrap.min.js"></script>
	<script src="<?php echo BASE_URL; ?>js/select2.full.min.js"></script>
	<script src="<?php echo BASE_URL; ?>js/jquery.inputmask.js"></script>
	<script src="<?php echo BASE_URL; ?>js/jquery.inputmask.date.extensions.js"></script>
	<script src="<?php echo BASE_URL; ?>js/jquery.inputmask.extensions.js"></script>
	<script src="<?php echo BASE_URL; ?>js/moment.min.js"></script>
	<script src="<?php echo BASE_URL; ?>js/bootstrap-datepicker.js"></script>
	<script src="<?php echo BASE_URL; ?>js/icheck.min.js"></script>
	<script src="<?php echo BASE_URL; ?>js/fastclick.js"></script>
	<script src="<?php echo BASE_URL; ?>js/jquery.sparkline.min.js"></script>
	<script src="<?php echo BASE_URL; ?>js/jquery.slimscroll.min.js"></script>
	<script src="<?php echo BASE_URL; ?>js/jquery.fancybox.pack.js"></script>
	<script src="<?php echo BASE_URL; ?>js/app.min.js"></script>
	<script src="<?php echo BASE_URL; ?>js/jscolor.js"></script>
	<script src="<?php echo BASE_URL; ?>js/on-off-switch.js"></script>
    <script src="<?php echo BASE_URL; ?>js/on-off-switch-onload.js"></script>
    <script src="<?php echo BASE_URL; ?>js/clipboard.min.js"></script>
	<script src="<?php echo BASE_URL; ?>js/demo.js"></script>
	<!-- <script src="../public_files/ckeditor/ckeditor.js"></script> -->
	<script src="../public_files/bootstrap-tagsinput-latest/src/bootstrap-tagsinput.js"></script>
	<script src="../public_files/js/bootstrap-datetimepicker.min.js"></script>
	
	<script src="https://cdn.ckeditor.com/4.16.0/full/ckeditor.js"></script>

	
	
</head>

<?php 

function check_customer_name($pdo,$user_id){
	$check_customer_name = $pdo->prepare('SELECT * from tbl_customer where cust_id = ?');
	$check_customer_name->execute(array($user_id));
	$get_customer_name = $check_customer_name->fetch(PDO::FETCH_ASSOC);

	return $get_customer_name ;
}

?>
<body class="hold-transition fixed skin-blue sidebar-mini">

	<div class="wrapper">
		
		<header class="main-header">

			<a href="index.php" class="logo">
				<span class="logo-lg">Ecommerce</span>
			</a>

			<nav class="navbar navbar-static-top">
				
				<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">Toggle navigation</span>
				</a>

				<span style="float:left;line-height:50px;color:#fff;padding-left:15px;font-size:18px;">Admin Panel</span>

				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<li>
							<a href="../" target="_blank">Visit Website</a>
						</li>
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="../public_files/uploads/<?php echo $_SESSION['user']['photo']; ?>" class="user-image" alt="User Image">
								<span class="hidden-xs"><?php echo $_SESSION['user']['full_name']; ?></span>
							</a>
							<ul class="dropdown-menu">
								<li class="user-footer">
									<div>
										<a href="profile-edit.php" class="btn btn-default btn-flat">Edit Profile</a>
									</div>
									<div>
										<a href="logout.php" class="btn btn-default btn-flat">Log out</a>
									</div>
								</li>
							</ul>
						</li>
					</ul>
				</div>

			</nav>
		</header>

  		<?php $cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); ?>

  		<aside class="main-sidebar">
    		<section class="sidebar">
      
      			<ul class="sidebar-menu">

			        <li class="treeview <?php if($cur_page == 'index.php') {echo 'active';} ?>">
			          <a href="index.php">
			            <i class="fa fa-hand-o-right"></i> <span>Dashboard</span>
			          </a>
			        </li>

			        <li class="treeview <?php if( ($cur_page == 'setting-logo.php') || ($cur_page == 'setting-favicon.php') || ($cur_page == 'setting-footer.php') || ($cur_page == 'setting-contact.php') || ($cur_page == 'setting-email.php') || ($cur_page == 'setting-product.php') || ($cur_page == 'setting-home.php') || ($cur_page == 'setting-banner.php') || ($cur_page == 'setting-payment.php') || ($cur_page == 'setting-currency-list.php') || ($cur_page == 'setting-head-body.php') || ($cur_page == 'setting-advertisement.php') || ($cur_page == 'setting-color.php') || ($cur_page == 'setting-commission-payout.php') ) {echo 'active';} ?>">
						<a href="#">
							<i class="fa fa-hand-o-right"></i>
							<span>Settings</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li><a href="<?php echo BASE_URL; ?>setting-logo.php"><i class="fa fa-circle-o"></i> Logo</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-favicon.php"><i class="fa fa-circle-o"></i> Favicon</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-footer.php"><i class="fa fa-circle-o"></i> Footer</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-contact.php"><i class="fa fa-circle-o"></i> Contact</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-email.php"><i class="fa fa-circle-o"></i> Email</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-product.php"><i class="fa fa-circle-o"></i> Product</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-home.php"><i class="fa fa-circle-o"></i> Home Page</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-banner.php"><i class="fa fa-circle-o"></i> Banner</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-payment.php"><i class="fa fa-circle-o"></i> Payment</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-currency-list.php"><i class="fa fa-circle-o"></i> Currency</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-head-body.php"><i class="fa fa-circle-o"></i> Head and Body</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-advertisement.php"><i class="fa fa-circle-o"></i> Advertisements</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-commission-payout.php"><i class="fa fa-circle-o"></i> Commission & Payout</a></li>
							<li><a href="<?php echo BASE_URL; ?>setting-color.php"><i class="fa fa-circle-o"></i> Color</a></li>
						</ul>
					</li>

			        <li class="treeview <?php if( ($cur_page == 'slider.php') ) {echo 'active';} ?>">
			          <a href="slider.php">
			            <i class="fa fa-hand-o-right"></i> <span>Slider</span>
			          </a>
			        </li>

			        <li class="treeview <?php if( ($cur_page == 'service.php') ) {echo 'active';} ?>">
			          <a href="service.php">
			            <i class="fa fa-hand-o-right"></i> <span>Service</span>
			          </a>
			        </li>
					
					<li class="treeview <?php if( ($cur_page == 'sale.php') ) {echo 'active';} ?>">
			          <a href="sale.php">
			            <i class="fa fa-hand-o-right"></i> <span>Sale</span>
			          </a>
			        </li>
					

			        <li class="treeview <?php if( ($cur_page == 'testimonial.php') ) {echo 'active';} ?>">
			          <a href="testimonial.php">
			            <i class="fa fa-hand-o-right"></i> <span>Testimonial</span>
			          </a>
			        </li>

			        <li class="treeview <?php if( ($cur_page == 'faq.php') ) {echo 'active';} ?>">
			          <a href="faq.php">
			            <i class="fa fa-hand-o-right"></i> <span>FAQ</span>
			          </a>
			        </li>


					<li class="treeview <?php if( ($cur_page == 'size.php') || ($cur_page == 'size-add.php') || ($cur_page == 'size-edit.php') || ($cur_page == 'color.php') || ($cur_page == 'color-add.php') || ($cur_page == 'color-edit.php') || ($cur_page == 'country.php') || ($cur_page == 'country-add.php') || ($cur_page == 'country-edit.php') || ($cur_page == 'shipping-cost.php') || ($cur_page == 'shipping-cost-edit.php') || ($cur_page == 'top-category.php') || ($cur_page == 'top-category-add.php') || ($cur_page == 'top-category-edit.php') || ($cur_page == 'mid-category.php') || ($cur_page == 'mid-category-add.php') || ($cur_page == 'mid-category-edit.php') || ($cur_page == 'end-category.php') || ($cur_page == 'end-category-add.php') || ($cur_page == 'end-category-edit.php') ) {echo 'active';} ?>">
						<a href="#">
							<i class="fa fa-hand-o-right"></i>
							<span>Categories</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							
							<!-- <li><a href="shipping-cost.php"><i class="fa fa-circle-o"></i> Shipping Cost</a></li> -->
							<li><a href="top-category.php"><i class="fa fa-circle-o"></i> Category</a></li>
							<li><a href="mid-category.php"><i class="fa fa-circle-o"></i> Sub Category</a></li>
							
						</ul>
					</li>


					<li class="treeview <?php if( ($cur_page == 'product.php') || ($cur_page == 'product-add.php') || ($cur_page == 'product-edit.php') ) {echo 'active';} ?>">
			          <a href="product.php">
			            <i class="fa fa-hand-o-right"></i> <span>Product</span>
			          </a>
			        </li>


			        <li class="treeview <?php if( ($cur_page == 'order.php') ) {echo 'active';} ?>">
			          <a href="order.php">
			            <i class="fa fa-hand-o-right"></i> <span>Order</span>
			          </a>
			        </li>

					<li class="treeview <?php if( ($cur_page == 'pending-payout.php') || ($cur_page == 'completed-payout.php') ) {echo 'active';} ?>">
						<a href="#">
							<i class="fa fa-hand-o-right"></i>
							<span>Payouts</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							
							<li><a href="pending-payout.php"><i class="fa fa-circle-o"></i> Pending Payouts</a></li>
							<li><a href="completed-payout.php"><i class="fa fa-circle-o"></i> Completed Payouts</a></li>
							
						</ul>
					</li>

			        <li class="treeview <?php if( ($cur_page == 'rating.php') ) {echo 'active';} ?>">
			          <a href="rating.php">
			            <i class="fa fa-hand-o-right"></i> <span>Rating</span>
			          </a>
			        </li>

			        <li class="treeview <?php if( ($cur_page == 'language.php') ) {echo 'active';} ?>">
			          <a href="language.php">
			            <i class="fa fa-hand-o-right"></i> <span>Language Settings</span>
			          </a>
			        </li>
					


					<li class="treeview <?php if( ($cur_page == 'customer.php') || ($cur_page == 'customer-add.php') || ($cur_page == 'customer-edit.php') ) {echo 'active';} ?>">
			          <a href="customer.php">
			            <i class="fa fa-hand-o-right"></i> <span>Customer/Vendor</span>
			          </a>
			        </li>

			        <li class="treeview <?php if( ($cur_page == 'page.php') ) {echo 'active';} ?>">
			          <a href="page.php">
			            <i class="fa fa-hand-o-right"></i> <span>Page</span>
			          </a>
			        </li>


			        <li class="treeview <?php if( ($cur_page == 'social-media.php') ) {echo 'active';} ?>">
			          <a href="social-media.php">
			            <i class="fa fa-hand-o-right"></i> <span>Social Media</span>
			          </a>
			        </li>

			        <li class="treeview <?php if( ($cur_page == 'advertisement.php') ) {echo 'active';} ?>">
			          <a href="advertisement.php">
			            <i class="fa fa-hand-o-right"></i> <span>Advertisement</span>
			          </a>
			        </li>

			        <li class="treeview <?php if( ($cur_page == 'subscriber.php')||($cur_page == 'subscriber.php') ) {echo 'active';} ?>">
			          <a href="subscriber.php">
			            <i class="fa fa-hand-o-right"></i> <span>Subscriber</span>
			          </a>
			        </li>			        

			        


      			</ul>
    		</section>
  		</aside>

  		<div class="content-wrapper">