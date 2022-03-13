<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $banner_registration = $row['banner_registration'];    
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_contact WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $contact_email = $row['contact_email'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_email WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $receive_email_thank_you_message = $row['receive_email_thank_you_message'];
    $send_email_from                 = $row['send_email_from'];
    $receive_email_to                = $row['receive_email_to'];
}
?>

<?php
if (isset($_POST['form1'])) {

    $valid = 1;

    if(empty($_POST['cust_name'])) {
        $valid = 0;
        $error_message .= CUSTOMER_NAME_CAN_NOT_BE_EMPTY."\\n";
    }

    if(empty($_POST['cust_email'])) {
        $valid = 0;
        $error_message .= EMAIL_ADDRESS_CAN_NOT_BE_EMPTY."\\n";
    } else {
        if (filter_var($_POST['cust_email'], FILTER_VALIDATE_EMAIL) === false) {
            $valid = 0;
            $error_message .= EMAIL_ADDRESS_MUST_BE_VALID."\\n";
        } else {
            $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
            $statement->execute(array($_POST['cust_email']));
            $total = $statement->rowCount();                            
            if($total) {
                $valid = 0;
                $error_message .= EMAIL_ADDRESS_ALREADY_EXISTS."\\n";
            }
        }
    }

    if(empty($_POST['cust_phone'])) {
        $valid = 0;
        $error_message .= PHONE_NUMBER_CAN_NOT_BE_EMPTY."\\n";
    }

    if(empty($_POST['cust_address'])) {
        $valid = 0;
        $error_message .= ADDRESS_CAN_NOT_BE_EMPTY."\\n";
    }

    if(empty($_POST['cust_country'])) {
        $valid = 0;
        $error_message .= YOU_MUST_HAVE_TO_SELECT_A_COUNTRY."\\n";
    }

    if(empty($_POST['cust_city'])) {
        $valid = 0;
        $error_message .= CITY_CAN_NOT_BE_EMPTY."\\n";
    }

    if(empty($_POST['cust_state'])) {
        $valid = 0;
        $error_message .= STATE_CAN_NOT_BE_EMPTY."\\n";
    }

    if(empty($_POST['cust_zip'])) {
        $valid = 0;
        $error_message .= ZIP_CODE_CAN_NOT_BE_EMPTY."\\n";
    }

    if( empty($_POST['cust_password']) || empty($_POST['cust_re_password']) ) {
        $valid = 0;
        $error_message .= PASSWORD_CAN_NOT_BE_EMPTY."\\n";
    }

    if( !empty($_POST['cust_password']) && !empty($_POST['cust_re_password']) ) {
        if($_POST['cust_password'] != $_POST['cust_re_password']) {
            $valid = 0;
            $error_message .= PASSWORDS_DO_NOT_MATCH."\\n";
        }
    }

    if($valid == 1) {

        $token = md5(time());
        $cust_datetime = date('Y-m-d h:i:s');
        $cust_timestamp = time();

        // saving into the database
        $statement = $pdo->prepare("INSERT INTO tbl_customer (
                                        cust_name,
                                        cust_cname,
                                        cust_email,
                                        cust_phone,
                                        cust_country,
                                        cust_address,
                                        cust_city,
                                        cust_state,
                                        cust_zip,
                                        cust_password,
                                        cust_token,
                                        cust_datetime,
                                        cust_timestamp,
                                        cust_status
                                    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $statement->execute(array(
                                        strip_tags($_POST['cust_name']),
                                        '',
                                        strip_tags($_POST['cust_email']),
                                        strip_tags($_POST['cust_phone']),
                                        strip_tags($_POST['cust_country']),
                                        strip_tags($_POST['cust_address']),
                                        strip_tags($_POST['cust_city']),
                                        strip_tags($_POST['cust_state']),
                                        strip_tags($_POST['cust_zip']),
                                        
                                        md5($_POST['cust_password']),
                                        $token,
                                        $cust_datetime,
                                        $cust_timestamp,
                                        0
                                    ));

        // Send email for confirmation of the account
        $to = $_POST['cust_email'];
        
        $subject = REGISTRATION_EMAIL_CONFIRMATION;
        $verify_link = BASE_URL.'verify.php?email='.$to.'&token='.$token;
        $message = '
'.REGISTRATION_COMPLETION_MESSAGE_IN_EMAIL.'<br><br>

<a href="'.$verify_link.'">'.$verify_link.'</a>';
	
	
        
        try {
		    
    	    $mail->setFrom($send_email_from, 'Admin');
    	    $mail->addAddress($to, $_POST['cust_name']);
    	    $mail->addReplyTo($receive_email_to, 'Admin');
    	    
    	    $mail->isHTML(true);
    	    $mail->Subject = $subject;

    	    $mail->MsgHTML($message);
            $mail->Send();

    	    $success_message = $receive_email_thank_you_message;    
    	} catch (Exception $e) {
    	    echo 'Message could not be sent.';
    	    echo 'Mailer Error: ' . $mail->ErrorInfo;
    	}

        unset($_POST['cust_name']);
        unset($_POST['cust_email']);
        unset($_POST['cust_phone']);
        unset($_POST['cust_address']);
        unset($_POST['cust_city']);
        unset($_POST['cust_state']);
        unset($_POST['cust_zip']);

        $success_message = REGISTRATION_COMPLETION_MESSAGE;
    }
}
?>

<div class="page-banner" style="background-color:#444;background-image: url(public_files/uploads/<?php echo $banner_registration; ?>);">
    <div class="inner">
        <h1><?php echo CUSTOMER_REGISTRATION; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">

                    

                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                
                                <?php
                                if($error_message != '') {
                                    echo "<script>alert('".$error_message."')</script>";
                                }
                                if($success_message != '') {
                                    echo "<script>alert('".$success_message."')</script>";
                                }
                                ?>

                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo FULL_NAME; ?> *</label>
                                    <input type="text" class="form-control" name="cust_name" value="<?php if(isset($_POST['cust_name'])){echo $_POST['cust_name'];} ?>">
                                </div>
                                
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo EMAIL_ADDRESS; ?> *</label>
                                    <input type="email" class="form-control" name="cust_email" value="<?php if(isset($_POST['cust_email'])){echo $_POST['cust_email'];} ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo PHONE_NUMBER; ?> *</label>
                                    <input type="text" class="form-control" name="cust_phone" value="<?php if(isset($_POST['cust_phone'])){echo $_POST['cust_phone'];} ?>">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for=""><?php echo ADDRESS; ?> *</label>
                                    <textarea name="cust_address" class="form-control" cols="30" rows="10" style="height:70px;"><?php if(isset($_POST['cust_address'])){echo $_POST['cust_address'];} ?></textarea>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo COUNTRY; ?> *</label>
                                    <select name="cust_country" class="form-control select2" style="width:100%;">
                                        <option value=""><?php echo SELECT_COUNTRY; ?></option>
                                    <?php
                                    $statement = $pdo->prepare("SELECT * FROM tbl_country ORDER BY country_name ASC");
                                    $statement->execute();
                                    $result = $statement->fetchAll();                            
                                    foreach ($result as $row) {
                                        ?>
                                        <option value="<?php echo $row['country_id']; ?>"><?php echo $row['country_name']; ?></option>
                                        <?php
                                    }
                                    ?>    
                                    </select>                                    
                                </div>
                                
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo CITY; ?> *</label>
                                    <input type="text" class="form-control" name="cust_city" value="<?php if(isset($_POST['cust_city'])){echo $_POST['cust_city'];} ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo STATE; ?> *</label>
                                    <input type="text" class="form-control" name="cust_state" value="<?php if(isset($_POST['cust_state'])){echo $_POST['cust_state'];} ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo ZIP_CODE; ?> *</label>
                                    <input type="text" class="form-control" name="cust_zip" value="<?php if(isset($_POST['cust_zip'])){echo $_POST['cust_zip'];} ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo PASSWORD; ?> *</label>
                                    <input type="password" class="form-control" name="cust_password">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""><?php echo RETYPE_PASSWORD; ?> *</label>
                                    <input type="password" class="form-control" name="cust_re_password">
                                </div>
                                <div class="col-md-6 form-group">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label for=""></label>
                                            <input type="submit" class="btn btn-primary" value="<?php echo REGISTER; ?>" name="form1">
                                        </div>
                                        <div class="col-md-8 form-group">
                                            <p class="m_10">Already a member? <a href="login.php">Login Here</a></p>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>                        
                    </form>
                </div>                
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
