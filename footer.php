<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_footer WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	$newsletter_on_off = $row['newsletter_on_off'];
	$footer_about      = $row['footer_about'];
	$footer_copyright  = $row['footer_copyright'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_post WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	$total_recent_post_footer  = $row['total_recent_post_footer'];
	$total_popular_post_footer = $row['total_popular_post_footer'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_contact WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	$contact_email   = $row['contact_email'];
	$contact_phone   = $row['contact_phone'];
	$contact_address = $row['contact_address'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_email WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	$send_email_from  = $row['send_email_from'];
	$receive_email_to = $row['receive_email_to'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_head_body WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
	$before_body = $row['before_body'];
}


?>


<!-- <?php if($newsletter_on_off == 1): ?>
<section class="home-newsletter">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="single">
					
					<form action="" method="post">
						<?php $csrf->echoInputField(); ?>
						<h2><?php echo SUBSCRIBE_TO_OUR_NEWSLETTER; ?></h2>
						<div class="input-group">
							<input type="email" class="form-control" placeholder="<?php echo ENTER_YOUR_EMAIL_ADDRESS; ?>" name="email_subscribe">
							<span class="input-group-btn">
							<button class="btn btn-theme" type="submit" name="form_subscribe"><?php echo SUBSCRIBE; ?></button>
							</span>
						</div>
					
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<?php endif; ?> -->

<section class="footer-main">
	<div class="container">
		<div class="row">
			<div class="col-md-3 col-sm-6 footer-col">
				<!-- <h3><?php echo ABOUT_US; ?></h3> -->
				<div class="row">
					<div class="col-md-12">
						<a href="index.php"><img width="100%" src="public_files/uploads/<?php echo $logo; ?>" alt="logo image" class="col-md-10 p_0"></a>
					</div>
					<div class="col-md-12">
						<p>
							<?php echo nl2br($footer_about); ?>
						</p>
					</div>
					<div class="col-md-12">
						<ul class="footer-social">
						<?php
						$statement = $pdo->prepare("SELECT * FROM tbl_social");
						$statement->execute();
						$result = $statement->fetchAll();
						foreach ($result as $row) {
							?>
							<?php if($row['social_url'] != ''): ?>
							<li class="social_li"><a  href="<?php echo $row['social_url']; ?>"><i class="<?php echo $row['social_icon']; ?>"></i></a></li>
							<?php endif; ?>
							<?php
						}
						?>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-6 footer-col">
				<h3><?php echo HELP; ?></h3>
				<div class="col-md-12 p_0">
					<ul>
					<?php
							$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
							$statement->execute();
							$result = $statement->fetchAll();		
							foreach ($result as $row) {
								$about_title = $row['about_title'];
								$faq_title = $row['faq_title'];
								$blog_title = $row['blog_title'];
								$contact_title = $row['contact_title'];
								$tnc_title = $row['tnc_title'];
								$privacy_title = $row['privacy_title'];
							}
							?>
							<li><a href="about.php"><?php echo $about_title; ?></a></li>
							<li><a href="faq.php"><?php echo $faq_title; ?></a></li>
							<li><a href="term-and-conditions.php"><?php echo $tnc_title; ?></a></li>
							<li><a href="privacy.php"><?php echo $privacy_title; ?></a></li>
							<li><a href="contact.php"><?php echo $contact_title; ?></a></li>
					</ul>
				</div>
			</div>
			<!-- <div class="col-md-3 col-sm-6 footer-col">
				<h3><?php echo RECENT_POSTS; ?></h3>
				<div class="row">
					<div class="col-md-12">
						<ul>
							<?php
				            $i = 0;
				            $statement = $pdo->prepare("SELECT * FROM tbl_post ORDER BY post_id DESC");
				            $statement->execute();
				            $result = $statement->fetchAll();
				            foreach ($result as $row) {
				                $i++;
				                if($i > $total_recent_post_footer) {
				                    break;
				                }
				                ?>
				                <li><a href="blog-single.php?slug=<?php echo $row['post_slug']; ?>"><?php echo $row['post_title']; ?></a></li>
				                <?php
				            }
           					?>
						</ul>
					</div>
				</div>
			</div> -->
			<!-- <div class="col-md-3 col-sm-6 footer-col">
				<h3><?php echo POPULAR_POSTS; ?></h3>
				<div class="row">
					<div class="col-md-12">
						<ul>
							<?php
				            $i = 0;
				            $statement = $pdo->prepare("SELECT * FROM tbl_post ORDER BY total_view DESC");
				            $statement->execute();
				            $result = $statement->fetchAll();                            
				            foreach ($result as $row) {
				                $i++;
				                if($i > $total_popular_post_footer) {
				                    break;
				                }
				                ?>
				                <li><a href="blog-single.php?slug=<?php echo $row['post_slug']; ?>"><?php echo $row['post_title']; ?></a></li>
				                <?php
				            }
				            ?>
						</ul>
					</div>
				</div>
			</div> -->
			<div class="col-md-3 col-sm-6 footer-col">
				<h3><?php echo CONTACT_INFORMATION; ?></h3>
				<div class="contact-item">
					<div class="text"><?php echo nl2br($contact_address); ?></div>
				</div>
				<div class="contact-item">
					<div class="text"><?php echo $contact_phone; ?></div>
				</div>
				<div class="contact-item">
					<div class="text"><?php echo $contact_email; ?></div>
				</div>
			</div>
			<?php if($newsletter_on_off == 1): ?>
			<div class="col-md-3 col-sm-6 footer-col">
				<h3><?php echo SUBSCRIBE_TO_OUR_NEWSLETTER; ?></h3>
				
					<div class="single">
						<?php
						if(isset($_POST['form_subscribe']))
						{
							
							if(empty($_POST['email_subscribe'])) 
							{
								$valid = 0;
								$newsletter_error_message .= EMAIL_ADDRESS_CAN_NOT_BE_EMPTY;
							}
							else
							{
								if (filter_var($_POST['email_subscribe'], FILTER_VALIDATE_EMAIL) === false)
								{
									$valid = 0;
									$newsletter_error_message .= EMAIL_ADDRESS_MUST_BE_VALID;
								}
								else
								{
									$statement = $pdo->prepare("SELECT * FROM tbl_subscriber WHERE subs_email=?");
									$statement->execute(array($_POST['email_subscribe']));
									$total = $statement->rowCount();							
									if($total)
									{
										$valid = 0;
										$newsletter_error_message .= EMAIL_ADDRESS_ALREADY_EXISTS;
									}
									else
									{
										// Sending email to the requested subscriber for email confirmation
										// Getting activation key to send via email. also it will be saved to database until user click on the activation link.
										$key = md5(uniqid(rand(), true));

										// Getting current date
										$current_date = date('Y-m-d');

										// Getting current date and time
										$current_date_time = date('Y-m-d H:i:s');

										// Inserting data into the database
										$statement = $pdo->prepare("INSERT INTO tbl_subscriber (subs_email,subs_date,subs_date_time,subs_hash,subs_active) VALUES (?,?,?,?,?)");
										$statement->execute(array($_POST['email_subscribe'],$current_date,$current_date_time,$key,0));

										// Sending Confirmation Email
										$to = $_POST['email_subscribe'];
										$subject = 'Subscriber Email Confirmation';
										
										// Getting the url of the verification link
										$verification_url = BASE_URL.'verify-subscriber.php?email='.$to.'&key='.$key;

										$message = '
										Thanks for your interest to subscribe our newsletter!<br><br>
										Please click this link to confirm your subscription:
															'.$verification_url.'<br><br>
										This link will be active only for 24 hours.
															';
										
										
										try {
						
											$mail->setFrom($send_email_from, 'Admin');
											$mail->addAddress($to);
											$mail->addReplyTo($receive_email_to, 'Admin');
											
											$mail->isHTML(true);
											$mail->Subject = $subject;
									
											$mail->MsgHTML($message);
											$mail->Send();
									
											$success_message1 = PLEASE_CHECK_YOUR_EMAIL_AND_CONFIRM_SUBSCRIPTION;   
										} catch (Exception $e) {
											echo 'Message could not be sent.';
											echo 'Mailer Error: ' . $mail->ErrorInfo;
										}
										
										

										
									}
								}
							}
						}
						if(!empty($newsletter_error_message)) {
							echo "<script>alert('".$newsletter_error_message."')</script>";
						}
						if(!empty($success_message1)) {
							echo "<script>alert('".$success_message1."')</script>";
						}
						?>
						<form action="" method="post">
							<?php $csrf->echoInputField(); ?>
							<div class="col-md-12 p_0">
								<p>
								Subscribe to our newsletters now and stay up-to-date with new collections
								</p>
							</div>
							<div class="input-group">
								<input type="email" class="form-control" placeholder="<?php echo ENTER_YOUR_EMAIL_ADDRESS; ?>" name="email_subscribe">
								<span class="input-group-btn">
								<button class="btn btn-theme" type="submit" name="form_subscribe"><?php echo SUBSCRIBE; ?></button>
								</span>
							</div>
						
						</form>
					</div>
				
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>


<div class="footer-bottom">
	<div class="container">
		<div class="row">
			<div class="col-md-12 copyright">
				<?php echo $footer_copyright; ?>

			</div>
		</div>
	</div>
</div>


<a href="#" class="scrollup">
	<i class="fa fa-angle-up"></i>
</a>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_payment WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $stripe_public_key = $row['stripe_public_key'];
    $stripe_secret_key = $row['stripe_secret_key'];
}
?>


<script src="<?php echo BASE_URL; ?>public_files/js/custom.js"></script>
<script>
	function confirmDelete()
	{
	    return confirm("Do you sure want to delete this data?");
	}
	$(document).ready(function () {
		advFieldsStatus = $('input[name="payment_method"]:checked').val();

		$('#paypal_form').hide();
		$('#stripe_form_div').hide();
		$('#bank_form').hide();
		$('#cash_on_delivery_form').hide();

        $('.advFieldsStatus').on('change',function() {
            advFieldsStatus = $('input[name="payment_method"]:checked').val();
            if ( advFieldsStatus == '' ) {
            	$('#paypal_form').hide();
				$('#stripe_form_div').hide();
				$('#bank_form').hide();
				$('#cash_on_delivery_form').hide();
            } else if ( advFieldsStatus == 'PayPal' ) {
               	$('#paypal_form').show();
				$('#stripe_form_div').hide();
				$('#bank_form').hide();
				$('#cash_on_delivery_form').hide();
            } else if ( advFieldsStatus == 'Stripe' ) {
               	$('#paypal_form').hide();
				$('#stripe_form_div').show();
				$('#bank_form').hide();
				$('#cash_on_delivery_form').hide();
            } else if ( advFieldsStatus == 'Bank Deposit' ) {
            	$('#paypal_form').hide();
				$('#stripe_form_div').hide();
				$('#bank_form').show();
				$('#cash_on_delivery_form').hide();
            } else if ( advFieldsStatus == 'Cash On Delivery' ) {
            	$('#paypal_form').hide();
				$('#stripe_form_div').hide();
				$('#bank_form').hide();
				$('#cash_on_delivery_form').show();
            }
        });
	});


	$(document).on('submit', '#stripe_form', function () {
        // createToken returns immediately - the supplied callback submits the form if there are no errors
        $('#submit-button').prop("disabled", true);
        $("#msg-container").hide();
        Stripe.card.createToken({
            number: $('.card-number').val(),
            cvc: $('.card-cvc').val(),
            exp_month: $('.card-expiry-month').val(),
            exp_year: $('.card-expiry-year').val()
            // name: $('.card-holder-name').val()
        }, stripeResponseHandler);
        return false;
    });
    Stripe.setPublishableKey('<?php echo $stripe_public_key; ?>');
    function stripeResponseHandler(status, response) {
        if (response.error) {
            $('#submit-button').prop("disabled", false);
            $("#msg-container").html('<div style="color: red;border: 1px solid;margin: 10px 0px;padding: 5px;"><strong>Error:</strong> ' + response.error.message + '</div>');
            $("#msg-container").show();
        } else {
            var form$ = $("#stripe_form");
            var token = response['id'];
            form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
            form$.get(0).submit();
        }
    }
</script>
<?php echo $before_body; ?>
</body>
</html>