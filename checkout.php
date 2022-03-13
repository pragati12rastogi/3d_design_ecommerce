<?php require_once('header.php'); ?>

<?php
unset($_SESSION['from_page']);
?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $banner_checkout = $row['banner_checkout'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_payment WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $paypal_status = $row['paypal_status'];
    $stripe_status = $row['stripe_status'];
    $bank_status = $row['bank_status'];
    $cash_on_delivery_status = $row['cash_on_delivery_status'];
}
?>

<?php
if(!isset($_SESSION['cart_p_id'])) {
    header('location: cart.php');
    exit;
}
?>

<div class="page-banner" style="background-image: url(public_files/uploads/<?php echo $banner_checkout; ?>)">
    <div class="overlay"></div>
    <div class="page-banner-inner">
        <h1><?php echo CHECKOUT; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                
                <?php if(!isset($_SESSION['customer'])): ?>
                    <form action="login.php" method="post">
                        <input type="hidden" name="from_page" value="checkout">
                    <p>
                        <button type="submit" class="btn btn-md btn-danger" name="form_from_page"><?php echo PLEASE_LOGIN_AS_CUSTOMER_TO_CHECKOUT; ?></button>
                    </p>
                    </form>
                <?php else: ?>
                
                    <div class="cart-buttons">
                        <a href="cart.php" class="btn btn-primary"><?php echo BACK_TO_CART; ?></a></li>
                        
                    </div>

                    <h3 class="special"><?php echo ORDER_DETAILS; ?></h3>
                    <div class="cart">
                        <table class="table table-responsive">
                            <tr>
                                <th><?php echo SERIAL; ?></th>
                                <th><?php echo PHOTO; ?></th>
                                <th><?php echo PRODUCT_NAME; ?></th>
                                <th><?php echo ACTUAL_PRICE; ?></th>
                                <th><?php echo DISCOUNT; ?></th>
                                <th><?php echo PRICE; ?></th>

                            </tr>
                            <?php
                            $table_total_price = 0;
                            $total_ofdiscount_amt =0;

                            $arr_cart_p_old_price = $_SESSION['cart_p_old_price'];
                            $i=0;
                            foreach($_SESSION['cart_p_id'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_p_id[$i] = $value;
                            }

                            $i=0;
                            foreach($_SESSION['cart_p_current_price'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_p_current_price[$i] = $value;
                            }

                            $i=0;
                            foreach($_SESSION['cart_p_name'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_p_name[$i] = $value;
                            }

                            $i=0;
                            foreach($_SESSION['cart_p_featured_photo'] as $key => $value) 
                            {
                                $i++;
                                $arr_cart_p_featured_photo[$i] = $value;
                            }
                            ?>
                            <?php for($i=1;$i<=count($arr_cart_p_id);$i++): ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td>
                                    <img src="public_files/uploads/<?php echo $arr_cart_p_featured_photo[$i]; ?>" alt="">
                                </td>
                                <td><?php echo $arr_cart_p_name[$i]; ?></td>
                                
                                <td>
                                    <?php
                                    if(!empty($arr_cart_p_old_price[$i])){

                                        if(CURRENCY_POSITION == 'Before') {
                                            echo CURRENCY_SYMBOL;
                                            echo number_format($arr_cart_p_old_price[$i] * CURRENCY_VALUE,2);
                                        } else {
                                            echo number_format($arr_cart_p_old_price[$i] * CURRENCY_VALUE,2);
                                            echo CURRENCY_SYMBOL;
                                        }

                                    }else{

                                        if(CURRENCY_POSITION == 'Before') {
                                            echo CURRENCY_SYMBOL;
                                            echo number_format($arr_cart_p_current_price[$i] * CURRENCY_VALUE,2);
                                        } else {
                                            echo number_format($arr_cart_p_current_price[$i] * CURRENCY_VALUE,2);
                                            echo CURRENCY_SYMBOL;
                                        }

                                    }
                                    
                                    ?>
                                </td>
                                
                                <td>
                                    <?php

                                    if(!empty($arr_cart_p_old_price[$i])){

                                        $discount_amt = $arr_cart_p_old_price[$i]-$arr_cart_p_current_price[$i];
                                    
                                    }else{
                                        $discount_amt = $arr_cart_p_current_price[$i]-$arr_cart_p_current_price[$i];
                                    
                                    }
                                    if(CURRENCY_POSITION == 'Before') {
                                        echo CURRENCY_SYMBOL;
                                        echo number_format($discount_amt * CURRENCY_VALUE,2);
                                    } else {
                                        echo number_format($discount_amt * CURRENCY_VALUE,2);
                                        echo CURRENCY_SYMBOL;
                                    }

                                    $total_ofdiscount_amt = $total_ofdiscount_amt + $discount_amt;
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    if(CURRENCY_POSITION == 'Before') {
                                        echo CURRENCY_SYMBOL;
                                        echo number_format(($arr_cart_p_current_price[$i]* CURRENCY_VALUE),2) ;
                                    } else {
                                        echo number_format($arr_cart_p_current_price[$i]* CURRENCY_VALUE,2) ;
                                        echo CURRENCY_SYMBOL;
                                    }

                                    $table_total_price = $table_total_price + $arr_cart_p_current_price[$i];
                                    ?>
                                </td>
                                
                                
                            </tr>
                            <?php endfor; ?>           
                            
                            <tr>
                                <th colspan="5" class="total-text"><?php echo TOTAL. ' '. DISCOUNT; ?></th>
                                <th class="total-amount">
                                    <?php
                                    $final_discount_total = $total_ofdiscount_amt * CURRENCY_VALUE;
                                    ?>
                                    <?php
                                    if(CURRENCY_POSITION == 'Before') {
                                        echo '- '.CURRENCY_SYMBOL;
                                        echo  number_format($final_discount_total,2);
                                    } else {
                                        echo '- '. number_format($final_discount_total,2);
                                        echo CURRENCY_SYMBOL;
                                    }
                                    ?>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="5" class="total-text"><?php echo TOTAL; ?></th>
                                <th class="total-amount">
                                    <?php
                                    $final_total = $table_total_price * CURRENCY_VALUE;
                                    ?>
                                    <?php
                                    if(CURRENCY_POSITION == 'Before') {
                                        echo CURRENCY_SYMBOL;
                                        echo number_format($final_total,2);
                                    } else {
                                        echo number_format($final_total,2);
                                        echo CURRENCY_SYMBOL;
                                    }
                                    ?>
                                </th>
                            </tr>
                        </table> 
                    </div>

                
                

                    <div class="clear"></div>
                    <h3 class="special"><?php echo PAYMENT_SECTION; ?></h3>
                    <div class="row">
                        
                        <div class="col-md-4">
                                    
                            <div class="row">

                                <div class="col-md-12 form-group">
                                    <label for=""><?php echo SELECT_PAYMENT_METHOD; ?> *</label>
                                    <div>
                                        <?php if($paypal_status == 'Active'): ?>
                                            <label class="radio-inline">
                                                <input type="radio" name="payment_method" class="advFieldsStatus" value="PayPal"> <?php echo PAYPAL; ?> 
                                            </label>
                                        <?php endif; ?>

                                        <?php if($stripe_status == 'Active'): ?>
                                            <label class="radio-inline">
                                                <input type="radio" name="payment_method" class="advFieldsStatus" value="Stripe" ><?php echo STRIPE; ?>
                                            </label>
                                        <?php endif; ?>
                                    </div>
                                    
                                </div>
                                
                                <?php if($paypal_status == 'Active'): ?>
                                <form class="paypal" action="<?php echo BASE_URL; ?>payment/paypal/payment_process.php" method="post" id="paypal_form" target="_blank">
                                    <input type="hidden" name="cmd" value="_xclick">
                                    <input type="hidden" name="no_note" value="1">
                                    <input type="hidden" name="lc" value="IND">
                                    <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest">
                                    <?php
                                    
                                    if(isset($_SESSION['setCurrency'])){
                                        $statement = $pdo->prepare("SELECT * FROM tbl_setting_currency WHERE id=".$_SESSION['setCurrency']);
                                    }else{
                                        $statement = $pdo->prepare("SELECT * FROM tbl_setting_currency WHERE default_currency=1");
                                    }
                                    $statement->execute();
                                    $paypal_multi_curr = $statement->fetch(PDO::FETCH_ASSOC);
                                    
                                    $currency_code = $paypal_multi_curr['currency_code'];
                                    
                                    if($currency_code == 'usd' || $currency_code == 'USD'){
                                        $paypalfinal_total = $final_total/$paypal_multi_curr['currency_value_per_usd'];
                                    }else{
                                        $paypalfinal_total = $final_total/$paypal_multi_curr['currency_value_per_usd'];
                                    }
                                    $paypalfinal_total = round($paypalfinal_total,2);
                                    
                                    
                                    $paypal_fee = $paypalfinal_total * (4.4/100)+0.30;
                                    
                                    ?>
                                    <input type="hidden" name="currency_code" value="USD">
                                    <input type="hidden" name="final_total" value="<?php echo $paypalfinal_total; ?>">
                                    <input type="hidden" name="paypal_fee" value="<?php echo round($paypal_fee,2); ?>">
                                    
                                    <div class="col-md-12 form-group">
                                        <p>Paypal Standard fee Added : <span style="font-weight:bold">
                                        <?php 
                                            if(CURRENCY_POSITION == 'Before') {
                                                echo CURRENCY_SYMBOL;
                                                echo number_format($paypal_fee,2);
                                            } else {
                                                echo number_format($paypal_fee,2);
                                                echo CURRENCY_SYMBOL;
                                            }
                                        ?></span></p>
                                        <p>Total Amount : <span style="font-weight:bold">
                                        <?php 
                                            if(CURRENCY_POSITION == 'Before') {
                                                echo CURRENCY_SYMBOL;
                                                echo number_format($paypalfinal_total+$paypal_fee,2);
                                            } else {
                                                echo number_format($paypalfinal_total+$paypal_fee,2);
                                                echo CURRENCY_SYMBOL;
                                            }
                                        ?></span></p>
                                        <input type="submit" class="btn btn-primary" value="<?php echo PAY_NOW; ?>" name="form1">
                                    </div>
                                </form>
                                <?php endif; ?>
                                

                                <?php if($stripe_status == 'Active'): ?>
                                <div class="col-md-12 ml_10" id="stripe_form_div" >
                                    <div class="card row">
                                        <div class="card-header">
                                            <h4>Card Details</h4>
                                        </div>
                                        <div class="card-body pt_20">
                                            <form action="payment/stripe/init.php" method="post" id="stripe_form">
                                                <input type="hidden" name="payment" value="posted">
                                                <input type="hidden" name="currency_code" value="USD">
                                                 
                                                <input type="hidden" name="amount" value="<?php echo $paypalfinal_total; ?>">
                                                <div class="col-md-12 form-group">
                                                    <label for=""><?php echo CARD_NUMBER; ?> *</label>
                                                    <input type="text" name="card_number" class="form-control card-number">
                                                </div>
                                                
                                                <div class="col-md-4 form-group">
                                                    <label for=""><?php echo MONTH; ?> *</label>
                                                    <input type="text" name="card_month" class="form-control card-expiry-month" placeholder="DD">
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label for=""><?php echo YEAR; ?> *</label>
                                                    <input type="text" name="card_year" class="form-control card-expiry-year" placeholder="YYYY">
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label for=""><?php echo CVV; ?> *</label>
                                                    <input type="text" name="card_cvv" class="form-control card-cvc" placeholder="123">
                                                </div>
                                                <div class="col-md-12 form-group">
                                                    <input type="submit" class="btn btn-primary btn-block" value="<?php echo PAY_NOW; ?>" name="form2" id="submit-button">
                                                    <div id="msg-container"></div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>


                                <?php if($bank_status == 'Active'): ?>
                                <form action="payment/bank/init.php" method="post" id="bank_form">
                                    <input type="hidden" name="amount" value="<?php echo $paypalfinal_total; ?>">
                                    <div class="col-md-12 form-group">
                                        <label for=""><?php echo SEND_TO_THIS_DETAILS; ?></span></label><br>
                                        <?php
                                        $statement = $pdo->prepare("SELECT * FROM tbl_setting_payment WHERE id=1");
                                        $statement->execute();
                                        $result = $statement->fetchAll();
                                        foreach ($result as $row) {
                                            echo nl2br($row['bank_detail']);
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for=""><?php echo TRANSACTION_INFORMATION; ?> <br><span style="font-size:12px;font-weight:normal;">(<?php echo INCLUDE_TXN_ID_AND_OTHER_INFORMATION_CORRECTLY; ?>)</span></label>
                                        <textarea name="transaction_info" class="form-control" cols="30" rows="10"></textarea>
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <input type="submit" class="btn btn-primary" value="<?php echo PAY_NOW; ?>" name="form3">
                                    </div>
                                </form>
                                <?php endif; ?>
                                

                                <?php if($cash_on_delivery_status == 'Active'): ?>
                                <form action="payment/cash_on_delivery/init.php" method="post" id="cash_on_delivery_form">
                                    <input type="hidden" name="amount" value="<?php echo $paypalfinal_total; ?>">
                                    <div class="col-md-12 form-group">
                                        <input type="submit" class="btn btn-primary" value="<?php echo SUBMIT; ?>" name="form4">
                                    </div>
                                </form>
                                <?php endif; ?>



                                
                            </div>
                                        
                        </div>
                            
                    </div>
                

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<style>
    .card-header{
        padding: 5px 20px;
        border-bottom: 1px solid #9b9b9b;
    }
</style>
<?php require_once('footer.php'); ?>