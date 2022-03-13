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


if (isset($_POST['form1'])) {

    $cust_name = strip_tags($_POST['cust_name']);
    $cust_phone = strip_tags($_POST['cust_phone']);
    $cust_address = strip_tags($_POST['cust_address']);
    $cust_country = strip_tags($_POST['cust_country']);
    $cust_city = strip_tags($_POST['cust_city']);
    $cust_state = strip_tags($_POST['cust_state']);
    $cust_zip = strip_tags($_POST['cust_zip']);
    $cust_city = strip_tags($_POST['cust_city']);
    $cust_type = strip_tags($_POST['customer_type']);


    $valid = 1;

    if(empty($cust_name)) {
        $valid = 0;
        $error_message .= CUSTOMER_NAME_CAN_NOT_BE_EMPTY."<br>";
    }

    if(empty($cust_phone)) {
        $valid = 0;
        $error_message .= PHONE_NUMBER_CAN_NOT_BE_EMPTY."<br>";
    }
    else
    {
        $q = $pdo->prepare("
                    SELECT * 
                    FROM tbl_customer 
                    WHERE cust_phone=? AND cust_phone!=?
                ");
        $q->execute([
                    $cust_phone,
                    $_SESSION['customer']['cust_phone']
                ]);
        $total = $q->rowCount();
        if($total)
        {
            $valid = 0;
            $error_message .= PHONE_NUMBER_ALREADY_EXIST."<br>";
        }
    }

    if(empty($cust_address)) {
        $valid = 0;
        $error_message .= ADDRESS_CAN_NOT_BE_EMPTY."<br>";
    }

    if(empty($cust_country)) {
        $valid = 0;
        $error_message .= YOU_MUST_HAVE_TO_SELECT_A_COUNTRY."<br>";
    }

    if(empty($cust_city)) {
        $valid = 0;
        $error_message .= CITY_CAN_NOT_BE_EMPTY."<br>";
    }

    if(empty($cust_state)) {
        $valid = 0;
        $error_message .= STATE_CAN_NOT_BE_EMPTY."<br>";
    }

    if(empty($cust_zip)) {
        $valid = 0;
        $error_message .= ZIP_CODE_CAN_NOT_BE_EMPTY."<br>";
    }

    if($valid == 1) {

        // update data into the database
        $statement = $pdo->prepare("UPDATE tbl_customer SET cust_name=?, cust_phone=?, cust_country=?, cust_address=?, cust_city=?, cust_state=?, cust_zip=?,customer_type=? WHERE cust_id=?");
        $statement->execute(array(
                    $cust_name,
                    $cust_phone,
                    $cust_country,
                    $cust_address,
                    $cust_city,
                    $cust_state,
                    $cust_zip,
                    $cust_type,
                    $_SESSION['customer']['cust_id']
                ));  
       
        $success_message = PROFILE_INFORMATION_IS_UPDATED_SUCCESSFULLY;

        
        $_SESSION['customer']['cust_name'] = $cust_name;
        $_SESSION['customer']['cust_phone'] = $cust_phone;
        $_SESSION['customer']['cust_country'] = $cust_country;
        $_SESSION['customer']['cust_address'] = $cust_address;
        $_SESSION['customer']['cust_city'] = $cust_city;
        $_SESSION['customer']['cust_state'] = $cust_state;
        $_SESSION['customer']['cust_zip'] = $cust_zip;
        $_SESSION['customer']['customer_type'] = $cust_type;
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
                <h3 >
                    <?php echo ACCOUNT_SETTINGS; ?>
                </h3>
                <hr class="mt_10">
            </div>
            
            <div >
                <ul class="nav nav-tabs nav_background">
                    <li class="active"><a href="account-settings.php" >MAIN SETTING</a></li>
                    <li><a href="payment-agreement.php" >PAYMENT AGREEMENT</a></li>
                    <li><a href="change-password.php" >CHANGE PASSWORD</a></li>
                    <li><a href="social-network-and-contact-info.php" >SOCIAL NETWORKS & CONTACT INFO</a></li>
                    <!-- <li><a href="notification-setting.php" >NOTIFICATION SETTINGS</a></li> -->
                </ul>

                
                <div class="tab-content ">

                    <div class="tab-pane active" >
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
                            <form action="" method="post">
                                <?php $csrf->echoInputField(); ?>
                                <div class="row">
                                    
                                    <div class="col-md-6 form-group">
                                        <label for=""><?php echo FULL_NAME; ?> *</label>
                                        <input type="text" class="form-control" name="cust_name" value="<?php echo $_SESSION['customer']['cust_name']; ?>">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for=""><?php echo EMAIL_ADDRESS; ?> *</label>
                                        <input type="text" class="form-control" name="" value="<?php echo $_SESSION['customer']['cust_email']; ?>" disabled>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for=""><?php echo PHONE_NUMBER; ?> *</label>
                                        <input type="text" class="form-control" name="cust_phone" value="<?php echo $_SESSION['customer']['cust_phone']; ?>">
                                    </div>
                                    
                                    <div class="col-md-6 form-group">
                                        <label for=""><?php echo COUNTRY; ?> *</label>
                                        <select name="cust_country" class="form-control">
                                        <?php
                                        $statement = $pdo->prepare("SELECT * FROM tbl_country ORDER BY country_name ASC");
                                        $statement->execute();
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            ?>
                                            <option value="<?php echo $row['country_id']; ?>" <?php if($row['country_id'] == $_SESSION['customer']['cust_country']) {echo 'selected';} ?>><?php echo $row['country_name']; ?></option>
                                            <?php
                                        }
                                        ?>
                                        </select>                                    
                                    </div>
                                    
                                    <div class="col-md-6 form-group">
                                        <label for=""><?php echo CITY; ?> *</label>
                                        <input type="text" class="form-control" name="cust_city" value="<?php echo $_SESSION['customer']['cust_city']; ?>">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for=""><?php echo STATE; ?> *</label>
                                        <input type="text" class="form-control" name="cust_state" value="<?php echo $_SESSION['customer']['cust_state']; ?>">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for=""><?php echo ZIP_CODE; ?> *</label>
                                        <input type="text" class="form-control" name="cust_zip" value="<?php echo $_SESSION['customer']['cust_zip']; ?>">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for=""><?php echo YOU_ARE_INTERESTED_IN; ?> *</label>
                                        <div></div>
                                        <label class="radio-inline">
                                            <input type="radio" name="customer_type" value="buying" <?php echo (($_SESSION['customer']['customer_type']=='buying')?'checked':''); ?> >Buying
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="customer_type" value="selling" <?php echo (($_SESSION['customer']['customer_type']=='selling')?'checked':''); ?>>Selling
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="customer_type" value="both" <?php echo (($_SESSION['customer']['customer_type']=='both')?'checked':''); ?>>Both
                                        </label>
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for=""><?php echo ADDRESS; ?> *</label>
                                        <textarea name="cust_address" class="form-control" cols="10" rows="5" ><?php echo $_SESSION['customer']['cust_address']; ?></textarea>
                                    </div>
                                </div>
                                <input type="submit" class="btn btn-primary" value="<?php echo UPDATE; ?>" name="form1">
                            </form>
                                            
                        </div> 
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