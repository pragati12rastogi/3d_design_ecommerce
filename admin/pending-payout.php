<?php require_once('header.php'); ?>


<section class="content-header">
	<div class="content-header-left">
		<h1>View Pending Payouts</h1>
	</div>
	
</section>

<section class="content">
	<?php
		if(!empty($_COOKIE['admin_payout_error'])) {
	
			echo "<div class='alert alert-danger' id='payout-error' style='margin-bottom:20px;'>".$_COOKIE['admin_payout_error']."</div>";
		?>
		<script>
			setTimeout(function() {
				$("#payout-error").remove();
			}, 5000);
		</script>
		<?php
		}
		if(!empty($_COOKIE['admin_payout_success'])) {
			echo "<div class='alert alert-success' id='payout-success' style='margin-bottom:20px;'>".$_COOKIE['admin_payout_success']."</div>";
		?>
		<script>
			setTimeout(function() {
				$("#payout-success").remove();
			}, 5000);
		</script>
		<?php
		}
	?>
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th width="30">SL</th>
                                <th width="150">Date</th>
								<th width="160"> Vendor Name</th>
								<th width="40" >Sale Count</th>
								<th width="180">Payout Calculation</th>
								<th width="80">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php

								$month_wise_total_sale = $pdo->prepare('SELECT tbl_customer.cust_name,tbl_customer.cust_id,Group_concat(tbl_order.product_id) as prod_list, Group_concat(tbl_order.unit_price) as price_list,tbl_payment.payment_date, Group_concat(tbl_order.id) as order_ids, MONTHNAME(tbl_payment.payment_date) as month_name,YEAR(tbl_payment.payment_date)as year_name,tbl_payout.pay_id FROM tbl_payment 
								LEFT JOIN tbl_order on tbl_payment.payment_id = tbl_order.payment_id
								LEFT JOIN tbl_product on tbl_product.p_id = tbl_order.product_id                                			   
								LEFT JOIN tbl_customer on tbl_product.user_id = tbl_customer.cust_id
								LEFT JOIN tbl_payout on tbl_customer.cust_id = tbl_payout.vendor_id and MONTHNAME(tbl_payment.payment_date) = tbl_payout.payout_month and YEAR(tbl_payment.payment_date) = tbl_payout.payout_year


								where tbl_product.user_type = "Customer"  and tbl_payment.payment_status = "Completed" and tbl_payout.pay_id Is NULL
								GROUP BY MONTH(tbl_payment.payment_date), YEAR(tbl_payment.payment_date),tbl_product.user_id 
								ORDER BY tbl_payment.payment_date DESC');
								$month_wise_total_sale->execute();
								$month_wise_sale = $month_wise_total_sale->fetchAll(PDO::FETCH_ASSOC);

								$get_commission_setting = $pdo->prepare('Select * from tbl_setting_commission where active = 1 ');
								$get_commission_setting->execute();
								$get_commission = $get_commission_setting->fetch(PDO::FETCH_ASSOC);
								foreach($month_wise_sale as $index => $monthly_sale){
									$date_format = $monthly_sale['year_name'].'-'.$monthly_sale['month_name'];

									$convert_ord_to_arr = explode(',',$monthly_sale['order_ids']);
									$convert_pri_to_arr = explode(',',$monthly_sale['price_list']);
								?>
									<tr>
										<td>
											<?php  echo ($index+1); ?>
										</td>
										<td>
											<?php echo date('d-m-Y',strtotime($date_format.'-01')).' to '.date('t-m-Y',strtotime($date_format.'-01')); ?>
										</td>
										<td>
											<div style="display:inline-block">
												<?php echo $monthly_sale['cust_name']; ?>
											</div>
											<div class="col-md-4">
												<a title="Click here to go on page of vendor informations" href="customer-detail.php?id=<?php echo $monthly_sale['cust_id']; ?>" class="btn btn-primary btn-xs" style="width:100%;margin-bottom:4px;" target="_blank"><i class="fa fa-user-circle"></i></a>
											</div>
										</td>
										<td>
											<?php  echo count($convert_ord_to_arr); ?>
										</td>
										<td>
											<div class="col-md-6">
												<?php  
												$commission_total = 0;
												if(!empty($get_commission)){
													if($get_commission['setting_type'] == 'percent'){
														
														$get_prod_sql = $pdo->prepare('SELECT * FROM tbl_product where p_id IN('.$monthly_sale['prod_list'].') ');
														$get_prod_sql->execute();
														$get_prod = $get_prod_sql->fetchAll(PDO::FETCH_ASSOC);
														
														foreach($get_prod as $price){
															$calc = $price['p_current_price'] * ($get_commission['setting_value']/100);
															$commission_total = $commission_total + $calc;
														}

													}else{
														$commission_total = $get_commission['setting_value'] * count($convert_pri_to_arr);
													}
													
												}
												echo ADMIN_CURRENCY_SYMBOL.' '. number_format(array_sum($convert_pri_to_arr) - $commission_total,2); ?>
											</div>
											<div class="col-md-6">
												<a href="#" data-toggle="modal" data-target="#ord_detail-<?php echo $index; ?>"class="btn btn-warning btn-xs" style="width:100%;margin-bottom:4px;">Order Details</a>
											</div>
											
										</td>
										<td>
											<a href="#" data-toggle="modal" data-target="#submit_commission-<?php echo $index; ?>"class="btn btn-success btn-xs" style="width:100%;margin-bottom:4px;">Update Status</a>
										</td>
									</tr>

								<?php
								}
							?>							
						</tbody>
					</table>
				</div>
				<?php foreach($month_wise_sale as $index => $monthly_sale){ ?>
				<div id="ord_detail-<?php echo $index; ?>" class="modal fade" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title" style="font-weight: bold;">Order Details</h4>
							</div>
							<div class="modal-body" style="font-size: 14px">
								<table class="table table-hover">
									<thead>
										<th>Sr.No.</th>
										<th>Product</th>
										<th>Purchased date</th>
										<th>Selling Cost (In <?php echo ADMIN_CURRENCY_SYMBOL; ?>)</th>
										<th>Commission</th>
									</thead>
									<tbody>
										<?php $get_orders_sql = $pdo->prepare('SELECT tbl_order.*,tbl_product.p_current_price FROM tbl_order
										LEFT JOIN tbl_product on tbl_order.product_id = tbl_product.p_id
										where tbl_order.id IN('.$monthly_sale['order_ids'].') ');
											$get_orders_sql->execute();
											$get_orders = $get_orders_sql->fetchAll(PDO::FETCH_ASSOC);
											
											$total_price =0;
											$total_commission = 0;

											foreach($get_orders as $ind => $order){
												$total_price = $total_price+$order['unit_price'];
										?>
										<tr>
											<td><?php echo ($ind+1) ;?></td>
											<td><?php echo $order['product_name'] ;?></td>
											<td><?php echo date('d-m-Y',strtotime($monthly_sale['payment_date'])) ;?></td>
											<td><?php echo number_format($order['unit_price'],2) ;?></td>
											<td><?php 
												
												$commission = 0;
												if(!empty($get_commission)){
													if($get_commission['setting_type'] == 'percent'){
														
														$calc = $order['p_current_price'] * ($get_commission['setting_value']/100);
														$commission = $calc;

														
													}else{
														$commission = $get_commission['setting_value'];
														
													}
													
												}
												$total_commission = $total_commission + $commission;
												echo "(-) ".number_format($commission,2);
												?>
											</td>
										</tr>

										<?php } ?>
										<tr>
											<td></td>
											<td></td>
											<td></td>
											<td><b><?php echo number_format($total_price,2); ?></b></td>
											<td><b><?php echo "(-) ".number_format($total_commission,2); ?></b></td>
										</tr>
										<tr>
											<td></td>
											<td></td>
											<td></td>
											
											<td><b>Payout Amount :</b></td>
											<td><b><?php $final_payout_amount = number_format(($total_price-$total_commission),2); 
												echo $final_payout_amount;
											?></b></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>

				<div id="submit_commission-<?php echo $index; ?>" class="modal fade" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title" style="font-weight: bold;">Update Status</h4>
							</div>
							<div class="modal-body" style="font-size: 14px">
								<div class="alert alert-offset-danger" role="alert">
									Please insure while submitting, process can't be undone and this will reflect to vendors
								</div>
								<form action="ajax-function.php" method="post">
									<?php $csrf->echoInputField(); ?>
									<input type="hidden" name="vendor_id" value="<?php echo $monthly_sale['cust_id']; ?>">
									<input type="hidden" name="order_ids" value="<?php echo $monthly_sale['order_ids']; ?>">
									<input type="hidden" name="payout_month" value="<?php echo $monthly_sale['month_name']; ?>">
									<input type="hidden" name="payout_year" value="<?php echo $monthly_sale['year_name']; ?>">
									<input type="hidden" name="payout_amt" value="<?php echo $final_payout_amount; ?>">
									<div class="row">
									
										<div class="col-md-12 form-group" style="display: inline-flex;">
											<label for="" class="col-md-5">Transaction Method * <small>(i.e. Paypal,UPI etc)</small></label>
											<input type="text" name="transaction_method" value="" class="form-control" placeholder="Paypal" required>
										</div>
										<!-- <div class="col-md-12 form-group" style="display: inline-flex;">
											<label for="" class="col-md-5">Transaction Charges * <small>(case of no charges enter 0)</small></label>
											<input type="text" name="transaction_charges" value="" class="form-control" placeholder="0" required>
										</div> -->
										<div class="col-md-12 form-group" style="display: inline-flex;">
											<label for="" class="col-md-5">Transaction Id * </label>
											<input type="text" name="transaction_id" class="form-control" placeholder="ch_1FTgVUBoKopKik6AS3xFvKf1" required >
										</div>
										
										<br>
										<br>
										<div class="col-md-12 form-group"><input type="submit" name="payout_submit" class="btn btn-success" style="float:right"></div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</section>
<style>
	.alert-offset-danger{
		background:#d3584938;
		padding:10px;
	}
</style>
<?php require_once('footer.php'); ?>