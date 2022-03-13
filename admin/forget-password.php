<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message='';


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
	}
	$mail->IsSMTP();
	$mail->SMTPAuth   = true;
	$mail->Host       = $smtp_host;
	$mail->Port       = $smtp_port;
	$mail->Username   = $smtp_username;
	$mail->Password   = $smtp_password;	
}



if(isset($_POST['form1'])) 
{

	$valid = 1;
        
    if(empty($_POST['email'])) {
        $valid = 0;
        $error_message .= "Email can not be empty.<br>";
    } else {
    	if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
	        $valid = 0;
	        $error_message .= 'Email address must be valid.<br>';
	    } else {
	    	$statement = $pdo->prepare("SELECT * FROM tbl_user WHERE email=?");
	    	$statement->execute(array($_POST['email']));
	    	$total = $statement->rowCount();						
	    	if(!$total) {
	    		$valid = 0;
	        	$error_message .= 'You email address is not found in our system.<br>';
	    	}
	    }
    }

    if($valid == 1) {

    	$token = md5(rand());

		$statement = $pdo->prepare("UPDATE tbl_user SET token=? WHERE email=?");
		$statement->execute(array($token,$_POST['email']));
		
		$message = '
		<html><body>
		<p>To reset your password, please <a href="'.BASE_URL.'reset-password.php?email='.$_POST['email'].'&token='.$token.'">click here</a> and enter a new password
		</body></html>';
		
		
		try {
		    $to = $_POST['email'];
		    $mail->setFrom($send_email_from);
		    $mail->addAddress($to);
		    $mail->addReplyTo($receive_email_to);
		    
		    $mail->isHTML(true);
		    $mail->Subject = "Password Reset Request";
	
		    $mail->MsgHTML($message);
			$mail->Send();
	
		    $success_message = 'An email is sent to your email address. Please follow instruction in there.';

		} catch (Exception $e) {
		    echo 'Message could not be sent.';
		    echo 'Mailer Error: ' . $mail->ErrorInfo;
		}



    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Forget Password</title>

	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/ionicons.min.css">
	<link rel="stylesheet" href="css/datepicker3.css">
	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet" href="css/select2.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.css">
	<link rel="stylesheet" href="css/AdminLTE.min.css">
	<link rel="stylesheet" href="css/_all-skins.min.css">

	<link rel="stylesheet" href="style.css">
</head>

<body class="hold-transition login-page sidebar-mini">

<div class="login-box">
	<div class="login-logo">
		<b>Admin Panel</b>
	</div>
  	<div class="login-box-body">
    	<h4 class="login-box-msg">Forget Password</h4>
    
	    <?php 
	    if( (isset($error_message)) && ($error_message!='') ):
	        echo '<div class="error">'.$error_message.'</div>';
	    endif;

	    if( (isset($success_message)) && ($success_message!='') ):
	        echo '<div class="success">'.$success_message.'</div>';
	    endif;
	    ?>

		<form action="" method="post">
			<?php $csrf->echoInputField(); ?>
			<div class="form-group has-feedback">
				<input class="form-control" placeholder="Email address" name="email" type="email" autocomplete="off" autofocus>
			</div>
			<div class="row">
				<div class="col-xs-8" style="padding-top:7px;"><a href="login.php" style="color:red;">Back to login page</a></div>
				<div class="col-xs-4">
					<input type="submit" class="btn btn-primary btn-block btn-flat login-button" name="form1" value="Submit">
				</div>
			</div>
		</form>
	</div>
</div>


<script src="js/jquery-2.2.3.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>
<script src="js/select2.full.min.js"></script>
<script src="js/jquery.inputmask.js"></script>
<script src="js/jquery.inputmask.date.extensions.js"></script>
<script src="js/jquery.inputmask.extensions.js"></script>
<script src="js/moment.min.js"></script>
<script src="js/bootstrap-datepicker.js"></script>
<script src="js/icheck.min.js"></script>
<script src="js/fastclick.js"></script>
<script src="js/jquery.sparkline.min.js"></script>
<script src="js/jquery.slimscroll.min.js"></script>
<script src="js/app.min.js"></script>
<script src="js/demo.js"></script>

</body>
</html>