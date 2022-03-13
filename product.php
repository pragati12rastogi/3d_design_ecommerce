<?php require_once('header.php'); ?>

<?php
if(!isset($_REQUEST['id'])) {
    header('location: index.php');
    exit;
} else {
    // Check the id is valid or not
    $statement = $pdo->prepare("SELECT t1.*,t2.tcat_name,t3.mcat_name FROM tbl_product t1 LEFT JOIN tbl_top_category t2 on t1.cat_id=t2.tcat_id LEFT JOIN tbl_mid_category t3 on t3.mcat_id = t1.subcat_id  WHERE p_id=?");
    $statement->execute(array($_REQUEST['id']));
    $total = $statement->rowCount();
    $product_detail = $statement->fetch(PDO::FETCH_ASSOC);
    if( $total == 0 ) {
        header('location: index.php');
        exit;
    }
    else {

        $mcat_id = $product_detail['subcat_id'];
        $mcat_name = $product_detail['mcat_name'];

        $tcat_id = $product_detail['cat_id'];
        $tcat_name = $product_detail['tcat_name'];

        $p_name = $product_detail['p_name'];
        $p_id = $product_detail['p_id'];

        $p_old_price = $product_detail['p_old_price'];
        $p_current_price = $product_detail['p_current_price'];

        $p_sku = $product_detail['p_sku'];
        $youtube_prev = $product_detail['youtube_prev'];
        $vimeo_prev = $product_detail['vimeo_prev'];
        $p_tags = $product_detail['p_tags'];
        $prod_model_file = $product_detail['prod_model_file'];

        $p_featured_photo = $product_detail['p_featured_photo'];
        $p_description = $product_detail['p_description'];

        $p_feature = $product_detail['p_feature'];
        $p_license = $product_detail['p_license'];
        $p_custom_license = $product_detail['p_custom_license'];
        $p_total_view = $product_detail['p_total_view'];
        $p_is_featured = $product_detail['p_is_featured'];
        $p_is_active = $product_detail['p_is_active'];
        $is_free = $product_detail['is_free'];
        
        $user_type = $product_detail['user_type'];
        $tbl_user_id = $product_detail['user_id'];
        $file_extension = $product_detail['file_extension'];
        $is_delete = $product_detail['is_delete'];
    }
}



// Getting all categories name for breadcrumb

$p_total_view = $p_total_view + 1;

$statement = $pdo->prepare("UPDATE tbl_product SET p_total_view=? WHERE p_id=?");
$statement->execute(array($p_total_view,$_REQUEST['id']));




if(isset($_POST['form_review'])) {
    
    $statement = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=? AND cust_id=?");
    $statement->execute(array($_REQUEST['id'],$_SESSION['customer']['cust_id']));
    $total = $statement->rowCount();
    
    if($total) {
        $error_message = YOU_ALREADY_HAVE_GIVEN_A_RATING; 
    } else {
        $statement = $pdo->prepare("INSERT INTO tbl_rating (p_id,cust_id,comment,rating) VALUES (?,?,?,?)");
        $statement->execute(array($_REQUEST['id'],$_SESSION['customer']['cust_id'],$_POST['comment'],$_POST['rating']));
        $success_message = RATING_IS_SUBMITTED_SUCCESSFULLY;    
    }
    
}

