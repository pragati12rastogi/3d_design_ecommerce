<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $banner_forget_password = $row['banner_forget_password'];
}

$statement = $pdo->prepare("SELECT * FROM tbl_setting_email WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $send_email_from  = $row['send_email_from'];
    $receive_email_to = $row['receive_email_to'];
    $forget_password_message = $row['forget_password_message'];
}
?>

<?php
if(isset($_POST['form1'])) {

    $valid = 1;
        
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
            if(!$total) {
                $valid = 0;
                $error_message .= EMAIL_ADDRESS_IS_NOT_FOUND_IN_OUR_SYSTEM."\\n";
            }
        }
    }

    if($valid == 1) {

        $token = md5(rand());
        $now = time();

        $statement = $pdo->prepare("UPDATE tbl_customer SET cust_token=?,cust_timestamp=? WHERE cust_email=?");
        $statement->execute(array($token,$now,strip_tags($_POST['cust_email'])));
        
        $message = '<p>'.TO_RESET_PASSWORD_PLEASE_CLICK_ON_LINK_BELOW.'<br> <a href="'.BASE_URL.'reset-password.php?email='.$_POST['cust_email'].'&token='.$token.'">Click here</a>';

        try {

            $to      = $_POST['cust_email'];
            $subject = PASSWORD_RESET_REQUEST;
            
            $mail->setFrom($send_email_from, 'Admin');
            $mail->addAddress($to);
            $mail->addReplyTo($receive_email_to, 'Admin');
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
    
            $mail->MsgHTML($message);
            $mail->Send();
    
            $success_message = $forget_password_message;
        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
        
    }
}
?>

<div class="page-banner" style="background-color:#444;background-image: url(public_files/uploads/<?php echo $banner_forget_password; ?>);">
    <div class="inner">
        <h1><?php echo FORGET_PASSWORD; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <?php
                    if($error_message != '') {
                        echo "<script>alert('".$error_message."')</script>";
                    }
                    if($success_message != '') {
                        echo "<script>alert('".$success_message."')</script>";
                    }
                    ?>
                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for=""><?php echo EMAIL_ADDRESS; ?> *</label>
                                    <input type="email" class="form-control" name="cust_email">
                                </div>
                                <div class="form-group">
                                    <label for=""></label>
                                    <input type="submit" class="btn btn-primary" value="<?php echo SUBMIT; ?>" name="form1">
                                </div>
                                <a href="login.php" style="color:#e4144d;"><?php echo BACK_TO_LOGIN_PAGE; ?></a>
                            </div>
                        </div>                        
                    </form>
                </div>                
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>