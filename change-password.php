<?php require_once('header.php'); ?>

<?php
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    header('location: '.BASE_URL.'logout.php');
    exit;
} else {
    // If customer is logged in, but admin make him inactive, then force logout this user.
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id=? AND cust_status=?");
    $statement->execute(array($_SESSION['customer']['cust_id'],0));
    $total = $statement->rowCount();
    if($total) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }
}
?>

<?php
if (isset($_POST['form1'])) {

    $valid = 1;

    if( empty($_POST['cust_password']) || empty($_POST['cust_re_password']) ) {
        $valid = 0;
        $error_message .= PASSWORD_CAN_NOT_BE_EMPTY."<br>";
    }

    if( !empty($_POST['cust_password']) && !empty($_POST['cust_re_password']) ) {
        if($_POST['cust_password'] != $_POST['cust_re_password']) {
            $valid = 0;
            $error_message .= PASSWORDS_DO_NOT_MATCH."<br>";
        }
    }

    $check_old_pass = $pdo->prepare("SELECT * from tbl_customer where cust_id=? ");
    $check_old_pass->execute(array($_SESSION['customer']['cust_id']));
    $check_old = $check_old_pass->fetch(PDO::FETCH_ASSOC);
    
    if($check_old['cust_password'] != md5(strip_tags($_POST['old_password']))){
        $valid = 0;
        $error_message .= "Password not match with old password. <br>";
    }

    if($valid == 1) {

        // update data into the database
        $password = strip_tags($_POST['cust_password']);
        
        $statement = $pdo->prepare("UPDATE tbl_customer SET cust_password=? WHERE cust_id=?");
        $statement->execute(array(md5($password),$_SESSION['customer']['cust_id']));
        
        $_SESSION['customer']['cust_password'] = md5($password);        

        $success_message = PASSWORD_IS_UPDATED_SUCCESSFULLY;
    }
}
?>

<div class="page">
    <div class="container">
        <div class="row">            
            <div class="col-md-12"> 
                <?php require_once('customer-sidebar.php'); ?>
            </div>
            <div class="col-md-12">
                
                <h3 class="">
                    <?php echo CHANGE_PASSWORD; ?>
                </h3>
                <hr class="mt_10">
            </div>
            <div >
                <ul class="nav nav-tabs nav_background">
                    <li ><a href="account-settings.php" >MAIN SETTING</a></li>
                    <li><a href="payment-agreement.php" >PAYMENT AGREEMENT</a></li>
                    <li class="active"><a href="change-password.php" >CHANGE PASSWORD</a></li>
                    <li><a href="social-network-and-contact-info.php" >SOCIAL NETWORKS & CONTACT INFO</a></li>
                    <!-- <li><a href="notification-setting.php" >NOTIFICATION SETTINGS</a></li> -->
                </ul>
                <div class="tab-content ">

                    <div class="tab-pane active" >
                        <form action="" method="post">
                            <?php $csrf->echoInputField(); ?>
                            <div class="row">
                                
                                <div class="col-md-12">
                                    
                                    <?php
                                    if(!empty($error_message)) {
                        
                                        echo "<div class='alert alert-danger' id='profile-error' style='margin-bottom:20px;'>".$error_message."</div>";
                                    ?>
                                    <script>
                                        setTimeout(function() {
                                            $("#profile-error").remove();
                                        }, 5000);
                                    </script>
                                    <?php
                                    }
                                    if($success_message != '') {
                                        echo "<div class='alert alert-success' id='profile-success' style='margin-bottom:20px;'>".$success_message."</div>";
                                    ?>
                                    <script>
                                        setTimeout(function() {
                                            $("#profile-success").remove();
                                        }, 5000);
                                    </script>
                                    <?php
                                    }
                                    ?>
                                    
                                    <div class="form-group">
                                        <label for=""><?php echo OLD_PASSWORD; ?> *</label>
                                        <input type="password" class="form-control" name="old_password">
                                    </div>
                                    <div class="form-group">
                                        <label for=""><?php echo NEW_PASSWORD; ?> *</label>
                                        <input type="password" class="form-control" name="cust_password">
                                    </div>
                                    <div class="form-group">
                                        <label for=""><?php echo RETYPE_NEW_PASSWORD; ?> *</label>
                                        <input type="password" class="form-control" name="cust_re_password">
                                    </div>
                                    <input type="submit" class="btn btn-primary" value="<?php echo UPDATE; ?>" name="form1">
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>                
            
        </div>
    </div>
</div>

<style>
    .nav_background{
        background: transparent !important;
    }
</style>

<?php require_once('footer.php'); ?>