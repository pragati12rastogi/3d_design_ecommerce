<?php 

require_once('header.php');

if(isset($_POST['spl_req_submit'])){
    
    $statement = $pdo->prepare("SELECT * FROM tbl_setting_email WHERE id=1");
    $statement->execute();
    $setting_email = $statement->fetchAll();                           
    foreach ($setting_email as $row) 
    {
        $send_email_from = $row['send_email_from'];
        $receive_email_to = $row['receive_email_to'];
        
    }
    
    $valid= 1;
    $error ='';

    if(empty($_POST['spl_req_username'])){
        $valid = 0;
        $error .= 'Username field is required<br>';
    }
    if(empty($_POST['spl_req_email'])){
        $valid = 0;
        $error .= 'Email field is required<br>';
    }
    if(empty($_POST['spl_req_phone'])){
        $valid = 0;
        $error .= 'Phone field is required<br>';
    }
    if(empty($_POST['spl_req_requirements'])){
        $valid = 0;
        $error .= 'Requirement field is required<br>';
    }

    $path = $_FILES['spl_req_file']['name'];
    $path_tmp = $_FILES['spl_req_file']['tmp_name'];
    
    if(!empty($path)){
        $size =  $_FILES['spl_req_file']['size'];
        if($size > 4194304) {
            $valid = 0;
            $error .= 'File Size Cannot be greater than 4MB <br>';

        }
    }
   
    if($valid){

        $visitor_name = strip_tags($_POST['spl_req_username']);
        $visitor_email = strip_tags($_POST['spl_req_email']);
        $visitor_phone = strip_tags($_POST['spl_req_phone']);
        $visitor_message = strip_tags($_POST['spl_req_requirements']);

        if(!empty($path)){
            $ext = pathinfo( $path, PATHINFO_EXTENSION );
            $final_name = 'specialRequirement_'.time().'.'.$ext;
            move_uploaded_file( $path_tmp, 'public_files/uploads/special_requirement/'.$final_name );
        }
          
        // sending email
        $to_admin = $receive_email_to;
        $subject = "Special Requirement";
        $message = '
            <html><body>
            <b>Name:</b><br>'.$visitor_name.'<br><br>
            <b>Email:</b><br>'.$visitor_email.'<br><br>
            <b>Phone:</b><br>'.$visitor_phone.'<br><br>
            <b>Requirement:</b><br>'.nl2br($visitor_message).'
            </body></html>
        ';
        
        try {
		
	    $mail->setFrom($send_email_from, 'Admin');
	    $mail->addAddress($to_admin, 'Admin');
	    $mail->addReplyTo($visitor_email, $visitor_name);
        
        if(!empty($path)){
            $mail->addAttachment("public_files/uploads/special_requirement/".$final_name);
        }

	    $mail->Subject = $subject;

	    $mail_body = $message;
        $mail->MsgHTML($mail_body);
	    
        if($mail->Send())
        {      
	        $success_message = 'Thankyou !! we will connect you shortly';
            setcookie('special_requirement_success',$success_message,time() + 5);
        }else{
            
            setcookie('special_requirement_error','Mailer Error: ' . $mail->ErrorInfo,time() + 5);
        }
          
        
	} catch (Exception $e) {
	    
        setcookie('special_requirement_error','Mailer Exception Error: ' . $mail->ErrorInfo,time() + 5);
	}

    }else{
        setcookie('special_requirement_error',$error,time() + 5);
    }
    
    if(!empty($final_name) && file_exists('public_files/uploads/special_requirement/'.$final_name)){
        unlink('public_files/uploads/special_requirement/'.$final_name);  
    }

    header('location: index.php');

    exit();
}

?>