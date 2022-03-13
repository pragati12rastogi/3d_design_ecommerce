<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1']))
{    
    // updating the database
    $statement = $pdo->prepare("UPDATE tbl_setting_email SET send_email_from=?,receive_email_to=?, smtp_active=?, smtp_ssl=?, smtp_host=?, smtp_port=?, smtp_username=?, smtp_password=?, receive_email_subject=?,receive_email_thank_you_message=?, forget_password_message=? WHERE id=1");
    $statement->execute(array($_POST['send_email_from'],$_POST['receive_email_to'],$_POST['smtp_active'],$_POST['smtp_ssl'],$_POST['smtp_host'],$_POST['smtp_port'],$_POST['smtp_username'],$_POST['smtp_password'],$_POST['receive_email_subject'],$_POST['receive_email_thank_you_message'],$_POST['forget_password_message']));

    $success_message = 'Email setting is updated successfully.';
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Setting - Email</h1>
    </div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_email WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
    $send_email_from                 = $row['send_email_from'];
    $receive_email_to                = $row['receive_email_to'];
    $smtp_active                     = $row['smtp_active'];
    $smtp_ssl                        = $row['smtp_ssl'];
    $smtp_host                       = $row['smtp_host'];
    $smtp_port                       = $row['smtp_port'];
    $smtp_username                   = $row['smtp_username'];
    $smtp_password                   = $row['smtp_password'];
    $receive_email_subject           = $row['receive_email_subject'];
    $receive_email_thank_you_message = $row['receive_email_thank_you_message'];
    $forget_password_message         = $row['forget_password_message'];
}
?>


<section class="content" style="min-height:auto;margin-bottom: -30px;">
    <div class="row">
        <div class="col-md-12">
            <?php if($error_message): ?>
            <div class="callout callout-danger">
            <p>
            <?php echo $error_message; ?>
            </p>
            </div>
            <?php endif; ?>

            <?php if($success_message): ?>
            <div class="callout callout-success">
            <p><?php echo $success_message; ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="content">

    <div class="row">
        <div class="col-md-12">

            <form class="form-horizontal" action="" method="post">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Send Email From</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="send_email_from" value="<?php echo $send_email_from; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Receive Email To</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="receive_email_to" value="<?php echo $receive_email_to; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">SMTP Active? *</label>
                            <div class="col-sm-4">
                                <select name="smtp_active" class="form-control select2" style="width:100%;">
                                    <option value="Yes" <?php if($smtp_active == 'Yes') {echo 'selected';} ?>>Yes</option>
                                    <option value="No" <?php if($smtp_active == 'No') {echo 'selected';} ?>>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">SSL Active? *</label>
                            <div class="col-sm-4">
                                <select name="smtp_ssl" class="form-control select2" style="width:100%;">
                                     <option value="Yes" <?php if($smtp_ssl == 'Yes') {echo 'selected';} ?>>Yes</option>
                                    <option value="No" <?php if($smtp_ssl == 'No') {echo 'selected';} ?>>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">SMTP Host</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="smtp_host" value="<?php echo $smtp_host; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">SMTP Port</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="smtp_port" value="<?php echo $smtp_port; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">SMTP Username</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="smtp_username" value="<?php echo $smtp_username; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">SMTP Password</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="smtp_password" value="<?php echo $smtp_password; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Contact Form Subject</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="receive_email_subject" value="<?php echo $receive_email_subject; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Email Thank you message</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="receive_email_thank_you_message"><?php echo $receive_email_thank_you_message; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Forget password Message</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="forget_password_message"><?php echo $forget_password_message; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label"></label>
                            <div class="col-sm-5">
                                <button type="submit" class="btn btn-success pull-left" name="form1">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
                
        </div>
    </div>

</section>

<?php require_once('footer.php'); ?>