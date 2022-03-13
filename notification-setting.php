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

    $avl_noti_setting = $pdo->prepare("SELECT * from tbl_notification_setting where customer_id=? ");
    $avl_noti_setting->execute(array($_SESSION['customer']['cust_id']));
    $noti_avl = $avl_noti_setting->fetch(PDO::FETCH_ASSOC);
    
}
?>

<?php
if (isset($_POST['form1'])) {

    $valid = 1;

    $news_and_design_updates = empty($_POST['news_and_design_updates'])?0:1;
    $sales_and_special_offer =empty($_POST['sales_and_special_offer'])?0:1;
    $onboarding_and_edu = empty($_POST['onboarding_and_edu'])?0:1;
    $sales_and_payment_info =empty($_POST['sales_and_payment_info'])?0:1;
    $communication =empty($_POST['communication'])?0:1;

    $check_social_info = $pdo->prepare("SELECT * from tbl_notification_setting where customer_id=? ");
    $check_social_info->execute(array($_SESSION['customer']['cust_id']));
    $social_info = $check_social_info->fetch(PDO::FETCH_ASSOC);
    
    $pdo->begintransaction();

    if($valid == 1) {

        if(empty($social_info)){
            // insert
            $statement = $pdo->prepare("INSERT into tbl_notification_setting (`news_and_design_updates`, `sales_and_special_offer`, `onboarding_and_edu`, `sales_and_payment_info`,`communication`,`customer_id`) VALUES (?,?,?,?,?,?) ");
            $statement->execute(array($news_and_design_updates,$sales_and_special_offer,$onboarding_and_edu,$sales_and_payment_info,$communication,$_SESSION['customer']['cust_id']));
 
            if(empty($pdo->lastInsertId())){
                $pdo->rollback();
                $error_message .=  NO_DATA_INSERTED.'<br>';
            }else{
                $pdo->commit();
                $success_message .= NOTIFICATION.' '.UPDATED_SUCCESSFULLY  .'<br>';
            }

        }else{
            // update
            $upd_cs = $pdo->prepare("UPDATE tbl_notification_setting SET news_and_design_updates=?,sales_and_special_offer=?,onboarding_and_edu=?, sales_and_payment_info=?, communication=?  WHERE customer_id=?");
            $upd_cs->execute(array($news_and_design_updates,$sales_and_special_offer,$onboarding_and_edu,$sales_and_payment_info,$communication,$_SESSION['customer']['cust_id']));
           
            $pdo->commit();
            $success_message .= NOTIFICATION.' '.UPDATED_SUCCESSFULLY .'<br>';
            
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
                    <?php echo EMAIL_NOTIFICATION; ?> 
                </h3>
                <hr class="mt_10">
            </div>
            <div >
                <ul class="nav nav-tabs nav_background">
                    <li ><a href="account-settings.php" >MAIN SETTING</a></li>
                    <!-- <li><a href="payment-agreement.php" >PAYMENT AGREEMENT</a></li> -->
                    <li ><a href="change-password.php" >CHANGE PASSWORD</a></li>
                    <li><a href="social-network-and-contact-info.php" >SOCIAL NETWORKS & CONTACT INFO</a></li>
                    <li  class="active"><a href="notification-setting.php" >NOTIFICATION SETTINGS</a></li>
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
                                    
                                    <table class="table table-bordered table-hover table-responsive">
                                        
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <h5>3D News / <?php echo WEBSITE_NAME; ?> updates</h5>
                                                    <p>Information about <?php echo WEBSITE_NAME; ?> products, new feature announcements and community news
                                                    </p>
                                                </td>
                                                <td class="p_30 text-center">
                                                    <input type="checkbox" name="news_and_design_updates" value="1" style="height: 25px;width: 20px;" <?php echo (!empty($noti_avl)? ($noti_avl['news_and_design_updates']== 1 ? 'checked':''):''); ?> >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h5>Sales & special offers</h5>
                                                    <p>Information about special offers and 3D model Sales 
                                                    </p>
                                                </td>
                                                <td class="p_30 text-center">
                                                    <input type="checkbox" name="sales_and_special_offer" value="1" style="height: 25px;width: 20px;" <?php echo (!empty($noti_avl)? ($noti_avl['sales_and_special_offer']== 1 ? 'checked':''):''); ?> >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h5>Onboarding & Education</h5>
                                                    <p>Information about your onboarding process and tips to help you get the most out of <?php echo WEBSITE_NAME; ?>
                                                    </p>
                                                </td>
                                                <td class="p_30 text-center">
                                                    <input type="checkbox" name="onboarding_and_edu" value="1" style="height: 25px;width: 20px;" <?php echo (!empty($noti_avl)? ($noti_avl['onboarding_and_edu']== 1 ? 'checked':''):''); ?> >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h5>Sales & Payments information</h5>
                                                    <p>Information about your sold 3D models and received payments
                                                    </p>
                                                </td>
                                                <td class="p_30 text-center">
                                                    <input type="checkbox" name="sales_and_payment_info" value="1" style="height: 25px;width: 20px;" <?php echo (!empty($noti_avl)? ($noti_avl['sales_and_payment_info']== 1 ? 'checked':''):''); ?> >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h5>Communication (Personal messages, 3D model feedback, community activity, offers)</h5>
                                                    <p>Information about your communication with other <?php echo WEBSITE_NAME; ?> users, including personal messages, new price and custom job offers, feedback and product support requests

                                                    </p>
                                                </td>
                                                <td class="p_30 text-center">
                                                    <input type="checkbox" name="communication" value="1" style="height: 25px;width: 20px;" <?php echo (!empty($noti_avl)? (($noti_avl['communication']== 1) ? 'checked':''):''); ?> >
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <input type="submit" class="btn btn-primary" value="<?php echo UPDATE.' '.NOTIFICATION; ?>" name="form1">
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