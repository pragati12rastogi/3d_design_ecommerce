<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $banner_product_category = $row['banner_product_category'];
}
?>


<div class="page-banner" style="background-image: url(public_files/uploads/<?php echo $banner_product_category; ?>)">
    <div class="inner">
        <h1>FREE MODEL</h1>
        
    </div>
    <div class ="text-center">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><b><a href="index.php">Home</a></b></li>
            <li class="breadcrumb-item"><b>Free Design</b></li>
        </ul>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?php require_once('free-sub-category.php'); ?>
            </div>
            <div class="col-md-9">
                
                <div class="product product-cat">

                    <div class="row">
                        <?php
                        // Checking if any product is available or not
                        $prod_count = 0;
                        
                        if(isset($_REQUEST['type']) && isset($_REQUEST['id']) && $_REQUEST['type'] == 'top-category') {
                            $statement = $pdo->prepare("SELECT * FROM tbl_product where cat_id =? and is_free =? and p_is_active=? and is_delete = 0");
                            $statement->execute(array($_REQUEST['id'],1,1));
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                        }
                        else if( isset($_REQUEST['type']) && isset($_REQUEST['id']) && $_REQUEST['type'] == 'mid-category') {
                            $statement = $pdo->prepare("SELECT * FROM tbl_product where subcat_id =? and is_free =? and p_is_active=? and is_delete = 0");
                            $statement->execute(array($_REQUEST['id'],1,1));
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                        }else{
                            $statement = $pdo->prepare("SELECT * FROM tbl_product where is_free =? and p_is_active=? and is_delete = 0");
                            $statement->execute(array(1,1));
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                        
                        }
                        
                        if(count($result)>0) {
                            
                            foreach ($result as $row) {
                                ?>
                                <div class="col-md-4 item item-product-cat">
                                    <div class="inner">
                                        <div class="thumb">
                                            <div class="photo" style="background-image:url(public_files/uploads/<?php echo $row['p_featured_photo']; ?>);"></div>
                                            <div class="overlay"></div>
                                        </div>
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
                                                    if(CURRENCY_POSITION == 'Before') {
                                                        echo CURRENCY_SYMBOL;
                                                        echo $row['p_current_price'] * CURRENCY_VALUE;
                                                    } else {
                                                        echo $row['p_current_price'] * CURRENCY_VALUE;
                                                        echo CURRENCY_SYMBOL;
                                                    }
                                                    ?> 
                                                    <?php if($row['p_old_price'] != ''): ?>
                                                    <del>
                                                        <?php
                                                        if(CURRENCY_POSITION == 'Before') {
                                                            echo CURRENCY_SYMBOL;
                                                            echo $row['p_old_price'] * CURRENCY_VALUE;
                                                        } else {
                                                            echo $row['p_old_price'] * CURRENCY_VALUE;
                                                            echo CURRENCY_SYMBOL;
                                                        }
                                                        ?>
                                                    </del>
                                                    <?php endif; 
                                                }   ?>
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
                            echo '<div class="pl_15 alert alert-info mt_40">'.NO_PRODUCT_FOUND.'</div>';
                        }
                        ?>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>