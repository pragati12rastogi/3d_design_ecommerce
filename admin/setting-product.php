<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1']))
{    
    // updating the database
    $statement = $pdo->prepare("UPDATE tbl_setting_post SET total_featured_product_home=?, total_latest_product_home=?, total_popular_product_home=? WHERE id=1");
    $statement->execute(array($_POST['total_featured_product_home'],$_POST['total_latest_product_home'],$_POST['total_popular_product_home']));

    $success_message = 'Post setting is updated successfully.';
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Setting - Product</h1>
    </div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_post WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
    $total_featured_product_home = $row['total_featured_product_home'];
    $total_latest_product_home   = $row['total_latest_product_home'];
    $total_popular_product_home  = $row['total_popular_product_home'];
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
                            <label for="" class="col-sm-4 control-label">Home Page (How many featured product?)<span>*</span></label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="total_featured_product_home" value="<?php echo $total_featured_product_home; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Home Page (How many latest product?)<span>*</span></label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="total_latest_product_home" value="<?php echo $total_latest_product_home; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label">Home Page (How many popular product?)<span>*</span></label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="total_popular_product_home" value="<?php echo $total_popular_product_home; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-4 control-label"></label>
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