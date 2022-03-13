<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
    
    // updating the database
    $statement = $pdo->prepare("UPDATE tbl_setting_footer SET newsletter_on_off=?, footer_about=?, footer_copyright=? WHERE id=1");
    $statement->execute(array($_POST['newsletter_on_off'],$_POST['footer_about'],$_POST['footer_copyright']));

    $success_message = 'Footer setting is updated successfully.';
    
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Setting - Footer</h1>
    </div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_footer WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                           
foreach ($result as $row) {
    $newsletter_on_off = $row['newsletter_on_off'];
    $footer_about      = $row['footer_about'];
    $footer_copyright  = $row['footer_copyright'];
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
                            <label for="" class="col-sm-2 control-label">Newsletter Section </label>
                            <div class="col-sm-3">
                                <select name="newsletter_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($newsletter_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($newsletter_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Footer - About Us </label>
                            <div class="col-sm-9">
                                <textarea class="form-control ckeditor" name="footer_about"><?php echo $footer_about; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Footer - Copyright </label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="footer_copyright" value="<?php echo $footer_copyright; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"></label>
                            <div class="col-sm-6">
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