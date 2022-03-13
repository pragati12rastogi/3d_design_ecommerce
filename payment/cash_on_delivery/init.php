<?php
ob_start();
session_start();
include("../../admin/inc/config.php");
include("../../admin/inc/functions.php");
include("../../send_email_function.php");

$currency_code = 'USD';
?>
<?php
if( !isset($_REQUEST['msg']) ) 
{
	
	$payment_date = date('Y-m-d H:i:s');
    $payment_id = time();

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
                            '',
                            $_POST['amount'],
                            '', 
                            '',
                            '', 
                            '',
                            '',
                            'Cash On Delivery',
                            $currency_code,
                            'Pending',
                            'N/A',
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
    unset($_SESSION['cart_p_id']);
    unset($_SESSION['cart_p_current_price']);
    unset($_SESSION['cart_p_old_price']);
    unset($_SESSION['cart_p_name']);
    unset($_SESSION['cart_p_featured_photo']);

    order_placement_email($pdo,$mail,$payment_id);

    header('location: ../../payment_success_cod.php');
}
?>