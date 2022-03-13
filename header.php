<?php
ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");
include("admin/inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();


$get_commission_setting = $pdo->prepare('Select * from tbl_setting_commission where active = 1 ');
$get_commission_setting->execute();
$get_commission = $get_commission_setting->fetch(PDO::FETCH_ASSOC);
 

if(isset($_SESSION['setCurrency'])){
	$statement = $pdo->prepare("SELECT * FROM tbl_setting_currency WHERE id=".$_SESSION['setCurrency']);
}else{
	$statement = $pdo->prepare("SELECT * FROM tbl_setting_currency WHERE default_currency=1");
}

$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	define('CURRENCY_CODE',$row['currency_code']);
	define('CURRENCY_SYMBOL',$row['currency_symbol']);
	define('CURRENCY_POSITION',$row['currency_position']);
	define('CURRENCY_VALUE',$row['currency_value_per_usd']);
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_logo WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	$logo = $row['logo'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_favicon WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	$favicon = $row['favicon'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_contact WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	$contact_email = $row['contact_email'];
	$contact_phone = $row['contact_phone'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_home WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	$meta_title_home       = $row['meta_title_home'];
	$meta_keyword_home     = $row['meta_keyword_home'];
	$meta_description_home = $row['meta_description_home'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_head_body WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	$before_head = $row['before_head'];
	$after_body  = $row['after_body'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_color WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	$theme_color = $row['color'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_email WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	$send_email_from  = $row['send_email_from'];
	$receive_email_to = $row['receive_email_to'];
	$smtp_active      = $row['smtp_active'];
	$smtp_ssl         = $row['smtp_ssl'];
	$smtp_host        = $row['smtp_host'];
	$smtp_port        = $row['smtp_port'];
	$smtp_username    = $row['smtp_username'];
	$smtp_password    = $row['smtp_password'];
}

 

require 'public_files/mail/PHPMailer.php';
require 'public_files/mail/Exception.php';
require 'public_files/mail/SMTP.php';
$mail = new PHPMailer\PHPMailer\PHPMailer(true);

if($smtp_active == 'Yes')
{
	if($smtp_ssl == 'Yes')
	{
		$mail->SMTPSecure = "ssl";
	}else{
		$mail->SMTPSecure = "tls";
	}
	
	// $mail->SMTPDebug =  PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
	$mail->IsSMTP();
	$mail->SMTPAuth   = true;
	$mail->Host       = $smtp_host;
	$mail->Port       = $smtp_port;
	$mail->Username   = $smtp_username;
	$mail->Password   = $smtp_password;	
	
}


$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';
$error_message2 = '';
$success_message2 = '';

// Getting all language variables into array as global variable
$i=1;
$statement = $pdo->prepare("SELECT * FROM tbl_language");
$statement->execute();
$result = $statement->fetchAll();							
foreach ($result as $row) {
	define($row['lang_name'],$row['lang_value']);
	$i++;
}



// Checking the order table and removing the pending transaction that are 24 hours+ old
$current_date_time = date('Y-m-d H:i:s');
$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=?");
$statement->execute(array('Pending'));
$result = $statement->fetchAll();							
foreach ($result as $row) {
	$ts1 = strtotime($row['payment_date']);
	$ts2 = strtotime($current_date_time);     
	$diff = $ts2 - $ts1;
	$time = $diff/(3600);
	if($time>24) {

		// Return back the stock amount
		$statement1 = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
		$statement1->execute(array($row['payment_id']));
		$result1 = $statement1->fetchAll();
		foreach ($result1 as $row1) {
			$statement2 = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
			$statement2->execute(array($row1['product_id']));
			$result2 = $statement2->fetchAll();							
			
		}
		
		// Deleting data from table
		$statement1 = $pdo->prepare("DELETE FROM tbl_order WHERE payment_id=?");
		$statement1->execute(array($row['payment_id']));

		$statement1 = $pdo->prepare("DELETE FROM tbl_payment WHERE id=?");
		$statement1->execute(array($row['id']));
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

	<!-- Meta Tags -->
	<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>

	<!-- Favicon -->
	<link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>public_files/uploads/<?php echo $favicon; ?>">

	<!-- Stylesheets -->
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/owl.carousel.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/owl.theme.default.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/jquery.bxslider.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/magnific-popup.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/rating.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/spacing.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/bootstrap-touch-slider.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/animate.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/tree-menu.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/select2.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/main.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/css/responsive.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public_files/bootstrap-tagsinput-latest/src/bootstrap-tagsinput.css">

	<?php

	$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
	$statement->execute();
	$result = $statement->fetchAll();							
	foreach ($result as $row) {
		$about_meta_title = $row['about_meta_title'];
		$about_meta_keyword = $row['about_meta_keyword'];
		$about_meta_description = $row['about_meta_description'];
		$faq_meta_title = $row['faq_meta_title'];
		$faq_meta_keyword = $row['faq_meta_keyword'];
		$faq_meta_description = $row['faq_meta_description'];
		$blog_meta_title = $row['blog_meta_title'];
		$blog_meta_keyword = $row['blog_meta_keyword'];
		$blog_meta_description = $row['blog_meta_description'];
		$contact_meta_title = $row['contact_meta_title'];
		$contact_meta_keyword = $row['contact_meta_keyword'];
		$contact_meta_description = $row['contact_meta_description'];
		$tnc_meta_title = $row['tnc_meta_title'];
		$tnc_meta_keyword = $row['tnc_meta_keyword'];
		$tnc_meta_description = $row['tnc_meta_description'];
		$privacy_meta_title = $row['privacy_meta_title'];
		$privacy_meta_keyword = $row['privacy_meta_keyword'];
		$privacy_meta_description = $row['privacy_meta_description'];
	}

	$cur_page = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	
	if($cur_page == 'index.php' || $cur_page == 'login.php' || $cur_page == 'registration.php' || $cur_page == 'cart.php' || $cur_page == 'checkout.php' || $cur_page == 'forget-password.php' || $cur_page == 'reset-password.php' || $cur_page == 'product-category.php' || $cur_page == 'product.php') {
		?>
		<title><?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}

	if($cur_page == 'about.php') {
		?>
		<title><?php echo $about_meta_title; ?></title>
		<meta name="keywords" content="<?php echo $about_meta_keyword; ?>">
		<meta name="description" content="<?php echo $about_meta_description; ?>">
		<?php
	}
	if($cur_page == 'faq.php') {
		?>
		<title><?php echo $faq_meta_title; ?></title>
		<meta name="keywords" content="<?php echo $faq_meta_keyword; ?>">
		<meta name="description" content="<?php echo $faq_meta_description; ?>">
		<?php
	}
	if($cur_page == 'blog.php') {
		?>
		<title><?php echo $blog_meta_title; ?></title>
		<meta name="keywords" content="<?php echo $blog_meta_keyword; ?>">
		<meta name="description" content="<?php echo $blog_meta_description; ?>">
		<?php
	}
	if($cur_page == 'contact.php') {
		?>
		<title><?php echo $contact_meta_title; ?></title>
		<meta name="keywords" content="<?php echo $contact_meta_keyword; ?>">
		<meta name="description" content="<?php echo $contact_meta_description; ?>">
		<?php
	}
	if($cur_page == 'term-and-conditions.php') {
		?>
		<title><?php echo $tnc_meta_title; ?></title>
		<meta name="keywords" content="<?php echo $tnc_meta_keyword; ?>">
		<meta name="description" content="<?php echo $tnc_meta_description; ?>">
		<?php
	}
	if($cur_page == 'privacy.php') {
		?>
		<title><?php echo $privacy_meta_title; ?></title>
		<meta name="keywords" content="<?php echo $privacy_meta_keyword; ?>">
		<meta name="description" content="<?php echo $privacy_meta_description; ?>">
		<?php
	}

	if($cur_page == 'blog-single.php')
	{
		$statement = $pdo->prepare("SELECT * FROM tbl_post WHERE post_slug=?");
		$statement->execute(array($_REQUEST['slug']));
		$result = $statement->fetchAll();							
		foreach ($result as $row) 
		{
		    $og_photo = $row['photo'];
		    $og_title = $row['post_title'];
		    $og_slug = $row['post_slug'];
			$og_description = substr(strip_tags($row['post_content']),0,200).'...';
			echo '<meta name="description" content="'.$row['meta_description'].'">';
			echo '<meta name="keywords" content="'.$row['meta_keyword'].'">';
			echo '<title>'.$row['meta_title'].'</title>';
		}
	}

	if($cur_page == 'product.php')
	{
		$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
		$statement->execute(array($_REQUEST['id']));
		$result = $statement->fetchAll();							
		foreach ($result as $row) 
		{
		    $og_photo = $row['p_featured_photo'];
		    $og_title = $row['p_name'];
		    $og_slug = 'product.php?id='.$_REQUEST['id'];
			$og_description = substr(strip_tags($row['p_description']),0,200).'...';
		}
	}

	if($cur_page == 'dashboard.php') {
		?>
		<title>Dashboard - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	if($cur_page == 'account-settings.php') {
		?>
		<title>Settings - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	if($cur_page == 'customer-billing-shipping-update.php') {
		?>
		<title>Update Billing and Shipping Info - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	if($cur_page == 'change-password.php' ) {
		?>
		<title>Change Password - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}
	
	if($cur_page == 'publishing.php'){
		echo "<title>Publishing - $meta_title_home </title>
		<meta name='keywords' content='$meta_keyword_home'>
		<meta name='description' content='$meta_description_home'>";
	}

	if($cur_page == 'payment-agreement.php'){
		echo "<title>Payment Agreement - $meta_title_home </title>
		<meta name='keywords' content='$meta_keyword_home'>
		<meta name='description' content='$meta_description_home'>";
	}

	if($cur_page == 'social-network-and-contact-info.php'){
		echo "<title>Social Networks - $meta_title_home </title>
		<meta name='keywords' content='$meta_keyword_home'>
		<meta name='description' content='$meta_description_home'>";
	
	}
	if($cur_page == 'notification-setting.php'){
		echo "<title>Network Setting - $meta_title_home </title>
		<meta name='keywords' content='$meta_keyword_home'>
		<meta name='description' content='$meta_description_home'>";
	}

	if($cur_page == 'customer-order.php') {
		?>
		<title>Orders - <?php echo $meta_title_home; ?></title>
		<meta name="keywords" content="<?php echo $meta_keyword_home; ?>">
		<meta name="description" content="<?php echo $meta_description_home; ?>">
		<?php
	}


	function wishlist_function($pdo,$prod_id){
		
		if(isset($_SESSION['customer'])){
			$user_id = $_SESSION['customer']['cust_id'];
			$user_type = 'Customer';
			$get_wishlist= $pdo->prepare("SELECT * FROM tbl_wishlist WHERE product_id=".$prod_id." and customer_id=".$user_id." and user_type= '".$user_type."'");
			$get_wishlist->execute();
			$get_wishlist = $get_wishlist->fetch(PDO::FETCH_ASSOC);
			
			if(!empty($get_wishlist)){
				return 'fa fa-heart';
			}else{
				return 'fa fa-heart-o';
			}
		}else{
			return 'fa fa-heart-o';
		}
	}

	function get_sale_discount_func($pdo,$customer_id){
		// check from table
		$discount_table = $pdo->prepare('SELECT * from tbl_customer_discount_setting where customer_id=?');
		$discount_table->execute([$customer_id]);
		$customer_dis_row = $discount_table->fetch(PDO::FETCH_ASSOC);

		if(empty($customer_dis_row)){
			return  ['discount_applied'=>0];
		}else{
			return  ['discount_applied'=>$customer_dis_row['participation_bit'],'sale_perc'=>$customer_dis_row['discount_rate_sale_period'],'supersale_perc'=>$customer_dis_row['discount_rate_supersale_period'] ];
		}
	}
	

	function check_supersale($sale_arr){
		if($sale_arr['sale_type'] == 'special_sale'){
			return $sale_arr;
		}
	}
	// sale active
	$sale_setting_statement = $pdo->prepare('Select * from tbl_sale_period where sale_start_time <= CURDATE() and sale_end_time >= CURDATE()');
	$sale_setting_statement->execute();
	$sale_date_setting_list =  $sale_setting_statement->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($sale_date_setting_list)>0){
		$any_special_sale = array_filter($sale_date_setting_list,'check_supersale');
		
		if(count($any_special_sale)>0){
			$sale_date_setting_list = $any_special_sale;
		}
		else{
			$sale_date_setting_list = $sale_date_setting_list[0];
		}	
	}
	
	function final_sale_check($sale_date_setting_list,$check_discount_table){
		
		if($sale_date_setting_list['sale_type']== 'sale' && $check_discount_table['sale_perc'] != 0 ){ 
			return ['sale_type'=>'sale','perc'=>$check_discount_table['sale_perc']];
		}elseif($sale_date_setting_list['sale_type']== 'special_sale' && $check_discount_table['supersale_perc'] != 0 ){ 
			return ['sale_type'=>'special_sale','perc'=>$check_discount_table['supersale_perc']];
		}else{
			return ['sale_type'=>'none','perc'=> 0 ];
		}
			
	}
	
	function default_prod_price($row){
		
		if(CURRENCY_POSITION == 'Before') {
			return CURRENCY_SYMBOL.' '.number_format(($row['p_current_price'] * CURRENCY_VALUE),2);
		} else {
			return number_format(($row['p_current_price'] * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL;
		
		}

		if(!empty($row['p_old_price'])){
			if(CURRENCY_POSITION == 'Before') {
			
				return '<del>'.CURRENCY_SYMBOL.' '.number_format(($row['p_old_price'] * CURRENCY_VALUE),2).'</del>';
			} else {
				return '<del>'.number_format(($row['p_old_price'] * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL.'</del>';
				
			} 
		}
	}


	function check_customer_name($pdo,$user_id){
		$check_customer_name = $pdo->prepare('SELECT cust_email from tbl_customer where cust_id = ?');
		$check_customer_name->execute(array($user_id));
		$get_customer_name = $check_customer_name->fetch(PDO::FETCH_ASSOC);

		return $get_customer_name ;
	}
	
	?>
	
	<?php if($cur_page == 'blog-single.php'): ?>
		<meta property="og:title" content="<?php echo $og_title; ?>">
		<meta property="og:type" content="website">
		<meta property="og:url" content="<?php echo BASE_URL.$og_slug; ?>">
		<meta property="og:description" content="<?php echo $og_description; ?>">
		<meta property="og:image" content="public_files/uploads/<?php echo $og_photo; ?>">
	<?php endif; ?>

	<?php if($cur_page == 'product.php'): ?>
		<meta property="og:title" content="<?php echo $og_title; ?>">
		<meta property="og:type" content="website">
		<meta property="og:url" content="<?php echo BASE_URL.$og_slug; ?>">
		<meta property="og:description" content="<?php echo $og_description; ?>">
		<meta property="og:image" content="public_files/uploads/<?php echo $og_photo; ?>">
	<?php endif; ?>


	<script src="<?php echo BASE_URL; ?>public_files/js/jquery-2.2.4.min.js"></script>
	<script src="<?php echo BASE_URL; ?>public_files/js/bootstrap.min.js"></script>
	<script src="https://js.stripe.com/v2/"></script>
	<script src="<?php echo BASE_URL; ?>public_files/js/megamenu.js"></script>
	<script src="<?php echo BASE_URL; ?>public_files/js/owl.carousel.min.js"></script>
	<script src="<?php echo BASE_URL; ?>public_files/js/owl.animate.js"></script>
	<script src="<?php echo BASE_URL; ?>public_files/js/jquery.bxslider.min.js"></script>
	<script src="<?php echo BASE_URL; ?>public_files/js/jquery.magnific-popup.min.js"></script>
	<script src="<?php echo BASE_URL; ?>public_files/js/rating.js"></script>
	<script src="<?php echo BASE_URL; ?>public_files/js/jquery.touchSwipe.min.js"></script>
	<script src="<?php echo BASE_URL; ?>public_files/js/bootstrap-touch-slider.js"></script>
	<script src="<?php echo BASE_URL; ?>public_files/js/select2.full.min.js"></script>
	<script src="<?php echo BASE_URL; ?>public_files/bootstrap-tagsinput-latest/src/bootstrap-tagsinput.js"></script>
	<script src="https://cdn.ckeditor.com/4.16.0/full/ckeditor.js"></script>
	
	<script src="<?php echo BASE_URL; ?>admin/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo BASE_URL; ?>admin/js/dataTables.bootstrap.min.js"></script>

	<script>
		$(function(){
			$("#example1").DataTable();
			$(".datatable").DataTable();
			$(".currency-dd").change(function(){
				var value = $(this).val();
				
				$.ajax({
					type:'json',
					method:'post',
					url:'ajax_function.php',															
					data:{'curr-dd':value},
					success:function(result){
						location.reload();
					}
				})
			});

			$(".wishlist_btn").click(function(){;
				var input_d = this;
				var get_btn_detail = this.attributes["data-id"].value;

				$.ajax({
					type:'json',
					method:'post',
					url:'ajax_function.php',																
					data:{'wishlist_products':get_btn_detail},
					success:function(result){
						result = JSON.parse(result);
						if(result.status == 'added'){
							$("a[data-id='"+get_btn_detail+"']").children().attr("class","fa fa-heart");
						}else if(result.status == 'removed'){
							$("a[data-id='"+get_btn_detail+"']").children().attr("class","fa fa-heart-o");
						}else if(result.status == 'notlogin'){
							
							window.open('login.php','_self');
						}
					}
				})
			})
		})
		
	</script>


	<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
	
	<script type="text/javascript" src="//platform-api.sharethis.com/js/sharethis.js#property=5993ef01e2587a001253a261&product=inline-share-buttons"></script>
	
	<style>
		
		.top .right ul li a:hover,
        .nav,
        .menu-container,
        .slide-text > a.btn-primary,
        .welcome p.button a,
        .product .owl-controls .owl-prev:hover, 
        .product .owl-controls .owl-next:hover,
        .product .text p a,
        .home-blog .text p.button a,
        .home-newsletter,
        .footer-main h3:after,
        .scrollup i,
        .cform input[type="submit"],
        .blog p.button a,
        div.pagination a,
        #left ul.nav>li.cat-level-1.parent>a,
        .product .btn-cart1 input[type="submit"],
        .review-form .btn-default {
			background: #<?php echo $theme_color; ?>!important;
		}
        
        #left ul.nav>li.cat-level-1.parent>a>.sign, 
        #left ul.nav>li.cat-level-1 li.parent>a>.sign {
            background-color: #<?php echo $theme_color; ?>!important;
        }
        
        .top .left ul li,
        .top .left ul li i,
        .top .right ul li a,
        .header .right ul li,
        .header .right ul li a,
        .welcome p.button a:hover,
        .product .text h4,
        .cform address span,
        .blog h3 a:hover,
        .blog .text ul.status li a,
        .blog .text ul.status li,
        .widget ul li a:hover,
        .breadcrumb ul li,
        .breadcrumb ul li a,
        .product .p-title h2, .currency-dd {
			color: #<?php echo $theme_color; ?>!important;
		}
        
        .scrollup i,
        div.pagination a,
        #left ul.nav>li.cat-level-1.parent>a {
            border-color: #<?php echo $theme_color; ?>!important;
        }
        
        .widget h4 {
            border-bottom-color: #<?php echo $theme_color; ?>!important;
        }
        
        
        .top .right ul li a:hover,
        #left ul.nav>li.cat-level-1 .lbl1 {
            color: #fff!important;
        }
        .welcome p.button a:hover {
            background: #fff!important;
        }
        .slide-text > a:hover, .slide-text > a:active {
            background-color: #333333!important;
        }
        .product .text p a:hover,
        .home-blog .text p.button a:hover,
        .blog p.button a:hover {
            background: #333!important;
        }
        
        div.pagination span.current {
            border-color: #777!important;
            background: #777!important;
        }
        
        div.pagination a:hover, 
        div.pagination a:active {
            border-color: #777!important;
            background: #777!important;
        }
        
        .product .nav {
            background: transparent!important;
        }
        
    </style>

<?php echo $before_head; ?>

</head>
<body>

<?php echo $after_body; ?>

<div id="preloader">
	<div id="status"></div>
</div>

<div class="top">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="left">
					<ul>
						<li><i class="fa fa-phone"></i> <?php echo $contact_phone; ?></li>
						<li><i class="fa fa-envelope-o"></i> <?php echo $contact_email; ?></li>
					</ul>
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="right">
					<ul>
						<li><a href="wishlist.php"><i class="fa fa-heart-o"> </i> Wishlist</a></li>
						<?php 
						$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
						$statement->execute();
						$result = $statement->fetch();
						
						$contact_title = $result['contact_title'];
						
						?>
						<li><a href="contact.php"><?php echo $contact_title; ?></a></li>
						<li><div>
						<a href="cart.php"><i class="fa fa-shopping-cart"></i> <?php echo VIEW_CART; ?> (<?php if(CURRENCY_POSITION == 'Before') {echo CURRENCY_SYMBOL;} ?><?php
						if(isset($_SESSION['cart_p_id'])) {
							$table_total_price = 0;
							$i=0;
							
							foreach($_SESSION['cart_p_current_price'] as $key => $value) 
							{
								$i++;
								$arr_cart_p_current_price[$i] = $value;

								$table_total_price = $table_total_price + $value;
							}
							
							echo $table_total_price * CURRENCY_VALUE;
						} else {
							echo '0.00';
						}
						?><?php if(CURRENCY_POSITION == 'After') {echo CURRENCY_SYMBOL;} ?>)</a></div></li>
						
						<li>
							<div >
								<select class="currency-dd">
									<?php $statement = $pdo->prepare("SELECT * FROM tbl_setting_currency"); 
										$statement->execute();
										$currency_list = $statement->fetchAll(PDO::FETCH_ASSOC);
										
										foreach($currency_list as $ind => $cur){
									?>
									<option value="<?php echo $cur['id']; ?>" <?php echo ((isset($_SESSION['setCurrency']) && $_SESSION['setCurrency']==$cur['id'])? "selected":"") ;?> ><?php echo $cur['currency_code'].' ('.$cur['currency_symbol'].') '; ?></option>
									<?php		
										}
									?>
									
								</select>
							</div>
							
						</li>		
					</ul>
				</div>
				
			</div>
		</div>
	</div>
</div>


<div class="header">
	<div class="container">
		<div class="row inner">
			<div class="col-md-4 logo">
				<a href="index.php"><img src="public_files/uploads/<?php echo $logo; ?>" alt="logo image"></a>
			</div>
			
			<div class="col-md-5 right">
				<ul>
					
					<?php
					if(isset($_SESSION['customer'])) {
						?>
					<li>
						<div class="dropdown">
							<div class="btn btn-link dropdown-toggle "  href="#" type="button" data-toggle="dropdown"><i class="fa fa fa-user-circle-o"></i> Hi <?php echo $_SESSION['customer']['cust_name']; ?> <span class="fa fa-caret-down"></span></div>
							<div class="dropdown-menu head-tooltiptext">
								<div class="head_drop_name_div">
									<div class="color-white"><i class="fa fa-user-circle-o"></i> <?php echo LOGGED_IN_AS; ?> <?php echo $_SESSION['customer']['cust_name']; ?> </div>
								</div>
								<table class="table table-hover table-bordered"  id="dropdown-logout">
									
									<tr class="first">
										<td><a target="_blank" title="Go to Dashboard" href="dashboard.php"><?php echo DASHBOARD; ?></a></td>
									</tr>
									<tr>
										<td><a href="logout.php" ><?php echo LOGOUT ; ?></a></td>
									</tr>
								</table>
							</div>
						</div>
					</li> 
						<?php
					} else {
						?>
						<li><a href="login.php"><i class="fa fa-sign-in"></i> <?php echo LOGIN; ?></a></li>
						<li><a href="registration.php"><i class="fa fa-user-plus"></i> <?php echo REGISTER; ?></a></li>
						<?php	
					}
					?>

					
				</ul>
			</div>
			<div class="col-md-3 search-area">
				<form class="navbar-form navbar-left" role="search" action="search-result.php" method="get">
					<?php $csrf->echoInputField(); ?>
					<div class="form-group">
						<input type="text" class="form-control search-top" placeholder="<?php echo SEARCH_PRODUCT; ?>" name="search_text">
					</div>
					<button type="submit" class="btn btn-default"><?php echo SEARCH; ?></button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="nav main-nav">
	<div class="container">
		<div class="row">
			<div class="col-md-12 pl_0 pr_0">
				<div class="menu-container">
					<div class="menu">
						<ul>
							<li><a href="index.php">Home</a></li>
							
							<?php
							$statement = $pdo->prepare("SELECT * FROM tbl_top_category WHERE show_on_menu=1");
							$statement->execute();
							$result = $statement->fetchAll();
							foreach ($result as $row) {
								
								?>
								<li><a href="product-category.php?id=<?php echo $row['tcat_id']; ?>&type=top-category"><?php echo $row['tcat_name']; ?></a>
									
								<?php
									$statement1 = $pdo->prepare("SELECT * FROM tbl_mid_category WHERE tcat_id=?");
									$statement1->execute(array($row['tcat_id']));
									$result1 = $statement1->fetchAll();
									if(count($result1)>0){
								?>
									<ul>
										<?php
										
										foreach ($result1 as $row1) {
											?>
											<li><a href="product-category.php?id=<?php echo $row1['mcat_id']; ?>&type=mid-category"><?php echo $row1['mcat_name']; ?></a>
												
											</li>
											<?php
										}?>
									</ul>
									<?php } ?>
								</li>
							<?php } ?>

						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
