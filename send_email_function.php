<?php 

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


function order_placement_email($pdo,$mail,$payment_id){
    
    $statement = $pdo->prepare("SELECT * FROM tbl_setting_email WHERE id=1");
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);                           
    
    $send_email_from = $result['send_email_from'];
    $receive_email_to = $result['receive_email_to'];
    
    // customer buyer details
    $get_buyer_details_sql = $pdo->prepare('SELECT 
        tbl_payment.customer_name as buyer_name,
        tbl_payment.customer_email as buyer_email,
        tbl_payment.customer_phone as buyer_phone,

        tbl_payment.paid_amount,
        tbl_payment.currency,
        tbl_payment.payment_method

        from tbl_payment 
        where tbl_payment.payment_id = ?');
    $get_buyer_details_sql->execute([$payment_id]);
    $get_buyer_details = $get_buyer_details_sql->fetch(PDO::FETCH_ASSOC);
    
    // customer vendor detail
    $get_vendor_details_sql = $pdo->prepare('SELECT 
        tbl_product.user_type,

        tbl_customer.cust_name as vendor_cust_name,
        tbl_customer.cust_email as vendor_cust_email,
        tbl_customer.cust_phone as vendor_cust_phone
        from tbl_order 
        left join tbl_product on tbl_product.p_id = tbl_order.product_id
        left join tbl_customer on tbl_product.user_type = "Customer" and tbl_customer.cust_id = tbl_product.user_id
        where tbl_order.payment_id = ?');

    $get_vendor_details_sql->execute([$payment_id]);
    $get_vendor_details = $get_vendor_details_sql->fetch(PDO::FETCH_ASSOC);
    

    $to_buyer_email = $get_buyer_details['buyer_email'];
    $to_buyer_name = $get_buyer_details['buyer_name'];



    $subject = 'Thankyou For Your Lovely Purchase';
    $message = '
    <html><body>
    <p>Thank You <b>'.$to_buyer_name.'</b>,</p>
    <p>For choosing us and being so awesome all at once.</p>
    <p> This is your payment Id : <b>'.$payment_id.'</b></p>
    <p>
        Your Design is present in your purchase section in dashboard.
        Checkout out for more design <a href="'.BASE_URL.'" target="_blank">Here</a>
        Having any query reach out to us through our Contact Us Page.
    </p>
    <p>
        Happy Shopping!!
    </p>
    </body></html>
    ';
    
	
    $mail->setFrom($send_email_from);
    $mail->addAddress($to_buyer_email, $to_buyer_name);
    
    if($get_vendor_details['user_type'] == 'Customer'){
        $mail->addReplyTo($get_vendor_details['vendor_cust_email'],$get_vendor_details['vendor_cust_name']);
        $mail->addCC($receive_email_to);
    }else{
        $mail->addReplyTo($receive_email_to);
    }
    

    $mail->Subject = $subject;

    $mail_body = $message;
    $mail->MsgHTML($mail_body);
    $mail->Send();

	
    if($get_vendor_details['user_type'] == 'Customer'){
        $mail->addAddress($receive_email_to);
        $mail->addAddress($get_vendor_details['vendor_cust_email'],$get_vendor_details['vendor_cust_name']);
        
    }else{
        $mail->addAddress($receive_email_to);
    }

    $message2 = '<html><body>
        <b>Purchase Made By:</b><br>'.$to_buyer_name.'<br><br>
        <b>Buyer Email:</b><br>'.$to_buyer_email.'<br><br>
        <b>Buyer made a payment of:</b><br>'.$get_buyer_details['currency'].' '.$get_buyer_details['paid_amount'].'<br><br>
        <b>Payment Method:</b><br>'.$get_buyer_details['payment_method'].'
        </body></html>';

        $mail->setFrom($send_email_from);
        $mail->addReplyTo($receive_email_to);
        $mail->Subject = 'Order placed with payment Id'.$payment_id;

        $mail_body = $message2;
        $mail->MsgHTML($mail_body);
        $mail->Send();
     
}

?>