// Getting the average rating for this product
$t_rating = 0;
$statement = $pdo->prepare("SELECT * FROM tbl_rating WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$tot_rating = $statement->rowCount();
if($tot_rating == 0) {
    $avg_rating = 0;
} else {
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
    foreach ($result as $row) {
        $t_rating = $t_rating + $row['rating'];
    }
    $avg_rating = $t_rating / $tot_rating;
}

if(isset($_POST['form_add_to_cart'])) {
    
    if(!isset($_SESSION['customer'])){
        setcookie("notlogin-alert-danger", "Please login first to use this feature.", time()+ 5,'/');
        header('location: login.php');
        exit();
    }   

	// getting the currect stock of this product
	$statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	
    if(isset($_SESSION['cart_p_id']))
    {
        $arr_cart_p_id = array();
        
        $arr_cart_p_current_price = array();

        $i=0;
        foreach($_SESSION['cart_p_id'] as $key => $value) 
        {
            $i++;
            $arr_cart_p_id[$i] = $value;
        }

        
        for($i=1;$i<=count($arr_cart_p_id);$i++) {
            if( ($arr_cart_p_id[$i]==$_REQUEST['id']) ) {
                $added = 1;
                break;
            }
        }

        if($added == 1) {
           $error_message1 = 'This product is already added to the shopping cart.';
        } else {

            $i=0;
            foreach($_SESSION['cart_p_id'] as $key => $res) 
            {
                $i++;
            }
            $new_key = $i+1;

            
            $_SESSION['cart_p_id'][$new_key] = $_REQUEST['id'];
            $_SESSION['cart_p_current_price'][$new_key] = $_POST['p_current_price'];
            $_SESSION['cart_p_old_price'][$new_key] = $_POST['p_old_price'];
            $_SESSION['cart_p_name'][$new_key] = $_POST['p_name'];
            $_SESSION['cart_p_featured_photo'][$new_key] = $_POST['p_featured_photo'];

            $success_message1 = 'Product is added to the cart successfully!';
        }
        
    }
    else
    {

        $_SESSION['cart_p_id'][1] = $_REQUEST['id'];
        
        $_SESSION['cart_p_current_price'][1] = $_POST['p_current_price'];
        $_SESSION['cart_p_old_price'][1] = $_POST['p_old_price'];
        $_SESSION['cart_p_name'][1] = $_POST['p_name'];
        $_SESSION['cart_p_featured_photo'][1] = $_POST['p_featured_photo'];

        $success_message1 = 'Product is added to the cart successfully!';
    }
	
}
?>

<?php

if(!empty($success_message1)) {
    
    setcookie('cart_success',$success_message1,time()+5);
    header('location: product.php?id='.$_REQUEST['id']);
}

?>


<div class="page">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
                <div class="breadcrumb mb_30">
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>">Home</a></li>
                        <li>></li>
                        <li><a href="<?php echo BASE_URL.'product-category.php?id='.$tcat_id.'&type=top-category' ?>"><?php echo $tcat_name; ?></a></li>
                        <li>></li>
                        <?php if(!empty($mcat_name)): ?>
                        <li><a href="<?php echo BASE_URL.'product-category.php?id='.$mcat_id.'&type=mid-category' ?>"><?php echo $mcat_name; ?></a></li>
                        <li>></li>
                        <?php endif; ?>

                        <li><?php echo $p_name; ?></li>
                    </ul>
                </div>
                <?php
                if(!empty($error_message1)) {
                
                    echo "<div class='alert alert-danger' id='cart-error' style='margin-bottom:20px;'>".$error_message1."</div>";
                ?>
                <script>
                    setTimeout(function() {
                        $("#cart-error").remove();
                    }, 5000);
                </script>
                <?php
                }
                ?>
                <?php
                if(!empty($_COOKIE['cart_success'])) {
                
                    echo "<div class='alert alert-success' id='cart-success' style='margin-bottom:20px;'>".$_COOKIE['cart_success']."</div>";
                ?>
                <script>
                    setTimeout(function() {
                        $("#cart-success").remove();
                    }, 5000);
                </script>
                <?php
                }
                ?>
				<div class="product">
					<div class="row">
						<div class="col-md-5">
							<ul class="prod-slider">
                                
								<li style="background-image: url(public_files/uploads/<?php echo $p_featured_photo; ?>);" >
                                    <a class="popup" href="public_files/uploads/<?php echo $p_featured_photo; ?>" ></a>
								</li>
                                
                                <?php
                                $statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=?");
                                $statement->execute(array($_REQUEST['id']));
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($result as $row) {
                                    ?>
                                    <li style="background-image: url(public_files/uploads/product_photos/<?php echo $row['photo']; ?>);">
                                        <a class="popup" href="public_files/uploads/product_photos/<?php echo $row['photo']; ?>"></a>
                                        
                                    </li>
                                   
                                    <?php
                                    
                                }
                                
                                    $get_customer_name = check_customer_name($pdo,$tbl_user_id);
                                ?>
							</ul>
                            
							<div id="prod-pager" class="product-carousel">
                                <div class="item">
								    <a data-slide-index="0" href=""><div class="prod-pager-thumb" style="background-image: url(public_files/uploads/<?php echo $p_featured_photo; ?>"></div></a>
                                </div>
                                <?php
                                $i=1;
                                $statement = $pdo->prepare("SELECT * FROM tbl_product_photo WHERE p_id=?");
                                $statement->execute(array($_REQUEST['id']));
                                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($result as $row) {
                                    ?>
                                    <div class="item ">
                                    <a data-slide-index="<?php echo $i; ?>" href=""><div class="prod-pager-thumb" style="background-image: url(public_files/uploads/product_photos/<?php echo $row['photo']; ?>"></div></a>
                                    </div>
                                    <?php
                                    $i++;
                                }
                                ?>
                                
							</div>
						</div>
						<div class="col-md-7">
							<div class="p-title"><h2><?php echo $p_name; ?></h2></div>
                            
							<div class="p-review">
								<div class="rating">
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
                                    ?>
                                </div>
							</div>
							<div class="p-short-des">
                                <p>
                                    <label>By - </label>
                                    <span><?php echo (empty($get_customer_name)?'Admin':$get_customer_name['cust_email']); ?></span>
                                </p>
                            
                                <?php if(!empty($p_sku)):?>
                                <p>
                                    <label>SKU:</label>
									<span><?php echo $p_sku; ?></span>
								</p>
                                <?php endif; ?>
								<p>
                                    <label><?php echo TAGS; ?>: </label>
									<span><?php $tags_arr = explode(',',$p_tags);
                                        foreach($tags_arr as $t =>$tag){
                                            echo '<a href ="product-category.php?id='.$tag.'&type=tags" class="badge badge-primary">'.ucfirst($tag).'</a> &nbsp;';
                                        }
                                    ?></span>
								</p>
                                <p>
                                    <label><?php echo LICENSE; ?>:</label>
									<span><?php echo ucfirst(str_replace('_',' ',$p_license))." License"; ?> <sup class="fa fa-question-circle-o" data-toggle="tooltip" title="Refer below to read policy"></sup></span>
								</p>

                                <p>
                                    <label><?php echo FILES_INCLUDE; ?>: </label>
									<span><?php echo $file_extension; ?> </span>
								</p>

							</div>
                            <form action="" method="post">
                            
							<div class="p-price">
                                <?php if($is_free == 0) { ?>
                                    <span style="font-size:14px;"><?php echo PRODUCT_PRICE; ?></span><br>
                                    
                                <?php  } ?>
                                <span>
                                    <?php 
                                    if($is_free == 1) {
                                        echo '<span style="color:red">Free</span>';
                                    }else{
                                        
                                        $discount_applied = 0;
                        
                                        if($user_type == 'Customer'){
                                            $check_discount_table = get_sale_discount_func($pdo,$tbl_user_id);
                                            $discount_applied    = $check_discount_table['discount_applied'];
                                                
                                        }
                                        
                                        if(!empty($sale_date_setting_list)){ 
                                            
                                            if($discount_applied != 0){

                                                $final_sale_check = final_sale_check($sale_date_setting_list,$check_discount_table);

                                                if($final_sale_check['sale_type'] != 'none'){

                                                    $dis_percentage = $final_sale_check['perc'];

                                                    
                                                    if(!empty($p_old_price)){

                                                        $get_discounted_price =  $p_old_price*($dis_percentage/100);
                                                        
                                                        $p_current_price =  $p_old_price-$get_discounted_price;
                                                        
                                                        if(CURRENCY_POSITION == 'Before') {
                                                            echo CURRENCY_SYMBOL.' '.number_format(($p_current_price * CURRENCY_VALUE),2);
                                                            echo ' <del>'.CURRENCY_SYMBOL.' '.number_format(( $p_old_price * CURRENCY_VALUE),2).'</del>';
                                                        } else {
                                                            echo number_format(($p_current_price * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL;
                                                            echo ' <del>'.number_format(( $p_old_price * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL.'</del>';
                                                        }
                                                        
                                                        echo '<p class="color-red fs-14 fw-100">This product is on '.$dis_percentage.'% sale. Grab Fast !!</p>';

                                                    }else{

                                                        $get_discounted_price = $p_current_price*($dis_percentage/100);

                                                        $p_old_price = $p_current_price;
                                                        $p_current_price = $p_current_price-$get_discounted_price;
                                                        
                                                        if(CURRENCY_POSITION == 'Before') {
                                                            echo CURRENCY_SYMBOL.' '.number_format(($p_current_price * CURRENCY_VALUE),2);
                                                            echo ' <del>'.CURRENCY_SYMBOL.' '.number_format(($p_old_price * CURRENCY_VALUE),2).'</del>';
                                                        } else {
                                                            echo number_format(($p_current_price * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL;
                                                            echo ' <del>'.number_format(($p_old_price * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL.'</del>';
                                                        }

                                                        echo '<p class="color-red fs-14 fw-100">This product is on '.$dis_percentage.'% sale. Grab Fast !!</p>';
                                                    }

                                                    

                                                }else{
                                                    echo default_prod_price($product_detail);
                                                }
                                            }else{
                                                echo default_prod_price($product_detail);
                                            }
                                        }else{
                                            echo default_prod_price($product_detail);
                                        }
                                    }
                                    $wishlist_icon = wishlist_function($pdo,$p_id);

                                    if(empty($p_old_price)){
                                        $p_old_price = $p_current_price;
                                    }
                                    ?>
                                </span>
                            </div>
                            <input type="hidden" name="p_current_price" value="<?php echo $p_current_price; ?>">
                            <input type="hidden" name="p_old_price" value="<?php echo $p_old_price; ?>">
                            <input type="hidden" name="p_name" value="<?php echo $p_name; ?>">
                            <input type="hidden" name="p_featured_photo" value="<?php echo $p_featured_photo; ?>">
							
                            <?php if($is_delete ==0){?>
							<div class="btn-cart btn-cart1">
                                <?php if($is_free == 1) {

                                    ?>
                                    <a href="download-product.php?zip_product=<?php echo $row['p_id']; ?>" style="padding: 12px;background:#3aafd7"><i class="fa fa-download"></i> &nbsp;Free Download</a>
                                    
                                <?php 

                                    }else{ ?>

                                    <input type="submit" value="<?php echo ADD_TO_CART; ?>" name="form_add_to_cart">

                                <?php } ?>
                                <a style="cursor: pointer;padding: 12px;background:#3aafd7" class="wishlist_btn" data-id= "<?php echo $p_id; ?>"><i class="<?php echo $wishlist_icon; ?>"></i></a>
							</div>
                            
                            </form>
							<div class="share">
                                <?php echo SHARE_THIS_PRODUCT; ?> <br>
								<div class="sharethis-inline-share-buttons"></div>
							</div>
                            <?php 
                            }else{
                                echo '<p class="color-red">This product not available anymore!!</p>';
                            }   ?>
                            <div class="row mt_10">
                                <?php if(!empty($youtube_prev)){
                                    $you = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtube.com\/watch\?v=([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "<iframe height=\"184px\" src=\"//www.youtube.com/embed/$1\" frameborder=\"0\" allowfullscreen></iframe>", $youtube_prev);
                                    
                                    echo '<div class="col-md-6">'.$you.'</div>';
                                }
                                ?>
                                    
                                
                                <?php if(!empty($vimeo_prev)): 
                                    $vimeo = preg_replace("/\s*[a-zA-Z\/\/:\.]*vimeo.com\/([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "<iframe height=\"184px\" src=\"//player.vimeo.com/video/$1\" frameborder=\"0\" allowfullscreen></iframe>", $vimeo_prev);
                                    echo '<div class="col-md-6">'.$vimeo.'</div>';    
                                ?>
                                    
                                <?php endif; ?>
                            </div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<!-- Nav tabs -->
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active"><a href="#description" aria-controls="description" role="tab" data-toggle="tab"><?php echo PRODUCT_DESCRIPTION; ?></a></li>
								<li role="presentation"><a href="#feature" aria-controls="feature" role="tab" data-toggle="tab"><?php echo FEATURES; ?></a></li>
                                <li role="presentation"><a href="#condition" aria-controls="condition" role="tab" data-toggle="tab"><?php echo LICENSE_POLICY; ?></a></li>
                                <li role="presentation"><a href="#review" aria-controls="review" role="tab" data-toggle="tab"><?php echo REVIEWS; ?></a></li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="description" style="margin-top: -30px;">
									<p>
                                        <?php
                                        if($p_description == '') {
                                            echo NO_DESCRIPTION_FOUND;
                                        } else {
                                            echo $p_description;
                                        }
                                        ?>
									</p>
								</div>
                                <div role="tabpanel" class="tab-pane" id="feature" style="margin-top: -30px;">
                                    <p>
                                        <?php
                                        if($p_feature == '') {
                                            echo NO_FEATURE_FOUND;
                                        } else {
                                            echo $p_feature;
                                        }
                                        ?>
                                    </p>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="condition" style="margin-top: -30px;">
                                    <p>
                                        <?php
                                        if($p_license == 'custom'){
                                            if($p_custom_license == '') {
                                                echo NO_LICENSE_POLICY_FOUND;
                                            } else {
                                                echo '<h4>Custom License</h4><p>'.$p_custom_license.'</p>';
                                            }
                                        }else if($p_license == 'editorial'){
                                            echo '<h4>Editorial License</h4>
                                            <p>Editorial license gives a permission to use the product only in an editorial manner, relating to events that are newsworthy, or of public interest, and may not be used for any commercial, promotional, advertising or merchandising use.</p>

                                            <p>In a few instances, you may otherwise have the rights to IP in content that is under Editorial license. For instance, you may be the advertising agency for a brand/IP owner or you may be the brand/IP owner itself purchasing user generated content. Given you have the rights clearance through other means, you may use the content under Editorial License commercially. Every user failing to comply with Editorial Use restrictions takes the responsibility to prove the ownership of IP rights.</p>
                                            
                                            <p>For users, who are not brand/IP owners or official affiliates, the restrictions of Editorial-licensed content usage include, but are not limited to, the following cases:</p>
                                            <ul>
                                            <li>
                                            Products may not be used on any item/product created for resale such as, commercials, for-profit animations, video games, VR/AR applications, or physical products such as a merchandise or t-shirt.</li>
                                            <li>
                                            Products may not be used as part of own product promotional materials, billboard, trade show or exhibit display.</li>
                                            <li>The product may not be incorporated into a logo, trademark or service mark. As an example, you cannot use Editorial content to create a logo design.</li>
                                            <li>Products may not be used in any insulting, abusive or otherwise unlawful manner.</li>
                                            <li>The product may not be used for any commercial related purpose.</li>
                                            <p>Read more about Editorial License and product usage allowed by different licenses in our legally binding General Terms and Conditions.</p>';
                                        }else{
                                            echo '<h4>Royalty Free License</h4>
                                            <p>Royalty Free License allows you to use the product without the need to pay royalties or other license fees for multiple uses, per volume sold, or some time period of use or sales. Products published with this license may not be sold, given, or assigned to another person or entity in the form it is downloaded from the site, but can be used in your commercial projects multiple times after paying for it just once. </p>

                                            <p>This is, however, a non-exclusive license and the product remains the property of a seller for further distribution. Please, refer to legally binding General Terms and Conditions to learn more about Royalty Free License, other types of licenses and general rules applicable to all products.</p>';
                                        }
                                        ?>
                                    </p>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="return_policy" style="margin-top: -30px;">
                                    <p>
                                        <?php
                                        
                                        ?>
                                    </p>
                                </div>
								<div role="tabpanel" class="tab-pane" id="review" style="margin-top: -30px;">

                                    <div class="review-form">
                                        <?php
                                        $statement = $pdo->prepare("SELECT * 
                                                            FROM tbl_rating t1 
                                                            JOIN tbl_customer t2 
                                                            ON t1.cust_id = t2.cust_id 
                                                            WHERE t1.p_id=?");
                                        $statement->execute(array($_REQUEST['id']));
                                        $total = $statement->rowCount();
                                        ?>
                                        <h2><?php echo REVIEWS; ?> (<?php echo $total; ?>)</h2>
                                        <?php
                                        if($total) {
                                            $j=0;
                                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                                $j++;
                                                ?>
                                                <div class="mb_10"><b><u><?php echo REVIEW; ?> <?php echo $j; ?></u></b></div>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th style="width:170px;"><?php echo CUSTOMER_NAME; ?></th>
                                                        <td><?php echo $row['cust_name']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th><?php echo COMMENT; ?></th>
                                                        <td><?php echo $row['comment']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th><?php echo RATING; ?></th>
                                                        <td>
                                                            <div class="rating">
                                                                <?php
                                                                for($i=1;$i<=5;$i++) {
                                                                    ?>
                                                                    <?php if($i>$row['rating']): ?>
                                                                        <i class="fa fa-star-o"></i>
                                                                    <?php else: ?>
                                                                        <i class="fa fa-star"></i>
                                                                    <?php endif; ?>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <?php
                                            }
                                        } else {
                                            echo NO_REVIEW_FOUND;
                                        }
                                        ?>
                                        
                                        <h2><?php echo GIVE_A_REVIEW; ?></h2>
                                        <?php
                                        if(!empty($error_message)) {
                                        
                                            echo "<div class='alert alert-danger' id='review-error' style='margin-bottom:20px;'>".$error_message."</div>";
                                        ?>
                                        <script>
                                            setTimeout(function() {
                                                $("#review-error").remove();
                                            }, 5000);
                                        </script>
                                        <?php
                                        }
                                        ?>
                                        <?php
                                        if(!empty($success_message)) {
                                        
                                            echo "<div class='alert alert-success' id='review-success' style='margin-bottom:20px;'>".$success_message."</div>";
                                        ?>
                                        <script>
                                            setTimeout(function() {
                                                $("#review-success").remove();
                                            }, 5000);
                                        </script>
                                        <?php
                                        }
                                        ?>
                                        
                                        <?php if(isset($_SESSION['customer'])): ?>
                                            
                                            <?php

                                            $payment_table_sql = $pdo->prepare('SELECT tbl_order.* from tbl_order 
                                                left join tbl_payment on tbl_payment.payment_id = tbl_order.payment_id
                                                where tbl_order.product_id = ? and tbl_payment.customer_id =?');
                                            $payment_table_sql->execute([$_REQUEST['id'],$_SESSION['customer']['cust_id']]);
                                            $payment_table = $payment_table_sql->rowCount();

                                            if($payment_table > 0){

                                            
                                                $statement = $pdo->prepare("SELECT * 
                                                                    FROM tbl_rating
                                                                    WHERE p_id=? AND cust_id=?");
                                                $statement->execute(array($_REQUEST['id'],$_SESSION['customer']['cust_id']));
                                                $total = $statement->rowCount();
                                                ?>
                                                <?php if($total==0): ?>
                                                <form action="" method="post">
                                                <div class="rating-section">
                                                    <input type="radio" name="rating" class="rating" value="1" checked>
                                                    <input type="radio" name="rating" class="rating" value="2" checked>
                                                    <input type="radio" name="rating" class="rating" value="3" checked>
                                                    <input type="radio" name="rating" class="rating" value="4" checked>
                                                    <input type="radio" name="rating" class="rating" value="5" checked>
                                                </div>                                            
                                                <div class="form-group">
                                                    <textarea name="comment" class="form-control" cols="30" rows="10" placeholder="Write your comment (optional)" style="height:100px;"></textarea>
                                                </div>
                                                <input type="submit" class="btn btn-default" name="form_review" value="<?php echo SUBMIT_REVIEW; ?>">
                                                </form>
                                                <?php else: ?>
                                                    <span style="color:red;"><?php echo YOU_ALREADY_HAVE_GIVEN_A_RATING; ?></span>
                                                <?php endif;
                                                
                                            }else{
                                            
                                                echo '<span style="color:red;">'.PURCHASE_PRODUCT_TO_GIVE_REVIEW.'</span>';
                                            }?>


                                        <?php else: ?>
                                            <p class="error">
												<?php echo YOU_MUST_HAVE_TO_LOGIN_TO_GIVE_A_REVIEW; ?> <br>
												<a href="login.php" style="color:red;text-decoration: underline;"><?php echo LOGIN; ?></a>
											</p>
                                        <?php endif; ?>                         
                                    </div>

								</div>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>
</div>

<div class="product bg-gray pt_70 pb_70">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline">
                    <h2><?php echo RELATED_PRODUCTS; ?></h2>
                    <h3><?php echo SEE_ALL_RELATED_PRODUCTS_FROM_BELOW; ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <div class="product-carousel">

                    <?php
                    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE cat_id=? AND p_id!=? and is_delete=0");
                    $statement->execute(array($tcat_id,$_REQUEST['id']));
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($result as $row) {
                        $discount_applied = 0;
                        
                        
                        if($row['user_type'] == 'Customer'){
                            $check_discount_table = get_sale_discount_func($pdo,$row['user_id']);
                            $discount_applied    = $check_discount_table['discount_applied'];
                                
                        }
                        ?>
                        <div class="item">
                            <!-- sale tag -->
                            <?php
                                if($row['is_free'] == 0){
                                    if(!empty($sale_date_setting_list)){ 
                                        if($discount_applied != 0){
                                            $final_sale_check = final_sale_check($sale_date_setting_list,$check_discount_table);
                                            if($final_sale_check['sale_type'] != 'none'){
                                                ?>
                                                <p class="m_0">
                                                    <span class="sale-design-tag">
                                                        <?php if($final_sale_check['sale_type']== 'sale' ){ 
                                                            
                                                            ?>
                                                        <i class="fa fa-bolt "></i>
                                                        <?php } elseif($final_sale_check['sale_type']== 'special_sale'){ 
                                                            
                                                            ?>
                                                            <i class="fa fa-bolt "></i>
                                                            <i class="fa fa-bolt "></i>
                                                        <?php } ?>
                                                    </span>
                                                    
                                                    <i class="sale-percent-tag"> <?php echo $final_sale_check['perc'] ?> %</i>
                                                </p>
                                                <?php 
                                            } 
                                        }
                                    } 
                                } 
                            ?>
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
                                    ?>
                                </div>
                                <?php $wishlist_icon = wishlist_function($pdo,$row['p_id']); ?>
                                <?php if($row['is_free'] == 1): ?>
                                    <p><a style="cursor: pointer;" class="wishlist_btn" data-id="<?php echo $row['p_id']; ?>"><i class="<?php echo $wishlist_icon; ?>"></i></a> &nbsp; &nbsp;
                                    <?php if(file_exists("public_files/uploads/model_product_files/".$row['prod_model_file'])){ ?>
                                    <a href="product.php?id=<?php echo $row['p_id']; ?>"><i class="fa fa-download"></i> Download</a>
                                    <?php } ?></p>   
                                <?php else: ?>
                                    <p><a style="cursor: pointer;" class="wishlist_btn" data-id= "<?php echo $row['p_id']; ?>"><i class="<?php echo $wishlist_icon; ?>"></i></a> &nbsp; &nbsp; <a href="product.php?id=<?php echo $row['p_id']; ?>"><i class="fa fa-cart-arrow-down"></i> Add to Cart</a></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                </div>

            </div>
        </div>
    </div>
</div>


<?php require_once('footer.php'); ?>
