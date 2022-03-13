<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
    
    $statement = $pdo->prepare("UPDATE tbl_setting_advertisement 
                            SET 
                            ads_above_welcome_on_off=?, 
                            ads_above_featured_product_on_off=?, 
                            ads_above_latest_product_on_off=?, 
                            ads_above_popular_product_on_off=?, 
                            ads_above_testimonial_on_off=?, 
                            ads_category_sidebar_on_off=? 

                            WHERE id=1");
    $statement->execute(array(
                            $_POST['ads_above_welcome_on_off'],
                            $_POST['ads_above_featured_product_on_off'],
                            $_POST['ads_above_latest_product_on_off'],
                            $_POST['ads_above_popular_product_on_off'],
                            $_POST['ads_above_testimonial_on_off'],
                            $_POST['ads_category_sidebar_on_off']
                        ));

    $success_message = 'Advertisement On Off setting is updated successfully.';
    
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Setting - Advertisement</h1>
    </div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_advertisement WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                           
foreach ($result as $row) {
    $ads_above_welcome_on_off          = $row['ads_above_welcome_on_off'];
    $ads_above_featured_product_on_off = $row['ads_above_featured_product_on_off'];
    $ads_above_latest_product_on_off   = $row['ads_above_latest_product_on_off'];
    $ads_above_popular_product_on_off  = $row['ads_above_popular_product_on_off'];
    $ads_above_testimonial_on_off      = $row['ads_above_testimonial_on_off'];
    $ads_category_sidebar_on_off       = $row['ads_category_sidebar_on_off'];
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

            <h3 class="s-title">Advertisements On and Off</h3>
            <form class="form-horizontal" action="" method="post">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Above Welcome </label>
                            <div class="col-sm-4">
                                <select name="ads_above_welcome_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($ads_above_welcome_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($ads_above_welcome_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Above Featured Product </label>
                            <div class="col-sm-4">
                                <select name="ads_above_featured_product_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($ads_above_featured_product_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($ads_above_featured_product_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Above Latest Product </label>
                            <div class="col-sm-4">
                                <select name="ads_above_latest_product_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($ads_above_latest_product_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($ads_above_latest_product_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Above Popular Product </label>
                            <div class="col-sm-4">
                                <select name="ads_above_popular_product_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($ads_above_popular_product_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($ads_above_popular_product_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Above Testimonial </label>
                            <div class="col-sm-4">
                                <select name="ads_above_testimonial_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($ads_above_testimonial_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($ads_above_testimonial_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Category Page Sidebar </label>
                            <div class="col-sm-4">
                                <select name="ads_category_sidebar_on_off" class="form-control" style="width:auto;">
                                    <option value="1" <?php if($ads_category_sidebar_on_off == 1) {echo 'selected';} ?>>On</option>
                                    <option value="0" <?php if($ads_category_sidebar_on_off == 0) {echo 'selected';} ?>>Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label"></label>
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