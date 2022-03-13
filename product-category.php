<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $banner_product_category = $row['banner_product_category'];
}
?>

<?php
if( !isset($_REQUEST['id']) || !isset($_REQUEST['type']) ) {
    header('location: index.php');
    exit;
} else {

    if( ($_REQUEST['type'] != 'top-category') && ($_REQUEST['type'] != 'mid-category') && ($_REQUEST['type'] != 'tags') ) {
        header('location: index.php');
        exit;
    }else{
        if($_REQUEST['type'] == 'top-category') {
            $statement = $pdo->prepare("SELECT * FROM tbl_top_category where tcat_id =? ");
            $statement->execute([$_REQUEST['id']]);
            $topc_result = $statement->fetch(PDO::FETCH_ASSOC);
            $title = $topc_result['tcat_name'];

        }
        else if($_REQUEST['type'] == 'mid-category') {
            $statement = $pdo->prepare("SELECT * FROM tbl_mid_category where mcat_id =? ");
            $statement->execute([$_REQUEST['id']]);
            $topc_result = $statement->fetch(PDO::FETCH_ASSOC);
            $title = $topc_result['mcat_name'];
        }
        else if($_REQUEST['type'] == 'tags') {
            $title =ucfirst($_REQUEST['id']);
        }
    }   
}
?>

