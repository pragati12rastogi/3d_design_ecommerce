<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_page WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $contact_title = $row['contact_title'];
    $contact_banner = $row['contact_banner'];
}
$statement = $pdo->prepare("SELECT * FROM tbl_setting_contact WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $contact_map_iframe = $row['contact_map_iframe'];
    $contact_email = $row['contact_email'];
    $contact_phone = $row['contact_phone'];
    $contact_address = $row['contact_address'];
    $watsapp_number = $row['watsapp_number'];
    $show_map = $row['show_map'];
}
?>

<div class="page-banner" style="background-image: url(public_files/uploads/<?php echo $contact_banner; ?>);">
    <div class="inner">
        <h1><?php echo $contact_title; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">            
            <div class="col-md-12">
                <h3>Contact Form</h3>
                <div class="row cform">
                    <div class="col-md-8">
                        <div class="well well-sm">
                            
<?php
// After form submit checking everything for email sending
if(isset($_POST['form_contact']))
{
    $error_message = '';
    $success_message = '';
    
    $setting_email_query = $pdo->prepare("SELECT * FROM tbl_setting_email WHERE id=1");
    $setting_email_query->execute();
    $setting_email = $setting_email_query->fetch(PDO::FETCH_ASSOC);                           
    
    $receive_email_subject = $setting_email['receive_email_subject'];
    
    
    $valid = 1;

    if(empty($_POST['visitor_name']))
    {
        $valid = 0;
        $error_message .= 'Please enter your name.<br>';
    }

    if(empty($_POST['visitor_phone']))
    {
        $valid = 0;
        $error_message .= 'Please enter your phone number.<br>';
    }

    if(empty($_POST['visitor_email']))
    {
        $valid = 0;
        $error_message .= 'Please enter your email address.<br>';
    }
    else
    {
        // Email validation check
        if(!filter_var($_POST['visitor_email'], FILTER_VALIDATE_EMAIL))
        {
            $valid = 0;
            $error_message .= 'Please enter a valid email address.<br>';
        }
    }

    if(empty($_POST['visitor_message']))
    {
        $valid = 0;
        $error_message .= 'Please enter your message.<br>';
    }

    if($valid == 1)
    {

        $visitor_name = strip_tags($_POST['visitor_name']);
        $visitor_email = strip_tags($_POST['visitor_email']);
        $visitor_phone = strip_tags($_POST['visitor_phone']);
        $visitor_message = strip_tags($_POST['visitor_message']);

        $message = "*".$receive_email_subject."*".'%0AVisitor%20Name:'.$visitor_name.'%0AVisitor%20Email:'.$visitor_email.'%0AVisitor%20Phone:'.$visitor_phone.'%0AVisitor%20Message:'.urlencode($visitor_message);
        // sending email
        $url = "https://wa.me/".urlencode($watsapp_number)."?text=".$message;
        
        header('location:'.$url);
        exit;


    }
}
?>
                
                <?php
                if($error_message != '') {
                    echo "<div class='alert alert-danger' id='contact-error' style='margin-bottom:20px;'>".$error_message."</div>";
                }
                if($success_message != '') {
                    echo "<div class='alert alert-success' id='contact-success' style='margin-bottom:20px;'>".$success_message."</div>";
                }
                ?>
                <script>
                    setTimeout(function() {
                        $("#contact-error").remove();
                        $("#contact-success").remove();
                    }, 5000);
                </script>


                            <form action="" method="post" >
                            <?php $csrf->echoInputField(); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="visitor_name" placeholder="Enter name">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" class="form-control" name="visitor_email" placeholder="Enter email address">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Phone Number</label>
                                        <input type="text" class="form-control" name="visitor_phone" placeholder="Enter phone number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Message</label>
                                        <textarea name="visitor_message" class="form-control" rows="9" cols="25" placeholder="Enter message"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <input type="submit" value="Send Message" class="btn btn-primary pull-right" name="form_contact">
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <legend><span class="glyphicon glyphicon-globe"></span> Our office</legend>
                        <address>
                            <?php echo nl2br($contact_address); ?>
                        </address>
                        <address>
                            <strong>Phone:</strong><br>
                            <span><?php echo $contact_phone; ?></span>
                        </address>
                        <address>
                            <strong>Email:</strong><br>
                            <a href="mailto:<?php echo $contact_email; ?>"><span><?php echo $contact_email; ?></span></a>
                        </address>
                    </div>
                </div>

                
                <?php 

                    if($show_map){
                        echo "<h3>Find Us On Map</h3>";
                        echo $contact_map_iframe;
                    }
                
                ?>
                
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>