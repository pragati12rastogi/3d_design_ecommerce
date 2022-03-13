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

    $avl_social_info = $pdo->prepare("SELECT * from tbl_customer_social_info where customer_id=? ");
    $avl_social_info->execute(array($_SESSION['customer']['cust_id']));
    $social_info_avl = $avl_social_info->fetch(PDO::FETCH_ASSOC);
}
?>

<?php
if (isset($_POST['form1'])) {

    $valid = 1;

    if(!empty($_POST['twitter_handle']) && !preg_match('/^[A-Za-z0-9_]{1,15}$/', $_POST['twitter_handle'])){
        $valid = 0;
        $error_message .= WRONG_TWITTER_ID."<br>";
    }

    if(!empty($_POST['fb_id']) && !preg_match('/(https?:\/\/)?([\w\.]*)facebook\.com\/([a-zA-Z0-9_]*)$/', $_POST['fb_id'])){
        $valid = 0;
        $error_message .= WRONG_FACEBOOK_ID."<br>";
    }
    
    if(!empty($_POST['linkdin_id']) && !preg_match('/(https?)?:?(\/\/)?(([w]{3}||\w\w)\.)?linkedin.com(\w+:{0,1}\w*@)?(\S+)(:([0-9])+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/', $_POST['linkdin_id'])){
        $valid = 0;
        $error_message .= WRONG_LINKDIN_ID."<br>";
    }

    $check_social_info = $pdo->prepare("SELECT * from tbl_customer_social_info where customer_id=? ");
    $check_social_info->execute(array($_SESSION['customer']['cust_id']));
    $social_info = $check_social_info->fetch(PDO::FETCH_ASSOC);
    
    $pdo->begintransaction();
    if($valid == 1) {

        if(empty($social_info)){
            // insert
            $statement = $pdo->prepare("INSERT into tbl_customer_social_info (`customer_id`, `twitter_handle`, `fb_id`, `linkdin_id`) VALUES (?,?,?,?) ");
            $statement->execute(array($_SESSION['customer']['cust_id'],$_POST['twitter_handle'],$_POST['fb_id'],$_POST['linkdin_id']));
 
            if(empty($pdo->lastInsertId())){
                $pdo->rollback();
                $error_message .= NO_DATA_INSERTED .'<br>';
            }else{
                $pdo->commit();
                $success_message .= SOCIAL_NETWORK_UPDATED .'<br>';
            }

        }else{
            // update
            $upd_cs = $pdo->prepare("UPDATE tbl_customer_social_info SET twitter_handle=?,fb_id=?,linkdin_id=? WHERE customer_id=?");
            $upd_cs->execute(array($_POST['twitter_handle'],$_POST['fb_id'],$_POST['linkdin_id'],$_SESSION['customer']['cust_id']));

            
            $pdo->commit();
            $success_message .= SOCIAL_NETWORK_UPDATED .'<br>';
            
        }
        
        
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
                    <?php echo SOCIAL_NETWORKS; ?> 
                </h3>
                <hr class="mt_10">
            </div>
            <div >
                <ul class="nav nav-tabs nav_background">
                    <li ><a href="account-settings.php" >MAIN SETTING</a></li>
                    <li><a href="payment-agreement.php" >PAYMENT AGREEMENT</a></li>
                    <li ><a href="change-password.php" >CHANGE PASSWORD</a></li>
                    <li class="active"><a href="social-network-and-contact-info.php" >SOCIAL NETWORKS & CONTACT INFO</a></li>
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
                                        <label for="">Twitter Handle (Username)</label>
                                        <input type="text" class="form-control" name="twitter_handle" placeholder="@" value="<?php echo (isset($social_info_avl)?$social_info_avl['twitter_handle']:''); ?>" >
                                    </div>
                                    <div class="form-group">
                                        <label for="">Facebook Page</label>
                                        <input type="text" class="form-control" name="fb_id" placeholder="Paste in full URL" value="<?php echo (isset($social_info_avl)?$social_info_avl['fb_id']:''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="">LinkedIn Page</label>
                                        <input type="text" class="form-control" name="linkdin_id" placeholder="Paste in full URL" value="<?php echo (isset($social_info_avl)?$social_info_avl['linkdin_id']:''); ?>">
                                    </div>
                                    <input type="submit" class="btn btn-primary" value="<?php echo UPDATE.' '.SOCIAL_NETWORKS; ?>" name="form1">
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