<div class="page-banner" style="background-image: url(public_files/uploads/<?php echo $banner_product_category; ?>)">
    <div class="inner">
        <h1><?php if($_REQUEST['type'] == 'tags') { echo "TAG : "; }else{ echo CATEGORY_COLON; }; ?> <?php echo $title; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?php require_once('sidebar-category.php'); ?>
            </div>
            <div class="col-md-9">
                
                <h3><?php echo ALL_PRODUCTS_UNDER. (($_REQUEST['type'] == 'tags')?" TAG":"");  ?> "<?php echo $title; ?>"</h3>
                <div class="product product-cat">

                    <div class="row">
                        <?php
                        // Checking if any product is available or not
                        $prod_count = 0;
                        if($_REQUEST['type'] == 'top-category') {
                            $statement = $pdo->prepare("SELECT * FROM tbl_product where cat_id =? and p_is_active=? and is_delete = 0");
                            $statement->execute(array($_REQUEST['id'],1));
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                        }
                        else if($_REQUEST['type'] == 'mid-category') {
                            $statement = $pdo->prepare("SELECT * FROM tbl_product where subcat_id =? and p_is_active=? and is_delete = 0");
                            $statement->execute(array($_REQUEST['id'],1));
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                        }
                        else if($_REQUEST['type'] == 'tags') {
                            $statement = $pdo->prepare("SELECT * FROM tbl_product where p_tags LIKE '%".$_REQUEST['id']."%' and p_is_active=1 and is_delete = 0");
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                        }

                        
                        
                        if(count($result)>0) {
                            
                            foreach ($result as $row) {
                                $discount_applied = 0;

                                
                                if($row['user_type'] == 'Customer'){
                                    $check_discount_table = get_sale_discount_func($pdo,$row['user_id']);
                                    $discount_applied    = $check_discount_table['discount_applied'];
                                        
                                }
                                ?>
                                
                                <div class="col-md-4 item item-product-cat">
                                    <?php
                                    if($row['is_free'] == 0){
                                        if(!empty($sale_date_setting_list)){ 
                                            if($discount_applied != 0){
                                                $final_sale_check = final_sale_check($sale_date_setting_list,$check_discount_table);
                                                if($final_sale_check['sale_type'] != 'none'){
                                                    ?>
                                                    <p class="m_0">
                                                        <span class="bolt-design-tag">
                                                            <?php if($final_sale_check['sale_type']== 'sale' ){ 
                                                                
                                                                ?>
                                                            <i class="fa fa-bolt "></i>
                                                            <?php } elseif($final_sale_check['sale_type']== 'special_sale'){ 
                                                                
                                                                ?>
                                                                <i class="fa fa-bolt "></i>
                                                                <i class="fa fa-bolt "></i>
                                                            <?php } ?>
                                                        </span>
                                                        
                                                        <i class="prod-percent-tag"> <?php echo $final_sale_check['perc'] ?> %</i>
                                                    </p>
                                                    <?php 
                                                } 
                                            }
                                        } 
                                    } 
                                    ?>
                                    <div class="inner">
                                        <a href="product.php?id=<?php echo $row['p_id']; ?>">
                                        <div class="thumb">
                                            <div class="photo" style="background-image:url(public_files/uploads/<?php echo $row['p_featured_photo']; ?>);"></div>
                                            <div class="overlay"></div>
                                        </div>
                                        </a>
                                        <div class="text">
                                            <h3><a href="product.php?id=<?php echo $row['p_id']; ?>"><?php echo $row['p_name']; ?></a></h3>
                                            <?php if(!empty($row['file_extension'])){
                                                echo "<p class='m_0'><small><b>Files: </b>".$row['file_extension']."</small></p>";
                                            }
                                            ?>
                                            <h4>
                                                <?php
                                                if($row['is_free'] == 1) {
                                                    echo '<span style="color:red">Free</span>';
                                                }else{
                                                    if(!empty($sale_date_setting_list)){ 

                                                        if($discount_applied != 0){
            
                                                            $final_sale_check = final_sale_check($sale_date_setting_list,$check_discount_table);
            
                                                            if($final_sale_check['sale_type'] != 'none'){
            
                                                                $dis_percentage = $final_sale_check['perc'];
            
                                                                
                                                                if(!empty($row['p_old_price'])){
            
                                                                    $get_discounted_price = $row['p_old_price']*($dis_percentage/100);
                                                                    $actual_price = $row['p_old_price']-$get_discounted_price;
            
                                                                    if(CURRENCY_POSITION == 'Before') {
                                                                        echo CURRENCY_SYMBOL.' '.number_format(($actual_price * CURRENCY_VALUE),2);
                                                                        echo ' <del>'.CURRENCY_SYMBOL.' '.number_format(($row['p_old_price'] * CURRENCY_VALUE),2).'</del>';
                                                                    } else {
                                                                        echo number_format(($actual_price * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL;
                                                                        echo ' <del>'.number_format(($row['p_old_price'] * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL.'</del>';
                                                                    }
                                                                    
            
                                                                }else{
            
                                                                    $get_discounted_price = $row['p_current_price']*($dis_percentage/100);
                                                                    $actual_price = $row['p_current_price']-$get_discounted_price;
                                                                    
                                                                    if(CURRENCY_POSITION == 'Before') {
                                                                        echo CURRENCY_SYMBOL.' '.number_format(($actual_price * CURRENCY_VALUE),2);
                                                                        echo ' <del>'.CURRENCY_SYMBOL.' '.number_format(($row['p_current_price'] * CURRENCY_VALUE),2).'</del>';
                                                                    } else {
                                                                        echo number_format(($actual_price * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL;
                                                                        echo ' <del>'.number_format(($row['p_current_price'] * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL.'</del>';
                                                                    }
                                                                }
            
                                                                
            
                                                            }else{
                                                                echo default_prod_price($row);
                                                            }
                                                        }else{
                                                            echo default_prod_price($row);
                                                        }
                                                    }else{
                                                        echo default_prod_price($row);
                                                    } 
                                                }   
                                                ?>
                                            </h4>
                                            <div class="rating">
                                                <?php
                                                $t_rating = 0;
                                                $statement1 = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=?");
                                                $statement1->execute(array($row['p_id']));
                                                $tot_rating = $statement1->rowCount();
                                                if($tot_rating == 0) {
                                                    $avg_rating = 0;
                                                } else {
                                                    $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($result1 as $row1) {
                                                        $t_rating = $t_rating + $row1['rating'];
                                                    }
                                                    $avg_rating = $t_rating / $tot_rating;
                                                }
                                                ?>
                                                <?php
                                                if($avg_rating == 0) {
                                                    echo '';
                                                }
                                                elseif($avg_rating == 1.5) {
                                                    echo '
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star-half-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                    ';
                                                } 
                                                elseif($avg_rating == 2.5) {
                                                    echo '
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star-half-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                    ';
                                                }
                                                elseif($avg_rating == 3.5) {
                                                    echo '
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star-half-o"></i>
                                                        <i class="fa fa-star-o"></i>
                                                    ';
                                                }
                                                elseif($avg_rating == 4.5) {
                                                    echo '
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star-half-o"></i>
                                                    ';
                                                }
                                                else {
                                                    for($i=1;$i<=5;$i++) {
                                                        ?>
                                                        <?php if($i>$avg_rating): ?>
                                                            <i class="fa fa-star-o"></i>
                                                        <?php else: ?>
                                                            <i class="fa fa-star"></i>
                                                        <?php endif; ?>
                                                        <?php
                                                    }
                                                }

                                                $wishlist_icon = wishlist_function($pdo,$row['p_id']);
                                                ?>
                                            </div>
                                            <?php if($row['is_free'] == 1): ?>
                                                <p><a style="cursor: pointer;" class="wishlist_btn" data-id="<?php echo $row['p_id']; ?>"><i class="<?php echo $wishlist_icon; ?>"></i></a> &nbsp; &nbsp;
                                                <?php if(file_exists("public_files/uploads/model_product_files/".$row['prod_model_file'])){ ?>
                                                    <a href="product.php?id=<?php echo $row['p_id']; ?>"><i class="fa fa-download"></i> Download</a>
                                                <?php } ?></p>
                                            <?php else: ?>
                                                
                                                <p><a style="cursor: pointer;" class="wishlist_btn" data-id="<?php echo $row['p_id']; ?>"><i class="<?php echo $wishlist_icon; ?>"></i></a> &nbsp; &nbsp;<a href="product.php?id=<?php echo $row['p_id']; ?>"><i class="fa fa-cart-arrow-down"></i> Add to Cart</a></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            
                        }
                        else {
                            echo '<div class="pl_15">'.NO_PRODUCT_FOUND.'</div>';
                        }
                        ?>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>