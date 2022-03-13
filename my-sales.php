<?php require_once('header.php'); ?>

<?php
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    header('location: '.BASE_URL.'logout.php');
    exit;
} else {
    // If customer is logged in, but admin make him inactive, then force logout this user.
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id=? AND cust_status=?");
    $statement->execute(array($_SESSION['customer']['cust_id'],0));
    $total = $statement->rowCount();
    if($total) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }

    
}


?>

<div class="page">
    <div class="container">
        <div class="row">            
            <div class="col-md-12"> 
                <?php require_once('customer-sidebar.php'); ?>
            </div>
            <div class="col-md-12">
                <h3 >
                    <?php echo MY_SALES; ?>
                </h3>
                <hr class="mt_10">
            </div>
            
            <div >
                <ul class="nav nav-tabs nav_background">
                    <li><a href="#latest_sale" data-toggle="tab">LATEST SALES</a></li>
                    <li><a href="#latest_downloads" data-toggle="tab">LATEST DOWNLOADS</a></li>
                    <li><a href="#monthly_summary" data-toggle="tab">MONTHLY SUMMARY</a></li>
                    <li><a href="#payment_info" data-toggle="tab">PAYMENTS INFORMATION</a></li>
                    <li><a href="#discounts" data-toggle="tab">DISCOUNTS</a></li>
                </ul>

                <div class="col-md-12" id="alert-div">
                    <?php if(!empty($_COOKIE['sale_setting_error'])){ ?>
                    <br>
                    <div class="alert alert-danger sale-setting-alert">
                        <p>
                            <?php echo $_COOKIE['sale_setting_error']; ?>
                        </p>
                    </div>
                    <?php } ?>
                    <?php if(!empty($_COOKIE['sale_setting_success'])){ ?>
                    <br>
                    <div class="alert alert-success sale-setting-alert">
                        <p>
                            <?php echo $_COOKIE['sale_setting_success']; ?>
                        </p>
                    </div>
                    <?php } ?>
                </div>
                <div class="tab-content ">


                    <div class="tab-pane " id="latest_sale">
                        <div class="col-md-12">
                            
                            <table id="latest_sale_table" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        
                                        <th>Payment ID</th>
                                        <th>Date</th>
                                        <th>Product Name</th>
                                        <th>Actual Price</th>
                                        <th>Sale Price</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $user_product_sold = $pdo->prepare('SELECT tbl_payment.* FROM tbl_payment 
                                        LEFT JOIN tbl_order on tbl_payment.payment_id = tbl_order.payment_id
                                        LEFT JOIN tbl_product on tbl_product.p_id = tbl_order.product_id
                                        WHERE tbl_product.user_type = "Customer" and tbl_product.user_id = ? and tbl_payment.payment_status = "Completed" GROUP BY tbl_order.payment_id order by tbl_payment.id');
                                        $user_product_sold->execute(array($_SESSION['customer']['cust_id']));
                                        $user_product_sold_today = $user_product_sold->fetchAll(PDO::FETCH_ASSOC);

                                        foreach($user_product_sold_today as $payment_details){

                                            $get_order_details = $pdo->prepare('SELECT tbl_order.* FROM tbl_order WHERE tbl_order.payment_id=? ');
                                            $get_order_details->execute([$payment_details['payment_id']]);
                                            $product_sold = $get_order_details->fetchAll(PDO::FETCH_ASSOC);

                                            
                                    ?>
                                        <tr>
                                            
                                            <td class="td_center_align"><?php echo $payment_details['payment_id']; ?></td>
                                            <td class="td_center_align"><?php echo date('d-m-Y h:i A',strtotime($payment_details['payment_date'])); ?></td>
                                            
                                            <td>
                                                <?php foreach($product_sold as $prod_detail){
                                                    echo '<p>'.$prod_detail['product_name'].'</p><hr>';
                                                }  
                                                ?>
                                            </td>
                                            <td><?php 

                                                foreach($product_sold as $prod_detail){

                                                    $soldprod_actual_p = empty($prod_detail['actual_price'])?$prod_detail['unit_price']:$prod_detail['actual_price'];
                                                    if(CURRENCY_POSITION == 'Before') {
                                                        echo '<p>'. CURRENCY_SYMBOL.' '.number_format(($soldprod_actual_p * CURRENCY_VALUE),2) .'</p><hr>' ;
                                                        
                                                    } else {
                                                        echo '<p>'. number_format(($soldprod_actual_p * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL .'</p><hr>';
                                                    
                                                    }
                                                }
                                            ?>
                                            </td>
                                            <td><?php 

                                                foreach($product_sold as $prod_detail){

                                                    if(CURRENCY_POSITION == 'Before') {
                                                        echo '<p>'. CURRENCY_SYMBOL.' '.number_format(($prod_detail['unit_price'] * CURRENCY_VALUE),2) .'</p><hr>';
                                                        
                                                    } else {
                                                        echo '<p>'. number_format(($prod_detail['unit_price'] * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL .'</p><hr>';
                                                    
                                                    }
                                                }
                                            ?>
                                            </td>

                                        </tr>
   
                                    <?php  } ?>
                                </tbody>
                            </table>
                                                
                            <!-- Modal -->
                            <?php foreach($user_product_sold_today as $payment_details){ ?>
                                <div class="modal fade" id="sold_transaction_modal_<?php echo $payment_details['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="sold_transaction_modalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            
                                            <h5 class="modal-title col-sm-9" id="sold_transaction_modalLabel">Transaction Details</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div>
                                                <p><label>Customer Name   :</label> <?php echo $payment_details['customer_name'];  ?></p>
                                                <p><label>Customer Email  :</label> <?php echo $payment_details['customer_email']; ?></p>
                                                <p><label>Customer Phone  :</label> <?php echo $payment_details['customer_phone']; ?></p>
                                                
                                            </div>
                                            
                                            <table class="table table-bordered table-hover table-responsive table-striped">
                                            <tr>
                                                <td>Payment Method</td>
                                                <td><?php echo $payment_details['payment_method']; ?> </td>
                                            </tr>
                                            <tr>
                                                <td>Paid Amount</td>
                                                <td><?php 
                                                $total_pay_amt = $payment_details['currency'].' '.number_format($payment_details['paid_amount'],2);
                                                
                                                    echo $total_pay_amt;
                                                ?> </td>
                                            </tr>
                                            <?php 
                                            
                                            
                                            if($payment_details['payment_method'] == 'PayPal'):

                                                echo '<tr><td>Transaction Id</td><td>'.$payment_details['txnid'].'</td></tr>';

                                            elseif($payment_details['payment_method'] == 'Stripe'):

                                                echo '<tr><td>Transaction Id</td><td>'.$payment_details['txnid'].'</td></tr>
                                                <tr><td>Card number</td><td>'.$payment_details['card_number'].'</td></tr>
                                                <tr><td>Card Expiry</td><td>'.$payment_details['card_month'].'/'.$payment_details['card_year'].'</td></tr>';

                                            elseif($payment_details['payment_method'] == 'Bank Deposit'):
                                                echo '<tr><td>Transaction Details</td><td>'.$payment_details['bank_transaction_info'].'</td></tr>';
                                
                                            endif;
                                
                                            
                                            ?>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div> 
                    </div>

                    <div class="tab-pane " id="latest_downloads">
                        <div class="col-md-12">
                            <table id="latest_download_table" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th width="30">S.No.</th>
                                        <th>Image</th>
                                        <th width="200">Model Name</th>
                                        <th>Download Count</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i=0;
                                    $statement = $pdo->prepare("SELECT t1.*,

                                        Count(t1.dc_id) as download_count,

                                        t2.p_name,
                                        t2.p_featured_photo
                                        
                                        FROM tbl_download_count t1

                                        left JOIN tbl_product t2
                                        ON t1.prod_id = t2.p_id

                                        WHERE t2.user_id = ? AND t2.user_type = 'Customer' and t2.is_delete = 0
                                        GROUP BY t1.prod_id
                                        ORDER BY t1.dc_id DESC
                                    ");

                                    $statement->execute([$_SESSION['customer']['cust_id']]);
                                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($result as $row) {
                                        $i++;
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td style="width:130px;"><img src="public_files/uploads/<?php echo $row['p_featured_photo']; ?>" alt="<?php echo $row['p_name']; ?>" style="width:100px;"></td>
                                            
                                            <td><?php echo $row['p_name']; ?></td>
                                            <td><?php echo $row['download_count']; ?></td>
                                            										
                                              
                                        </tr>
                                        <?php
                                    }
                                    ?>							
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane " id="monthly_summary">
                        <div class="col-md-12">
                            
                            <table id="monthly_table" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Sales</th>
                                        <th>Downloads</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $month_wise_total_sale = $pdo->prepare('SELECT SUM(tbl_order.unit_price) as revenue, COUNT(tbl_order.id) as total_sale, MONTHNAME(tbl_payment.payment_date) as month_name,YEAR(tbl_payment.payment_date)as year_name FROM tbl_payment 
                                        LEFT JOIN tbl_order on tbl_payment.payment_id = tbl_order.payment_id
                                        LEFT JOIN tbl_product on tbl_product.p_id = tbl_order.product_id
                                        where tbl_product.user_type = "Customer" and tbl_product.user_id = ? and tbl_payment.payment_status = "Completed" GROUP BY MONTH(tbl_payment.payment_date), YEAR(tbl_payment.payment_date) ');
                                        $month_wise_total_sale->execute([$_SESSION['customer']['cust_id']]);
                                        $month_wise_sale = $month_wise_total_sale->fetchAll(PDO::FETCH_ASSOC);

                                        
                                        $month_wise_total_download = $pdo->prepare('SELECT COUNT(tbl_download_count.dc_id) as total_download, MONTHNAME(tbl_download_count.created_at) as month_name,YEAR(tbl_download_count.created_at)as year_name FROM tbl_download_count 
                                        LEFT JOIN tbl_product on tbl_product.p_id = tbl_download_count.prod_id
                                        where tbl_product.user_type = "Customer" and tbl_product.user_id = ?  GROUP BY MONTH(tbl_download_count.created_at) , YEAR(tbl_download_count.created_at) ');
                                        $month_wise_total_download->execute([$_SESSION['customer']['cust_id']]);
                                        $month_wise_download = $month_wise_total_download->fetchAll(PDO::FETCH_ASSOC);

                                        $new_monthwise_arr = array_merge($month_wise_sale,$month_wise_download);

                                        $total_m_w = [];
                                        foreach($new_monthwise_arr as $month_ind => $month_wise){
                                            
                                            $key_form = $month_wise['year_name'].'-'.$month_wise['month_name'];
                                            
                                            if(!isset($total_m_w[$key_form])){
                                                $total_m_w[$key_form] = $month_wise;
                                            }else{
                                                $nonEmptyValues = array_filter($month_wise, function($w) { return !empty($w); });
                                                $total_m_w[$key_form] = array_merge($total_m_w[$key_form], $nonEmptyValues);
    
                                            }
                                        }
                                        
                                        foreach($total_m_w as $my => $month_summ){
                                    ?>

                                    <tr>
                                        <td>
                                            <?php echo date('d-m-Y',strtotime($my.'-01')).' to '.date('t-m-Y',strtotime($my.'-01')) ?>
                                        </td>
                                        <td>
                                            <?php if(isset($month_summ['total_sale'])){ echo $month_summ['total_sale']; }else{ echo 0; } ?>
                                        </td>
                                        <td>
                                            <?php if(isset($month_summ['total_download'])){ echo $month_summ['total_download']; }else{ echo 0; } ?>
                                        </td>
                                        <td>
                                            <?php if(isset($month_summ['revenue'])){ 
                                                
                                                if(CURRENCY_POSITION == 'Before') {
                                                    echo  CURRENCY_SYMBOL.' '.number_format(($month_summ['revenue'] * CURRENCY_VALUE),2) ;
                                                    
                                                } else {
                                                    echo  number_format(($month_summ['revenue'] * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL ;
                                                
                                                }
                                                
                                            }else{ echo 0; } ?>
                                        </td>
                                    </tr>

                                    <?php } ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="tab-pane " id="payment_info">
                        <div class="col-md-12">
                            <p class="color-grey mb_30"><label>Note:</label> Payment will processed in <?php echo $get_commission['payout_date']; ?> of each month. </p>
                            
                            <table id="payment_table" class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>Payment Date</th>
                                        <th>Payment Method</th>
                                        <th>Transaction ID</th>
                                        <th>Revenue</th>
                                        <th>Payout Fees</th>
                                        <th>Payout Amount</th>
                                        <th>Payout Of Date</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        
                                        $payout_sql = $pdo->prepare('SELECT tbl_payout.* ,tbl_customer.cust_name FROM tbl_payout 	   
                                        LEFT JOIN tbl_customer on tbl_payout.vendor_id = tbl_customer.cust_id 
                                        WHERE tbl_payout.vendor_id = ?
                                        ORDER BY tbl_payout.pay_id DESC');
                                        $payout_sql->execute([$_SESSION['customer']['cust_id']]);
                                        $payouts = $payout_sql->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        foreach($payouts as $index =>$pay){

                                            $get_orders_sql = $pdo->prepare('SELECT tbl_order.*,tbl_payment.payment_date FROM tbl_order 
                                                LEFT JOIN tbl_payment on tbl_payment.payment_id = tbl_order.payment_id
                                                where tbl_order.id IN('.$pay['order_ids'].') ');

											$get_orders_sql->execute();
											$get_orders = $get_orders_sql->fetchAll(PDO::FETCH_ASSOC);

                                            $total_price = 0;
                                            $total_commission =0;
                                            foreach($get_orders as $ind => $order){
												$total_price = $total_price+$order['unit_price'];
                                                $commission = 0;
												if(!empty($get_commission)){
													if($get_commission['setting_type'] == 'percent'){
														
														$calc = $order['unit_price'] * ($get_commission['setting_value']/100);
														$commission = $calc;

														
													}else{
														$commission = $get_commission['setting_value'];
														
													}
													
												}
												$total_commission = $total_commission + $commission;
                                            }
											
                                    ?>
                                    <tr>
                                        <td><?php echo date('d-m-Y h:i A',strtotime($pay['created_at'])); ?></td>
                                        <td><?php echo $pay['transaction_method']; ?> </td>
                                        <td><?php echo $pay['transaction_id']; ?></td>
                                        <td><?php 
                                        if(CURRENCY_POSITION == 'Before') {
                                            echo CURRENCY_SYMBOL.' '.number_format($total_price * CURRENCY_VALUE,2);
                                            
                                        } else {
                                            echo number_format($total_price * CURRENCY_VALUE,2).' '.CURRENCY_SYMBOL ;
                                        
                                        } 
                                        ?></td>
                                        <td><?php 
                                        if(CURRENCY_POSITION == 'Before') {
                                            echo "(-) ". CURRENCY_SYMBOL.' '.number_format($total_commission * CURRENCY_VALUE,2);
                                            
                                        } else {
                                            echo "(-) ".number_format($total_commission * CURRENCY_VALUE,2).' '.CURRENCY_SYMBOL ;
                                        
                                        } ?> </td>
                                        <td><?php 
                                        if(CURRENCY_POSITION == 'Before') {
                                            echo CURRENCY_SYMBOL.' '.number_format($total_price-$total_commission * CURRENCY_VALUE,2);
                                            
                                        } else {
                                            echo number_format($total_price-$total_commission * CURRENCY_VALUE,2).' '.CURRENCY_SYMBOL ;
                                        
                                        } ?></td>
                                        <td><?php echo $pay['payout_month'].' '. $pay['payout_year'];?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <div class="tab-pane " id="discounts">
                        <?php 
                        
                            $check_exist_setting = $pdo->prepare('Select * from tbl_customer_discount_setting where customer_id =?');

                            $check_exist_setting->execute(array($_SESSION['customer']['cust_id']));

                            $setting_first_row = $check_exist_setting->fetch(PDO::FETCH_ASSOC);
                            
                        ?>
                        
                        <div class="col-md-12 ">
                            <h3 class="fw-100 color-grey">Choose if you want to participate in sale offs</h3>
                            <br>
                            <div class="form-group ">
                                
                                <label class="checkbox-inline fs-18"><input type="checkbox" class="input-group" value="1" <?php echo ((!empty($setting_first_row))? (($setting_first_row['participation_bit']==1)?'checked':''):''); ?> style="height:15px;width: 15px;" onclick="sale_discount_participation(this)" > I want to participate in sale offs 
                                   <i class="fa fa-question-circle-o " title="Feature in the site-wide sale off campaigns to increase your revenue" ></i> 
                                </label>

                                <p>Note:This setting has to be checked for your models to be offered in sale offs. The discounts will not be applied if you choose the discount rate only.</p>
                            </div>
                            <hr>
                            <h3 class="fw-100 color-grey">Adjust your discount rate</h3>
                            <br>
                            <div class="form-group row">
                                <p class=" pl_20">Choose the discount percentage you want to display for your customers during sale periods.
                                </p>
                                <form method="post" action="ajax_function.php">

                                    
                                    <div class="col-sm-6 col-md-4 pl_20">
                                        <select class="col-md-4 form-control" name="sale_time_discount_percentage">
                                            <option value="">Select percentage</option>
                                            <option value="30" <?php echo ((!empty($setting_first_row))? (($setting_first_row['discount_rate_sale_period']==30)?'selected':''):''); ?> >30%</option>
                                            <option value="40" <?php echo ((!empty($setting_first_row))? (($setting_first_row['discount_rate_sale_period']==40)?'selected':''):''); ?> >40%</option>
                                            <option value="50" <?php echo ((!empty($setting_first_row))? (($setting_first_row['discount_rate_sale_period']==50)?'selected':''):''); ?> >50%</option>
                                        </select>
                                        
                                    </div>
                                    <div class="col-md-2 col-sm-6">
                                        <input type="submit" value="Confirm" name="sale_time_discount_btn" class=" btn btn-success">
                                    </div>
                                </form>

                            </div>
                            <hr>
                            <h3 class="fw-100 color-grey">Adjust your Super Discount rate</h3>
                            <br>
                            <div class="form-group row">
                                <p class=" pl_20">Choose the discount percentage you want to display <b>during the special sale period</b>. Your preferences will automatically revert to normal after Super Discount period.
                                </p>
                                <form method="post" action="ajax_function.php">

                                    
                                    <div class="col-sm-6 col-md-4 pl_20">
                                        <select class="col-md-4 form-control" name="super_sale_discount_percentage">
                                            <option value="">Select percentage</option>
                                            <option value="30" <?php echo ((!empty($setting_first_row))? (($setting_first_row['discount_rate_supersale_period']==30)?'selected':''):''); ?> >30%</option>
                                            <option value="40" <?php echo ((!empty($setting_first_row))? (($setting_first_row['discount_rate_supersale_period']==40)?'selected':''):''); ?> >40%</option>
                                            <option value="50" <?php echo ((!empty($setting_first_row))? (($setting_first_row['discount_rate_supersale_period']==50)?'selected':''):''); ?> >50%</option>
                                            <option value="60" <?php echo ((!empty($setting_first_row))? (($setting_first_row['discount_rate_supersale_period']==60)?'selected':''):''); ?> >60%</option>
                                            <option value="70" <?php echo ((!empty($setting_first_row))? (($setting_first_row['discount_rate_supersale_period']==70)?'selected':''):''); ?> >70%</option>
                                        </select>
                                        
                                    </div>
                                    <div class="col-md-2 col-sm-6">
                                        <input type="submit" value="Confirm" name="super_sale_discount_btn" class=" btn btn-success">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
<style>
    .nav_background{
        background: transparent !important;
    }
</style>
<script>

        var selectedTab =  window.location.href.split("#")[1] ;
        
        if (selectedTab == undefined) {
            $('a[href$=latest_sale]:first').trigger('click');
        }
        else{
            $('a[href$=' + selectedTab+']:first').trigger("click");
        }
        
        setTimeout(function() {
            $(".sale-setting-alert").remove();
        }, 10000);


        function sale_discount_participation(check_input){
            var checkbox = check_input.checked;
            var participation_bit = 0;
            if(checkbox){
                participation_bit = 1;
            }
            $.ajax({
                method: 'POST',
                url: 'ajax_function.php',
                data:{'sale_participation_bit':participation_bit},
                type: 'json',
                success:function(response){
                    response = JSON.parse(response);
                    $("#alert-div").empty();

                    if(response['status'] == 'success'){
                        
                        $("#alert-div").append('<div class="alert alert-success sale-setting-alert"> <p>'+ response['msg'] +' </p> </div>');

                        setTimeout(function() {
                            $(".sale-setting-alert").remove();
                        }, 10000);

                    }else{

                        $("#alert-div").append('<div class="alert alert-danger sale-setting-alert"> <p>'+ response['msg'] +' </p> </div>');

                        setTimeout(function() {
                            $(".sale-setting-alert").remove();
                        }, 10000);

                    }
                }
            })
        }

</script>
<?php require_once('footer.php'); ?>