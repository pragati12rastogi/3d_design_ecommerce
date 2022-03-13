<?php require_once('header.php'); ?>

<section class="content-header">
	<h1>Dashboard</h1>
</section>

<?php 
$statement = $pdo->prepare("SELECT * FROM tbl_customer");
$statement->execute();
$total_customer = $statement->rowCount();


$statement = $pdo->prepare("SELECT * FROM tbl_top_category");
$statement->execute();
$total_top_category = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_mid_category");
$statement->execute();
$total_mid_category = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_end_category");
$statement->execute();
$total_end_category = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_product where is_delete = 0");
$statement->execute();
$total_product = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=?");
$statement->execute(array('Completed'));
$total_order_completed = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE shipping_status=?");
$statement->execute(array('Completed'));
$total_shipping_completed = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=?");
$statement->execute(array('Pending'));
$total_order_pending = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=? AND shipping_status=?");
$statement->execute(array('Completed','Pending'));
$total_order_complete_shipping_pending = $statement->rowCount();
?>

<section class="content">
	<div class="row">
		<div class="col-md-4 col-sm-6 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-aqua-gradient"><i class="fa fa-user-circle-o"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Total Vendors</span>
					<span class="info-box-number"><?php echo $total_customer; ?></span>
					<span><a href="customer.php">View All</a></span>
				</div>
			</div>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-maroon"><i class="fa fa-align-left"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Top Categories</span>
					<span class="info-box-number"><?php echo $total_top_category; ?></span>
					<span><a href="top-category.php">View All</a></span>
				</div>
			</div>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-yellow-gradient"><i class="fa fa-gears"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Mid Categories</span>
					<span class="info-box-number"><?php echo $total_mid_category; ?></span>
					<span><a href="mid-category.php">View All</a></span>
				</div>
			</div>
		</div>
		
		<div class="col-md-4 col-sm-6 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-red-gradient"><i class="fa fa-modx"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Total 3D Models</span>
					<span class="info-box-number"><?php echo $total_product; ?></span>
					<span><a href="product.php">View All</a></span>
				</div>
			</div>
		</div>
		<div class="col-md-4 col-sm-6 col-xs-12">
			<div class="info-box">
				<span class="info-box-icon bg-purple-gradient"><i class="fa fa-hand-o-right"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Total Sales</span>
					<span class="info-box-number"><?php echo $total_order_completed; ?></span>
					<span><a href="order.php">View All</a></span>
				</div>
			</div>
		</div>
		
		
	</div>
</section>

<?php require_once('footer.php'); ?>