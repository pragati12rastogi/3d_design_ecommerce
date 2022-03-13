<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_email WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row)
{
    $send_email_from  = $row['send_email_from'];
    $receive_email_to = $row['receive_email_to'];
    $smtp_active      = $row['smtp_active'];
    $smtp_ssl         = $row['smtp_ssl'];
    $smtp_host        = $row['smtp_host'];
    $smtp_port        = $row['smtp_port'];
    $smtp_username    = $row['smtp_username'];
    $smtp_password    = $row['smtp_password'];
}

require '../public_files/mail/PHPMailer.php';
require '../public_files/mail/Exception.php';
require '../public_files/mail/SMTP.php';
$mail = new PHPMailer\PHPMailer\PHPMailer();

if($smtp_active == 'Yes')
{
    if($smtp_ssl == 'Yes')
    {
        $mail->SMTPSecure = "ssl";
    }else{
        $mail->SMTPSecure = "tls";
    }
    $mail->SMTPDebug =  PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
    $mail->IsSMTP();
    $mail->SMTPAuth   = true;
    $mail->Host       = $smtp_host;
    $mail->Port       = $smtp_port;
    $mail->Username   = $smtp_username;
    $mail->Password   = $smtp_password; 
}
?>

<?php
$error_message = '';
if(isset($_POST['form1'])) {
    $valid = 1;
    if(empty($_POST['subject_text'])) {
        $valid = 0;
        $error_message .= 'Subject can not be empty\n';
    }
    if(empty($_POST['message_text'])) {
        $valid = 0;
        $error_message .= 'Message can not be empty\n';
    }
    if($valid == 1) {

        $subject_text = strip_tags($_POST['subject_text']);
        $message_text = strip_tags($_POST['message_text']);

        // Getting Customer Email Address
        $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id=?");
        $statement->execute(array($_POST['cust_id']));
        $result = $statement->fetchAll();                            
        foreach ($result as $row) {
            $cust_email = $row['cust_email'];
            $cust_name = $row['cust_name'];
        }

        $order_detail = '';
        $statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_id=?");
        $statement->execute(array($_POST['payment_id']));
        $result = $statement->fetchAll();                            
        foreach ($result as $row) {
        	
        	if($row['payment_method'] == 'PayPal'):
        		$payment_details = '
                Transaction Id: '.$row['txnid'].'<br>
        		';
        	elseif($row['payment_method'] == 'Stripe'):
				$payment_details = '
                Transaction Id: '.$row['txnid'].'<br>
                Card number: '.$row['card_number'].'<br>
                
        		';
        	elseif($row['payment_method'] == 'Bank Deposit'):
				$payment_details = '
                Transaction Details: <br>'.$row['bank_transaction_info'];
            elseif($row['payment_method'] == 'Cash On Delivery'):
                $payment_details = 'N/A';
        	endif;

            $order_detail .= '
                Customer Name: '.$row['customer_name'].'<br>
                Customer Email: '.$row['customer_email'].'<br>
                Customer Phone: '.$row['customer_phone'].'<br>
                Payment Method: '.$row['payment_method'].'<br>
                Payment Date: '.$row['payment_date'].'<br>
                Payment Details: <br>'.$payment_details.'<br>
                Paid Amount: '.$row['paid_amount'].'<br>
                Payment Status: '.$row['payment_status'].'<br>
                Shipping Status: '.$row['shipping_status'].'<br>
                Payment Id: '.$row['payment_id'].'<br>
            ';
        }

        $i=0;
        $statement = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
        $statement->execute(array($_POST['payment_id']));
        $result = $statement->fetchAll();                            
        foreach ($result as $row) {
            $i++;
            $order_detail .= '
                <br><b><u>Product Item '.$i.'</u></b><br>
                Product Name: '.$row['product_name'].'<br>
                Unit Price: '.$row['unit_price'].'<br>
            ';
        }

        $statement = $pdo->prepare("INSERT INTO tbl_customer_message (subject,message,order_detail,cust_id) VALUES (?,?,?,?)");
        $statement->execute(array($subject_text,$message_text,$order_detail,$_POST['cust_id']));

        // sending email
        $to_customer = $cust_email;
        $message = '
                <html><body>
                <h3>Message: </h3>
                '.$message_text.'
                <h3>Order Details: </h3>
                '.$order_detail.'
                </body></html>
                ';
        

        try {

            $mail->CharSet = 'UTF-8';
            
            $mail->setFrom($send_email_from);
            $mail->addAddress($to_customer, $cust_name);
            $mail->addReplyTo($receive_email_to);
            
            $mail->isHTML(true);
            $mail->Subject = $subject_text;

            $mail->MsgHTML($message);
            $mail->Send();

            $success_message = 'Your email to customer is sent successfully.';   
        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
        
        

    }
}
?>
<?php
if($error_message != '') {
    echo "<script>alert('".$error_message."')</script>";
}
if($success_message != '') {
    echo "<script>alert('".$success_message."')</script>";
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Orders</h1>
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
			        <th>SL</th>
                    <th>Vendor Details</th>
                    <th>Customer Details</th>
			        <th>Product Details</th>
                    <th>
                    	Payment Information
                    </th>
                    <th>Paid Amount</th>
                    <th>Payment Status</th>
			        <th>Action</th>
			    </tr>
			</thead>
            <tbody>
            	<?php
            	$i=0;
            	$statement = $pdo->prepare("SELECT tbl_payment.*,tbl_product.user_type,tbl_product.user_id FROM tbl_payment 
                left join tbl_order on tbl_order.payment_id = tbl_payment.payment_id 
                left join tbl_product on tbl_order.product_id = tbl_product.p_id 
                ORDER by id DESC");
            	$statement->execute();
            	$result = $statement->fetchAll();							
            	foreach ($result as $row) {

                    $get_customer_name = check_customer_name($pdo,$row['user_id']);
            		$i++;
            		?>
					<tr class="<?php if($row['payment_status']=='Pending'){echo 'bg-r';}else{echo 'bg-g';} ?>">
	                    <td><?php echo $i; ?></td>
                        <td><?php 
                            if(empty($get_customer_name))
                            { echo 'Admin';}
                            else{

                                $c_social_info = $pdo->prepare('SELECT * from tbl_customer_social_info where customer_id = ?');
                                $c_social_info->execute([$get_customer_name['cust_id']]);
                                $social_info = $c_social_info->fetch(PDO::FETCH_ASSOC);

                                echo '<b>Name:</b>'.$get_customer_name['cust_name'].'<br>';
                                echo '<b>Email:</b>'.$get_customer_name['cust_email'].'<br>';
                                echo '<b>Phone:</b>'.$get_customer_name['cust_phone'].'<br><br>';
                                if(!empty($social_info)){
                                    if(!empty($social_info['twitter_handle'])){
                                        echo '<a href="http://twitter.com/'.$social_info['twitter_handle'].'" class="" ><i class="fa fa-twitter-square text-navy font-size-25"></i></a>';
                                    }
                                    if(!empty($social_info['fb_id'])){
                                        echo '<a href="'.$social_info['fb_id'].'" ><i class=""></i></a>';
                                    }
                                    if(!empty($social_info['linkdin_id'])){
                                        echo '<a href="'.$social_info['linkdin_id'].'" ><i class=""></i></a>';
                                    }
                                }
                                
                            }
                        ?></td>
	                    <td>
                            <b>Name:</b><br> <?php echo $row['customer_name']; ?><br>
                            <b>Email:</b><br> <?php echo $row['customer_email']; ?><br>
                            <b>Phone:</b><br> <?php echo $row['customer_phone']; ?><br><br>
                            <a href="customer-detail.php?id=<?php echo $row['customer_id']; ?>" class="btn btn-primary btn-xs" style="width:100%;margin-bottom:4px;" target="_blank">See Details</a>
                            <!-- <a href="#" data-toggle="modal" data-target="#model-<?php echo $i; ?>"class="btn btn-warning btn-xs" style="width:100%;margin-bottom:4px;">Send Message</a> -->
                            <div id="model-<?php echo $i; ?>" class="modal fade" role="dialog">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title" style="font-weight: bold;">Send Message</h4>
										</div>
										<div class="modal-body" style="font-size: 14px">
											<form action="" method="post">
                                                <input type="hidden" name="cust_id" value="<?php echo $row['customer_id']; ?>">
                                                <input type="hidden" name="payment_id" value="<?php echo $row['payment_id']; ?>">
												<table class="table table-bordered">
													<tr>
														<td>Subject</td>
														<td>
                                                            <input type="text" name="subject_text" class="form-control" style="width: 100%;" required>
														</td>
													</tr>
                                                    <tr>
                                                        <td>Message</td>
                                                        <td>
                                                            <textarea name="message_text" class="form-control" cols="30" rows="10" style="width:100%;height: 200px;" required></textarea>
                                                        </td>
                                                    </tr>
													<tr>
														<td></td>
														<td>
                                                            <input type="submit" class="btn btn-success btn-sm" value="Send Message" name="form1">
                                                        </td>
													</tr>
												</table>
											</form>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>
                        </td>
                        <td>
                           <?php
                           $statement1 = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
                           $statement1->execute(array($row['payment_id']));
                           $result1 = $statement1->fetchAll();
                           foreach ($result1 as $row1) {
                                echo '(<b>Product Name:</b> '.$row1['product_name'];
                                echo ', <b>Unit Price:</b> '.$row1['unit_price'].')';
                                echo '<br><br>';
                           }
                           ?>
                        </td>
                        <td>
                        	<?php if($row['payment_method'] == 'PayPal'): ?>
                        		<b>Payment Method:</b> <?php echo '<span style="color:red;"><b>'.$row['payment_method'].'</b></span>'; ?><br>
                                <b>Currency:</b> <?php echo $row['currency']; ?><br>
                        		<b>Payment Id:</b> <?php echo $row['payment_id']; ?><br>
                        		<b>Date:</b> <?php echo $row['payment_date']; ?><br>
                        		<b>Transaction Id:</b> <?php echo $row['txnid']; ?><br>

                        	<?php elseif($row['payment_method'] == 'Stripe'): ?>
                        		<b>Payment Method:</b> <?php echo '<span style="color:red;"><b>'.$row['payment_method'].'</b></span>'; ?><br>
                                <b>Currency:</b> <?php echo $row['currency']; ?><br>
                        		<b>Payment Id:</b> <?php echo $row['payment_id']; ?><br>
								<b>Date:</b> <?php echo $row['payment_date']; ?><br>
                        		<b>Transaction Id:</b> <?php echo $row['txnid']; ?><br>
                        		<b>Card Number:</b> <?php echo $row['card_number']; ?><br>
                        		<b>Expire Month:</b> <?php echo $row['card_month']; ?><br>
                        		<b>Expire Year:</b> <?php echo $row['card_year']; ?><br>

                        	<?php elseif($row['payment_method'] == 'Bank Deposit'): ?>
                        		<b>Payment Method:</b> <?php echo '<span style="color:red;"><b>'.$row['payment_method'].'</b></span>'; ?><br>
                                <b>Currency:</b> <?php echo $row['currency']; ?><br>
                        		<b>Payment Id:</b> <?php echo $row['payment_id']; ?><br>
								<b>Date:</b> <?php echo $row['payment_date']; ?><br>
                        		<b>Transaction Information:</b> <br><?php echo $row['bank_transaction_info']; ?><br>

                            <?php elseif($row['payment_method'] == 'Cash On Delivery'): ?>
                                N/A

                        	<?php endif; ?>
                        </td>
                        <td><?php echo $row['paid_amount']; ?></td>
                        <td>
                            <?php echo $row['payment_status']; ?>
                            <br><br>
                            <?php
                                if($row['payment_status']=='Pending'){
                                    ?>
                                    <a href="order-change-status.php?id=<?php echo $row['id']; ?>&task=Completed" class="btn btn-warning btn-xs" style="width:100%;margin-bottom:4px;" onClick="return confirm('Are you sure?');">Make Completed</a>
                                    <?php
                                }
                            ?>
                        </td>
                        
	                    <td>
                            <a href="order-delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-xs" style="width:100%;" onClick="return confirm('Are you sure?');">Delete</a>
	                    </td>
	                </tr>
            		<?php
            	}
            	?>
            </tbody>
          </table>
        </div>
      </div>
  

</section>


<?php require_once('footer.php'); ?>