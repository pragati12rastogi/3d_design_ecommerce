<?php
ob_start();
session_start();
require_once('../../admin/inc/config.php');

$error_message = '';

$statement = $pdo->prepare("SELECT * FROM tbl_setting_payment WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$paypal_email   = $row['paypal_email'];
	$paypal_sandbox = $row['paypal_sandbox'];
}

$return_url = BASE_URL.'../../payment_success.php';
$cancel_url = BASE_URL.'../../payment_cancel.php';
$notify_url = BASE_URL.'verify_process.php';

$item_name = 'Product Item(s)';
$item_amount = $_POST['final_total'];
$payout_fee = $_POST['paypal_fee'];
$item_number = time();

$payment_date = date('Y-m-d H:i:s');

// Check if paypal request or response
if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])){

	$pdo->begintransaction();

	$querystring = '';
	
	// Firstly Append paypal account to querystring
	$querystring .= "?business=".urlencode($paypal_email)."&";
	
	// Append amount& currency (£) to quersytring so it cannot be edited in html
	
	//The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.
	$querystring .= "item_name=".urlencode($item_name)."&";
	$querystring .= "amount=".urlencode($item_amount+$payout_fee)."&";
	$querystring .= "item_number=".urlencode($item_number)."&";
	
	//loop for posted values and append to querystring
	foreach($_POST as $key => $value){
		$value = urlencode(stripslashes($value));
		$querystring .= "$key=$value&";
	}
	
	// Append paypal return addresses
	$querystring .= "return=".urlencode(stripslashes($return_url))."&";
	$querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
	$querystring .= "notify_url=".urlencode($notify_url);
	
	// Append querystring with custom field
	//$querystring .= "&custom=".USERID;

	$statement = $pdo->prepare("INSERT INTO tbl_payment (
						customer_id,
						customer_name,
						customer_email,
						customer_phone,
						payment_date,
						txnid, 
						payout_fee,
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
						) 
						VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	$sql = $statement->execute(array(
						$_SESSION['customer']['cust_id'],
						$_SESSION['customer']['cust_name'],
						$_SESSION['customer']['cust_email'],
						$_SESSION['customer']['cust_phone'],
						$payment_date,
						'',
						$payout_fee,
						$item_amount,
						'',
						'',
						'',
						'',
						'',
						'PayPal',
						'USD',
						'Pending',
						'Pending',
						$item_number
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


    $i=0;
    $statement = $pdo->prepare("SELECT * FROM tbl_product where is_delete = 0");
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);							
    foreach ($result as $row) {
    	$i++;
    	$arr_p_id[$i] = $row['p_id'];
    	
    }


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
						$item_number
					));

    }

	
	if($sql){
		
		$pdo->commit();
		// Redirect to paypal IPN
		if($paypal_sandbox){
		
			header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);
		
		}else{
			header('location:https://www.paypal.com/cgi-bin/webscr'.$querystring);
		}

		exit();
	}else{
		$pdo->rollback();
		exit();
	}
	
} else {

	// Response from Paypal

	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix
		$req .= "&$key=$value";
	}
	
	// assign posted variables to local variables
	$data['item_name']			= $_POST['item_name'];
	$data['payment_status'] 	= $_POST['payment_status'];
	$data['payment_amount'] 	= $_POST['mc_gross'];
	$data['payment_currency']	= $_POST['mc_currency'];
	$data['txn_id']			    = $_POST['txn_id'];
	$data['receiver_email'] 	= $_POST['receiver_email'];
	$data['payer_email'] 		= $_POST['payer_email'];
	$data['custom'] 			= $_POST['custom'];
			
	// post back to PayPal system to validate
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	
		
	$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
	
	if (!$fp) {
		// HTTP ERROR
		
	} else {
		fputs($fp, $header . $req);
		while (!feof($fp)) {
			$res = fgets ($fp, 1024);
			if (strcmp($res, "VERIFIED") == 0) {
				
				// Used for debugging
				// mail('user@domain.com', 'PAYPAL POST - VERIFIED RESPONSE', print_r($post, true));
				
			
			} else if (strcmp ($res, "INVALID") == 0) {
			

				// PAYMENT INVALID & INVESTIGATE MANUALY!
				// E-mail admin or alert user
				
				// Used for debugging
				//@mail("user@domain.com", "PAYPAL DEBUGGING", "Invalid Response<br />data = <pre>".print_r($post, true)."</pre>");
			}
		}
	fclose ($fp);
	}
}