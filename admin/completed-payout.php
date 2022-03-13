<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Completed Payouts</h1>
	</div>
	
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th width="30">SL</th>
                                <th width="60">Payout Of</th>
								<th width="200"> Vendor Name</th>
								<th width="200">Sale Count</th>
								<th width="60">Payout Calculation</th>
								<th width="80">Transaction Method</th>
								<th width="80">Transaction ID</th>
								<th width="80">Payout Given On</th>
							</tr>
						</thead>
						<tbody>
							<?php
                                $get_commission_setting = $pdo->prepare('Select * from tbl_setting_commission where active = 1 ');
								$get_commission_setting->execute();
								$get_commission = $get_commission_setting->fetch(PDO::FETCH_ASSOC);

                                $payout_sql = $pdo->prepare('SELECT tbl_payout.* ,tbl_customer.cust_name FROM tbl_payout 	   
                                LEFT JOIN tbl_customer on tbl_payout.vendor_id = tbl_customer.cust_id ORDER BY tbl_payout.pay_id DESC');
                                $payout_sql->execute();
                                $payouts = $payout_sql->fetchAll(PDO::FETCH_ASSOC);

                                foreach($payouts as $index => $pay){
                                    $convert_ord_to_arr = explode(',',$pay['order_ids']);
                                ?>
                                    <tr>
                                        <td>
                                            <?php echo ($index+1); ?>
                                        </td>
                                        <td>
                                            <?php echo date('d-m-Y',strtotime($pay['payout_year'].'-'.$pay['payout_month'].'-01')).' to '.date('t-m-Y',strtotime($pay['payout_year'].'-'.$pay['payout_month'].'-01')); ?>
                                        </td>
                                        <td>
                                            <div style="display:inline-block">
												<?php echo $pay['cust_name']; ?>
											</div>
											<div class="col-md-4">
												<a title="Click here to go on page of vendor informations" href="customer-detail.php?id=<?php echo $pay['vendor_id']; ?>" class="btn btn-primary btn-xs" style="width:100%;margin-bottom:4px;" target="_blank"><i class="fa fa-user-circle"></i></a>
											</div>
                                        </td>
                                        <td>
                                            <div class="col-md-6">
                                                <?php echo count($convert_ord_to_arr); ?>
                                            </div>
                                            <div class="col-md-6">
												<a href="#" data-toggle="modal" data-target="#ord_detail-<?php echo $index; ?>"class="btn btn-warning btn-xs" style="width:100%;margin-bottom:4px;">Order Details</a>
											</div>
                                        </td>
                                        <td>
                                            <?php  echo ADMIN_CURRENCY_SYMBOL.' '.$pay['payout_amt']; ?>
                                        </td>
                                        <td>
                                            <?php echo $pay['transaction_method']; ?>
                                        </td>
                                        <td>
                                            <?php echo $pay['transaction_id']; ?>
                                        </td>
                                        <td>
                                            <?php echo date('Y-m-d h:i A',strtotime($pay['created_at'])); ?>
                                        </td>
                                    </tr>

                                <?php
                                }
                            ?>							
						</tbody>
					</table>
				</div>
			</div>
            <?php foreach($payouts as $index => $pay){ ?>
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
										<th>Cost (In <?php echo ADMIN_CURRENCY_SYMBOL; ?>)</th>
										<th>Commission</th>
									</thead>
									<tbody>
										<?php 
                                            $get_orders_sql = $pdo->prepare('SELECT tbl_order.*,
												tbl_payment.payment_date,
												tbl_product.p_current_price FROM tbl_order 
                                                LEFT JOIN tbl_payment on tbl_payment.payment_id = tbl_order.payment_id
												LEFT JOIN tbl_product on tbl_order.product_id = tbl_product.p_id
                                                where tbl_order.id IN('.$pay['order_ids'].') ');

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
                                            <td><?php echo date('d-m-Y',strtotime($order['payment_date'])) ;?></td>
											
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
											<td><b><?php echo $total_price; ?></b></td>
											<td><b><?php echo "(-) ".$total_commission; ?></b></td>
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
            <?php } ?>
		</div>
	</div>
</section>


<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure want to delete this item?</p>
                <p style="color:red;">Be careful! This product will be deleted from the order table, payment table and rating table also.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>