<?php require_once('header.php'); ?>

<?php 
if(!isset($_REQUEST['id']))
{
	header('location: customer.php');
	exit;
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Customer Details</h1>
	</div>
</section>

<?php
$i=0;
$statement = $pdo->prepare("SELECT t1.*,t2.country_name as country_name,t3.country_name as business_country
							FROM tbl_customer t1
							LEFT JOIN tbl_country t2
							ON t1.cust_country = t2.country_id
							LEFT JOIN tbl_country t3
							ON t1.cust_b_country = t3.country_id
							WHERE cust_id=?
						");
$statement->execute([$_REQUEST['id']]);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);						
foreach ($result as $row) {
	$cust_name = $row['cust_name'];
	
	$cust_email = $row['cust_email'];
	$cust_phone = $row['cust_phone'];
	$cust_country = $row['country_name'];
	$cust_address = $row['cust_address'];
	$cust_city = $row['cust_city'];
	$cust_state = $row['cust_state'];
	$cust_zip = $row['cust_zip'];
	$cust_type = $row['customer_type'];

	$sign_type = $row['sign_type'];
	$cust_cname = $row['cust_cname'];
	$vat_b_identifier = $row['vat_b_identifier'];
	$cust_b_code = $row['cust_b_code'];
	$cust_b_country = $row['cust_b_country'];
	$business_country = $row['business_country'];
	$cust_b_city = $row['cust_b_city'];
	$cust_b_zip = $row['cust_b_zip'];

	$identity_number = $row['identity_number'];
	$vat_p_identifier = $row['vat_p_identifier'];

	$cust_pp_email = $row['cust_pp_email'];
	$cust_bank_owner_name = $row['cust_bank_owner_name'];
	$cust_acc_no = $row['cust_acc_no'];
	$cust_bank_ifsc = $row['cust_bank_ifsc'];

	$cust_status = $row['cust_status'];
}


$social_info_sql = $pdo->prepare('SELECT * FROM tbl_customer_social_info where customer_id=?');
$social_info_sql->execute([$_REQUEST['id']]);

$social_info = $social_info_sql->fetch(PDO::FETCH_ASSOC);


$tbl_notification_setting_sql = $pdo->prepare('SELECT * FROM tbl_notification_setting where customer_id=?');
$tbl_notification_setting_sql->execute([$_REQUEST['id']]);

$notification_setting = $tbl_notification_setting_sql->fetch(PDO::FETCH_ASSOC);

?>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-body table-responsive">

					<h4 style="color:blue;">General Information</h4 style="color:blue;">
					<table class="table table-bordered table-striped">
						<tbody>
							<tr>
								<th style="width: 270px;">Customer Name</th>
								<td><?php echo $cust_name; ?></td>
							</tr>
							
							<tr>
								<th>Customer Email</th>
								<td><?php echo $cust_email; ?></td>
							</tr>
							<tr>
								<th>Customer Phone</th>
								<td><?php echo $cust_phone; ?></td>
							</tr>
							<tr>
								<th>Customer Country</th>
								<td><?php echo $cust_country; ?></td>
							</tr>
							<tr>
								<th>Customer Address</th>
								<td><?php echo $cust_address; ?></td>
							</tr>
							<tr>
								<th>Customer City</th>
								<td><?php echo $cust_city; ?></td>
							</tr>
							<tr>
								<th>Customer State</th>
								<td><?php echo $cust_state; ?></td>
							</tr>
							<tr>
								<th>Customer Zip Code</th>
								<td><?php echo $cust_zip; ?></td>
							</tr>
							<tr>
								<th>Customer Interest</th>
								<td><?php echo ucfirst($cust_type); ?></td>
							</tr>
						</tbody>
					</table>
					
					<?php if(!empty($social_info)):?>
					<h4 style="color:blue;margin-top:50px;">Social Network</h4 style="color:blue;">
					<table class="table table-bordered table-striped">
						<tbody>
							<tr>
								<th style="width: 270px;">Twitter Handle (Username)</th>
								<td><?php echo $social_info['twitter_handle']; ?></td>
							</tr>
							<tr>
								<th>Facebook Page</th>
								<td><?php echo $social_info['fb_id']; ?></td>
							</tr>
							<tr>
								<th>LinkedIn Page</th>
								<td><?php echo $social_info['linkdin_id']; ?></td>
							</tr>
							
						</tbody>
					</table>
					<?php endif; ?>
					
					<?php if(!empty($sign_type)):?>
					<h4 style="color:blue;margin-top:50px;">Payment Agreement</h4 style="color:blue;">
					<table class="table table-bordered table-striped">
						<tbody>
							<tr>
								<th style="width: 270px;">Signed As</th>
								<td><?php echo ucfirst($sign_type); ?></td>
							</tr>
							<?php if($sign_type == 'business'){ ?>
							<tr>
								<th>Company Name</th>
								<td><?php echo $cust_cname; ?></td>
							</tr>
							<tr>
								<th>VAT Identifier</th>
								<td><?php echo $vat_b_identifier; ?></td>
							</tr>
							<tr>
								<th>Company code</th>
								<td><?php echo $cust_b_code; ?></td>
							</tr>
							<tr>
								<th>Company Country</th>
								<td><?php echo $business_country; ?></td>
							</tr>
							<tr>
								<th>Company City</th>
								<td><?php echo $cust_b_code; ?></td>
							</tr>
							<tr>
								<th>Company Zip</th>
								<td><?php echo $cust_b_code; ?></td>
							</tr>
							<?php }else{ ?>
								<tr>
									<th>Identity Number</th>
									<td><?php echo $identity_number; ?></td>
								</tr>
								<tr>
									<th>VAT Identifier</th>
									<td><?php echo $vat_p_identifier; ?></td>
								</tr>
							<?php } ?>

							<tr>
								<th>Paypal Email</th>
								<td><?php echo $cust_pp_email; ?></td>
							</tr>
							<tr>
								<th>Bank Owner Name</th>
								<td><?php echo $cust_bank_owner_name; ?></td>
							</tr>
							<tr>
								<th>Bank Account Number</th>
								<td><?php echo $cust_acc_no; ?></td>
							</tr>
							<tr>
								<th>Bank IFSC Code</th>
								<td><?php echo $cust_bank_ifsc; ?></td>
							</tr>

						</tbody>
					</table>
					<?php endif; ?>
								

					<h4 style="color:blue;margin-top:50px;">Product Created</h4 style="color:blue;">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th width="30">SL</th>
								<th>Photo</th>
								<th>Category</th>
								<th width="200">Product Name</th>
								<th width="60">Old Price</th>
								<th width="60">Current Price</th>
								<th width="60">Tags</th>
								<th>Is Featured?</th>
								<th>Is Active?</th>
								
							</tr>
						</thead>
						<tbody>
							<?php
							$i=0;
							$statement = $pdo->prepare("SELECT
														
														t1.p_id,
														t1.p_name,
														t1.p_old_price,
														t1.p_current_price,
														t1.p_featured_photo,
														t1.p_is_featured,
														t1.p_is_active,
														t1.p_tags,
														t1.is_free,
														t1.cat_id,
														t1.subcat_id,
														
														t2.tcat_name,
														t2.tcat_id,
														
														t3.mcat_id,
														t3.mcat_name

							                           	FROM tbl_product t1

							                           	left JOIN tbl_top_category t2
							                           	ON t1.cat_id = t2.tcat_id

							                           	left JOIN tbl_mid_category t3
							                           	ON t1.subcat_id = t3.mcat_id
														
														where t1.is_delete =0 and t1.user_type ='Customer' and t1.user_id = ?
							                           	ORDER BY t1.p_id DESC
							                           	");
							$statement->execute([$_REQUEST['id']]);
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);
							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td style="width:130px;"><img src="../public_files/uploads/<?php echo $row['p_featured_photo']; ?>" alt="<?php echo $row['p_name']; ?>" style="width:100px;"></td>
									<td><?php echo $row['tcat_name']; ?><br><?php echo $row['mcat_name']; ?></td>
									
									<td><?php echo $row['p_name']; ?></td>
									<td><?php echo $row['p_old_price']; ?></td>
									<td><?php echo ($row['is_free']== 0)?($row['p_current_price']):'Free'; ?></td>
									<td><?php echo $row['p_tags']; ?></td>
									<td>
										<?php if($row['p_is_featured'] == 1) {echo 'Yes';} else {echo 'No';} ?>
									</td>
									<td>
										<?php if($row['p_is_active'] == 1) {echo 'Yes';} else {echo 'No';} ?>
									</td>
									
								</tr>
								<?php
							}
							?>							
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

</section>


<?php require_once('footer.php'); ?>