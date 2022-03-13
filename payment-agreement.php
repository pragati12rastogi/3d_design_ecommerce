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

   $get_the_record_sql = $pdo->prepare('SELECT * from tbl_customer where cust_id=?'); 
   $get_the_record_sql->execute([$_SESSION['customer']['cust_id']]);
   $get_the_record = $get_the_record_sql->fetch(PDO::FETCH_ASSOC);
}

if(isset($_POST['form_agreement'])){

    $valid = 1;
    $sign_type = $_POST['sign_type'];

    if(empty($sign_type)){
        $valid = 0;
        $error_message .= "Please select sign type. <br>";
    }else{
        if($sign_type == 'business'){

            if(empty($_POST['cust_cname'])){
                $valid = 0;
                $error_message .= "Please fill company name. <br>";
            }

            if(empty($_POST['cust_b_country'])){
                $valid = 0;
                $error_message .= "Please select company country. <br>";
            }

            if(empty($_POST['cust_b_city'])){
                $valid = 0;
                $error_message .= "Please fill company city. <br>";
            }

            if(empty($_POST['cust_b_zip'])){
                $valid = 0;
                $error_message .= "Please fill company zipcode. <br>";
            }

        }else if($sign_type == 'person'){

            if(empty($_POST['identity_number'])){
                $valid = 0;
                $error_message .= "Please fill Identification Number. <br>";
            }
        }

    }

    if(empty($_POST['cust_pp_email'])){
        $valid = 0;
        $error_message .= "Please fill paypal email. <br>";
    }

    if(empty($_POST['cust_bank_owner_name'])){
        $valid = 0;
        $error_message .= "Please fill Bank Owner Name. <br>";
    }

    if(empty($_POST['cust_acc_no'])){
        $valid = 0;
        $error_message .= "Please fill Account Number. <br>";
    }

    if(empty($_POST['cust_bank_ifsc'])){
        $valid = 0;
        $error_message .= "Please fill IFSC code. <br>";
    }

    $get_proof_old_record = $pdo->prepare("SELECT * FROM tbl_agreement_proof WHERE customer_id=?");
	$get_proof_old_record->execute(array($_SESSION['customer']['cust_id']));
	$get_proof = $get_proof_old_record->fetchAll(PDO::FETCH_ASSOC);	

    

    if(count($get_proof)<=0){
        if(count(array_filter($_FILES['id_proofs']['name']))<=0){
            $valid = 0;
            $error_message .= "Please Attach proofs for verification. <br>";
        }
    }
    
    if($valid == 1){
        if(count(array_filter($_FILES['id_proofs']['name']))>0){
            $proof_files = array_filter($_FILES['id_proofs']['name']);
            $proof_files_temp = array_filter($_FILES['id_proofs']["tmp_name"]);
            foreach($proof_files as $ind => $proof ){
                $proof_extention = pathinfo( $proof, PATHINFO_EXTENSION );
                $final_name = $proof.rand(10,100)."_".time().'.'.$proof_extention;

                move_uploaded_file($proof_files_temp[$ind],"public_files/uploads/agreement_photo/".$final_name);

                $statement = $pdo->prepare("INSERT INTO tbl_agreement_proof (proof_file,customer_id) VALUES (?,?)");
                $statement->execute(array($final_name,$_SESSION['customer']['cust_id']));
            }
        }

        $update_customer = $pdo->prepare('Update `tbl_customer` SET sign_type=?, cust_cname = ?, vat_b_identifier= ?, cust_b_code=?, cust_b_country=?, cust_b_city=?, cust_b_zip=?, identity_number=?, cust_pp_email=? ,cust_bank_owner_name=?, cust_acc_no=?, cust_bank_ifsc=?,vat_p_identifier=? where cust_id=?');

        if($sign_type == 'business'){
            $update_customer->execute([
                $_POST['sign_type'],
                $_POST['cust_cname'],
                $_POST['vat_b_identifier'],
                $_POST['cust_b_code'],
                $_POST['cust_b_country'],
                $_POST['cust_b_city'],
                $_POST['cust_b_zip'],
                '',
                $_POST['cust_pp_email'],
                $_POST['cust_bank_owner_name'],
                $_POST['cust_acc_no'],
                $_POST['cust_bank_ifsc'],
                '',
                $_SESSION['customer']['cust_id']
            ]);
        }else{
            $update_customer->execute([
                $_POST['sign_type'],
                '',
                '',
                '',
                '',
                '',
                '',
                $_POST['identity_number'],
                $_POST['cust_pp_email'],
                $_POST['cust_bank_owner_name'],
                $_POST['cust_acc_no'],
                $_POST['cust_bank_ifsc'],
                $_POST['vat_p_identifier'],
                $_SESSION['customer']['cust_id']
            ]);
        }
        


        $success_message = 'Agreement is updated successfully.';


        $email_setting = $pdo->prepare("SELECT * FROM tbl_setting_email WHERE id=1");
        $email_setting->execute();
        $result = $email_setting->fetch(PDO::FETCH_ASSOC);                            
        
        $send_email_from                 = $result['send_email_from'];
        $receive_email_to                = $result['receive_email_to'];

        $to = $get_the_record['cust_email'];
        
        $subject = 'Payment Agreement Form Updated';
        
        if($_POST['sign_type'] == 'business'){
            $type_msg = '<tr>
                        th>Coumpany Name</th>
                        <td>'.$_POST['cust_cname'].'</td>
                    </tr>
                    <tr>
                        <th>VAT Identifier</th>
                        <td>'.$_POST['vat_b_identifier'].'</td>
                    </tr>
                    <tr>
                        <th>Company Code</th>
                        <td>'.$_POST['cust_b_code'].'</td>
                    </tr>
                    <tr>
                        <th>Company Country</th>
                        <td>'.$_POST['cust_b_country'].'</td>
                    </tr>
                    <tr>
                        <th>Company City</th>
                        <td>'.$_POST['cust_b_city'].'</td>
                    </tr>
                    <tr>
                        <th>Company Zipcode</th>
                        <td>'.$_POST['cust_b_zip'].'</td>
                    </tr>
                    ';

        }else{
            $type_msg = '<tr>
                        th>Identification Number</th>
                        <td>'.$_POST['identity_number'].'</td>
                    </tr>
                    <tr>
                        <th>VAT</th>
                        <td>'.$_POST['vat_p_identifier'].'</td>
                    </tr>
                    ';
        }
        
        $message = '<html>
        <style>
            table, th, td {
            border:1px solid black;
            }
        </style>
        <body>
        <p>Greetings <b>'.$get_the_record['cust_name'].'</b>,</p>
        <p></p>
        <table style="width:100%">
            <tr>
                <th>Sign Agreement As</th>
                <td>'.$_POST['sign_type'].'</td>
            </tr>
            '.$type_msg.'
            
        </table>
        <p>If you didn\'t made changes ,we suggest to change your password.</p>
        </body></html>';
            
	
        try {
		    
    	    $mail->setFrom($send_email_from, 'Admin');
    	    $mail->addAddress($to, $get_the_record['cust_name'] );
    	    $mail->addReplyTo($receive_email_to, 'Admin');
    	    
    	    $mail->isHTML(true);
    	    $mail->Subject = $subject;

    	    $mail->MsgHTML($message);
            $mail->Send();

    	        
    	} catch (Exception $e) {
    	    echo 'Message could not be sent.';
    	    echo 'Mailer Error: ' . $mail->ErrorInfo;
    	}

        $get_the_record_sql = $pdo->prepare('SELECT * from tbl_customer where cust_id=?'); 
        $get_the_record_sql->execute([$_SESSION['customer']['cust_id']]);
        $get_the_record = $get_the_record_sql->fetch(PDO::FETCH_ASSOC);
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
                    <?php echo PAYEMENT_AGREEMENT; ?>
                </h3>
                <hr class="mt_10">
            </div>
            
            <div >
                <ul class="nav nav-tabs nav_background">
                    <li ><a href="account-settings.php" >MAIN SETTING</a></li>
                    <li class="active"><a href="payment-agreement.php" >PAYMENT AGREEMENT</a></li>
                    <li><a href="change-password.php" >CHANGE PASSWORD</a></li>
                    <li><a href="social-network-and-contact-info.php" >SOCIAL NETWORKS & CONTACT INFO</a></li>
                    <!-- <li><a href="notification-setting.php" >NOTIFICATION SETTINGS</a></li> -->
                </ul>

                
                <div class="tab-content ">

                    <div class="tab-pane active" >
                        <div class="col-md-12">
                            <?php
                            if(!empty($error_message)) {
                        
                                echo "<div class='alert alert-danger' id='agreement-error' style='margin-bottom:20px;'>".$error_message."</div>";
                            ?>
                            <script>
                                setTimeout(function() {
                                    $("#agreement-error").remove();
                                }, 5000);
                            </script>
                            <?php
                            }
                            if($success_message != '') {
                                echo "<div class='alert alert-success' id='agreement-success' style='margin-bottom:20px;'>".$success_message."</div>";
                            ?>
                            <script>
                                setTimeout(function() {
                                    $("#agreement-success").remove();
                                }, 5000);
                            </script>
                            <?php
                            }
                            ?>
                            <label>In order to receive payments, you first need to sign the agreement and fill out the form.</label>   
                            <br>
                            <br>
                            <div class="agree-form-div">
                                <h4>Agreement Form</h4>
                                <hr>
                                <form action="" method="post" enctype="multipart/form-data">
                                    <?php $csrf->echoInputField(); ?>
                                    
                                    <div class="row">

                                        <div class="col-md-12 form-group">
                                            <label class="mr_20">Choose Sign Type *</label>
                                            
                                            <label class="radio-inline">
                                                <input type="radio" name="sign_type" onclick="choosen_agreement()" required value="business" <?php echo (($get_the_record['sign_type']=='business')?'checked':''); ?> >Sign As Business
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="sign_type" onclick="choosen_agreement()" value="person" <?php echo (($get_the_record['sign_type']=='person')?'checked':''); ?>>Sign As Person
                                            </label>
                                            
                                        </div>
                                        
                                        <div class="" id="business_div" style="display:none" >
                                            <div class="col-md-6 form-group">
                                                <label for=""><?php echo COMPANY_NAME; ?> *</label>
                                                <input type="text" class="form-control" name="cust_cname" value="<?php echo $get_the_record['cust_cname']; ?>">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="">VAT Identifier</label>
                                                <input type="text" class="form-control" name="vat_b_identifier" value="<?php echo $get_the_record['vat_b_identifier']; ?>">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="">Company Code</label>
                                                <input type="text" class="form-control" name="cust_b_code" value="<?php echo $get_the_record['cust_b_code']; ?>">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="">Company Country *</label>
                                                <select name="cust_b_country" class="form-control select2" style="width:100%;">
                                                    <option value=""><?php echo SELECT_COUNTRY; ?></option>
                                                    <?php
                                                    $statement = $pdo->prepare("SELECT * FROM tbl_country ORDER BY country_name ASC");
                                                    $statement->execute();
                                                    $result = $statement->fetchAll();                            
                                                    foreach ($result as $row) {
                                                        ?>
                                                        <option value="<?php echo $row['country_id']; ?>" <?php echo  $get_the_record['cust_b_country']==$row['country_id']?'selected':''; ?> ><?php echo $row['country_name']; ?></option>
                                                        <?php
                                                    }
                                                    ?>    
                                                </select> 
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="">Company City *</label>
                                                <input type="text" class="form-control" name="cust_b_city" value="<?php echo $get_the_record['cust_b_city']; ?>">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="">Company Zipcode *</label>
                                                <input type="text" class="form-control" name="cust_b_zip" value="<?php echo $get_the_record['cust_b_zip']; ?>">
                                            </div>
                                        </div>

                                        <div class="" id="person_div" style="display:none"  >
                                            <div class="col-md-6 form-group">
                                                <label title="Identification Number is a number of document(Passport,ID,Driver's License,etc.) which may be used to verify aspect of a person's personal identity">Identification Number *</label>
                                                <input type="text" class="form-control" name="identity_number" value="<?php echo $get_the_record['identity_number']; ?>">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="">VAT </label>
                                                <input type="text" class="form-control" name="vat_p_identifier" value="<?php echo $get_the_record['vat_p_identifier']; ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-12 form-group">
                                            <label id="sign_data">Please attach a copy of your business certificate, VAT ID or any business existence proof document in order to receive payments  *</label>
                                            <input type="file" name="id_proofs[]" accept="pdf,xlsx,xls" multiple>
                                            
                                            <div class="col-sm-6" style="padding-top:4px;">
                                                <table id="agree_proofs" style="width:100%;">
                                                    <tbody>
                                                        <?php
                                                        $statement = $pdo->prepare("SELECT * FROM tbl_agreement_proof WHERE customer_id=?");
                                                        $statement->execute(array($_SESSION['customer']['cust_id']));
                                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($result as $row) {
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <a href="public_files/uploads/agreement_photo/<?php echo $row['proof_file']; ?>" alt="agreement proofs" download><?php echo $row['proof_file']; ?></a>
                                                                </td>
                                                                <td style="width:28px;">
                                                                    <a onclick="return confirmDelete();" href="ajax_function.php?agree_proof_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-xs">X</a>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <hr>
                                        
                                        <div class="col-md-12 form-group">
                                        <h4 for="">Paypal Details </h4>
                                        <hr>
                                            <label for="">Paypal Email Address *</label>
                                            <input type="email" class="form-control" name="cust_pp_email" value="<?php echo $get_the_record['cust_pp_email']; ?>">
                                        </div>

                                        <div class="col-md-12 form-group">
                                        <h4 for="">Bank Details </h4>
                                        <hr>    
                                            <label for="">Bank Owner Name *</label>
                                            
                                            <input type="text" class="form-control" name="cust_bank_owner_name" value="<?php echo $get_the_record['cust_bank_owner_name']; ?>">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="">Bank Account Number *</label>
                                            <input type="number" class="form-control" name="cust_acc_no" value="<?php echo $get_the_record['cust_acc_no']; ?>">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="">Bank IFSC Code *</label>
                                            <input type="text" class="form-control" name="cust_bank_ifsc" value="<?php echo $get_the_record['cust_bank_ifsc']; ?>">
                                        </div>
                                        
                                        
                                        <div class="col-md-4 form-group" style="float: right;">
                                        
                                            <input type="submit" class="btn btn-primary btn-block" value="Confirm And Sign Agreement" name="form_agreement">
                                        </div>

                                    </div>
                                </form> 
                            </div>
                        </div> 
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

<script>

    $(document).ready(function(){
        choosen_agreement();
    })
    function choosen_agreement(){
        var agree = $('input[name="sign_type"]:checked').val();

        if(agree == 'business'){
            $("#business_div").show();
            $("#person_div").hide();
            $("#sign_data").text('Please attach a copy of your business certificate, VAT ID or any business existence proof document in order to receive payments  *');
        }else if(agree == 'person'){
            $("#business_div").hide();
            $("#person_div").show();
            $("#sign_data").text('Please attach a copy of your ID, passport or social security in order to receive payments *');
        }
        
    }

    
</script>

<style>
    .nav_background{
        background: transparent !important;
    }

    .agree-form-div{
        padding: 15px;
        border: 1px solid #b4afaf;
        border-radius: 10px;
    }
    
</style>

<?php require_once('footer.php'); ?>