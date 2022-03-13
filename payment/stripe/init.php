<?php
ob_start();
session_start();
include("../../admin/inc/config.php");
include("../../admin/inc/functions.php");
?>

<?php require 'lib/init.php';
include("../../send_email_function.php");
?>

<?php

$statement = $pdo->prepare("SELECT * FROM tbl_setting_payment WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $stripe_public_key = $row['stripe_public_key'];
    $stripe_secret_key = $row['stripe_secret_key'];
}
if (isset($_POST['payment']) && $_POST['payment'] == 'posted' && floatval($_POST['amount']) > 0) {

    \Stripe\Stripe::setApiKey($stripe_secret_key);
    try {
        if (!isset($_POST['stripeToken'])){
            throw new Exception("The Stripe Token was not generated correctly");
        }
        
        $currency_code = $_POST['currency_code'];

        $payment_date = date('Y-m-d H:i:s');
        $payment_id = time();
        $amount = floatval($_POST['amount']);
        $cents = floatval($amount * 100); //converting to cents

        $response = \Stripe\Charge::create(array("amount" => $cents,
                    "currency" => $currency_code,
                    "card" => $_POST['stripeToken'],
                    "description" => 'Stripe Test Payment'
        ));

        $transaction_id = $response->id; // Its unique charge ID
        $transaction_status = $response->status;
        $statement = $pdo->prepare("INSERT INTO tbl_payment (   
                                customer_id,
                                customer_name,
                                customer_email,
                                customer_phone,
                                payment_date,
                                txnid, 
                                paid_amount,
                                card_number,
                                card_cvv,
                                card_month,
                                card_year,
                                bank_transaction_info,
                                payment_method,
                                currency,
                                payment_status,
                                shipping_status,
                                payment_id
                            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $statement->execute(array(
                                $_SESSION['customer']['cust_id'],
                                $_SESSION['customer']['cust_name'],
                                $_SESSION['customer']['cust_email'],
                                $_SESSION['customer']['cust_phone'],
                                $payment_date,
                                $transaction_id,
                                $_POST['amount'],
                                $_POST['card_number'], 
                                md5($_POST['card_cvv']), 
                                $_POST['card_month'], 
                                $_POST['card_year'],
                                '',
                                'Stripe',
                                $currency_code,
                                'Completed',
                                'Pending',
                                $payment_id
                            ));

        $i=0;
        foreach($_SESSION['cart_p_id'] as $key => $value) 
        {
            $i++;
            $arr_cart_p_id[$i] = $value;
        }

        $i=0;
        foreach($_SESSION['cart_p_name'] as $key => $value) 
        {
            $i++;
            $arr_cart_p_name[$i] = $value;
        }

        $i=0;
        foreach($_SESSION['cart_p_current_price'] as $key => $value) 
        {
            $i++;
            $arr_cart_p_current_price[$i] = $value;
        }

        $arr_cart_p_old_price = $_SESSION['cart_p_old_price'];

        

        for($i=1;$i<=count($arr_cart_p_name);$i++) {
            $statement = $pdo->prepare("INSERT INTO tbl_order (
                            product_id,
                            product_name,
                            actual_price,  
                            unit_price, 
                            payment_id
                            ) 
                            VALUES (?,?,?,?,?)");
            $sql = $statement->execute(array(
                            $arr_cart_p_id[$i],
                            $arr_cart_p_name[$i],
                            $arr_cart_p_old_price[$i],
                            $arr_cart_p_current_price[$i],
                            $payment_id
                        ));
        }

        if($sql){

            unset($_SESSION['cart_p_id']);
            unset($_SESSION['cart_p_current_price']);
            unset($_SESSION['cart_p_old_price']);
            unset($_SESSION['cart_p_name']);
            unset($_SESSION['cart_p_featured_photo']);

            order_placement_email($pdo,$mail,$payment_id); 

            header('location: ../../payment_success.php');
        }else{
            alert('Some error occurred');
            header('location: ../../checkout.php');
        }
        

    } catch (Exception $e) {
        $error = $e->getMessage();
        ?><script type="text/javascript">alert('Error: <?php echo $error; ?>');</script><?php
    }
}

?>