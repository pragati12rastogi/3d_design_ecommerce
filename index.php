<?php require_once('header.php'); ?>

<?php

$statement = $pdo->prepare("SELECT * FROM tbl_setting_home WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
    $cta_title                    = $row['cta_title'];
    $cta_content                  = $row['cta_content'];
    $cta_read_more_text           = $row['cta_read_more_text'];
    $cta_read_more_url            = $row['cta_read_more_url'];
    $cta_photo                    = $row['cta_photo'];
    $featured_product_title       = $row['featured_product_title'];
    $featured_product_subtitle    = $row['featured_product_subtitle'];
    $latest_product_title         = $row['latest_product_title'];
    $latest_product_subtitle      = $row['latest_product_subtitle'];
    $popular_product_title        = $row['popular_product_title'];
    $popular_product_subtitle     = $row['popular_product_subtitle'];
    $testimonial_title            = $row['testimonial_title'];
    $testimonial_subtitle         = $row['testimonial_subtitle'];
    $testimonial_photo            = $row['testimonial_photo'];
    $blog_title                   = $row['blog_title'];
    $blog_subtitle                = $row['blog_subtitle'];
    $home_service_on_off          = $row['home_service_on_off'];
    $home_welcome_on_off          = $row['home_welcome_on_off'];
    $home_featured_product_on_off = $row['home_featured_product_on_off'];
    $home_latest_product_on_off   = $row['home_latest_product_on_off'];
    $home_popular_product_on_off  = $row['home_popular_product_on_off'];
    $home_testimonial_on_off      = $row['home_testimonial_on_off'];
    $home_blog_on_off             = $row['home_blog_on_off'];
}


$statement = $pdo->prepare("SELECT * FROM tbl_setting_advertisement WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
    
    $ads_above_featured_product_on_off = $row['ads_above_featured_product_on_off'];
    $ads_above_latest_product_on_off   = $row['ads_above_latest_product_on_off'];
    $ads_above_popular_product_on_off  = $row['ads_above_popular_product_on_off'];
    $ads_above_testimonial_on_off      = $row['ads_above_testimonial_on_off'];
    $ads_category_sidebar_on_off       = $row['ads_category_sidebar_on_off'];
}


      
$statement = $pdo->prepare("SELECT * FROM tbl_setting_post WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();
foreach ($result as $row) {
    $total_featured_product_home = $row['total_featured_product_home'];
    $total_latest_product_home = $row['total_latest_product_home'];
    $total_popular_product_home = $row['total_popular_product_home'];
}


$statement = $pdo->prepare("SELECT * FROM tbl_advertisement");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $adv_type[] = $row['adv_type'];
    $adv_photo[] = $row['adv_photo'];
    $adv_photo2[] = $row['adv_photo2'];
    $adv_photo3[] = $row['adv_photo3'];
    $adv_url[] = $row['adv_url'];
    $adv_adsense_code[] = $row['adv_adsense_code'];
}
?>
<script>
    
    $(function(){
        $(window).scroll(function () {
            var scrollTop = $(window).scrollTop();
            
            if (scrollTop < 3500) {
                maxHeight = 320;
            } else if (scrollTop > 4000) {
                maxHeight =  1305;
            } else {
                maxHeight = 1305 - 320 * ((scrollTop - 3500)) / 3500;
            }
            $('#how_muoro_works').css({
                'height': maxHeight + "px"
            });
        })
        ;(function($, win) {
        $.fn.inViewport = function(cb) {
            return this.each(function(i,el){
            function visPx(){
                var H = $(this).height(),
                    r = el.getBoundingClientRect(), t=r.top, b=r.bottom;
                return cb.call(el, Math.max(0, t>0? H-t : (b<H?b:H)));  
            } visPx();
            $(win).on("resize scroll", visPx);
            });
        };
        }(jQuery, window));

       $(".spl_btn1").inViewport(function(px){
            if(px){ $(this).addClass("fadeInLeftBig animated wow"); }
            else{
                $(this).removeClass("fadeInLeftBig animated wow");
            }
        });
        $(".spl_btn3").inViewport(function(px){
            if(px){ $(this).addClass("fadeInRightBig animated wow") ;}
            else{
                $(this).removeClass("fadeInRightBig animated wow");
            }
        }); 
    })
    
</script>
<div id="bootstrap-touch-slider" class="carousel bs-slider fade control-round indicators-line" data-ride="carousel" data-pause="hover" data-interval="false" >

    <ol class="carousel-indicators">
        <?php
        $i=0;
        $statement = $pdo->prepare("SELECT * FROM tbl_slider");
        $statement->execute();
        $result = $statement->fetchAll();                            
        foreach ($result as $row) {            
            ?>
            <li data-target="#bootstrap-touch-slider" data-slide-to="<?php echo $i; ?>" <?php if($i==0) {echo 'class="active"';} ?>></li>
            <?php
            $i++;
        }
        ?>
    </ol>

    <!-- Wrapper For Slides -->
    <div class="carousel-inner" role="listbox">

        <?php
        $i=0;
        $statement = $pdo->prepare("SELECT * FROM tbl_slider");
        $statement->execute();
        $result = $statement->fetchAll();                            
        foreach ($result as $row) {            
            ?>
            <div class="item <?php if($i==0) {echo 'active';} ?>" style="background-image:url(public_files/uploads/<?php echo $row['photo']; ?>);">
                <div class="bs-slider-overlay"></div>
                <div class="container">
                    <div class="row">
                        <div class="slide-text <?php if($row['position'] == 'Left') {echo 'slide_style_left';} elseif($row['position'] == 'Center') {echo 'slide_style_center';} elseif($row['position'] == 'Right') {echo 'slide_style_right';} ?>">
                            <h1 data-animation="animated <?php if($row['position'] == 'Left') {echo 'zoomInLeft';} elseif($row['position'] == 'Center') {echo 'flipInX';} elseif($row['position'] == 'Right') {echo 'zoomInRight';} ?>"><?php echo $row['heading']; ?></h1>
                            <p data-animation="animated <?php if($row['position'] == 'Left') {echo 'fadeInLeft';} elseif($row['position'] == 'Center') {echo 'fadeInDown';} elseif($row['position'] == 'Right') {echo 'fadeInRight';} ?>"><?php echo nl2br($row['content']); ?></p>
                            <a href="<?php echo $row['button_url']; ?>" target="_blank"  class="btn btn-primary" data-animation="animated <?php if($row['position'] == 'Left') {echo 'fadeInLeft';} elseif($row['position'] == 'Center') {echo 'fadeInDown';} elseif($row['position'] == 'Right') {echo 'fadeInRight';} ?>"><?php echo $row['button_text']; ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $i++;
        }
        ?>
    </div>

    <!-- Left Control -->
    <a class="left carousel-control" href="#bootstrap-touch-slider" role="button" data-slide="prev">
        <span class="fa fa-angle-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>

    <!-- Right Control -->
    <a class="right carousel-control" href="#bootstrap-touch-slider" role="button" data-slide="next">
        <span class="fa fa-angle-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>

</div>

<div class="three_btn_div ">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 text-center col-md-6 col-lg-6 col-sm-6 ">
                <a>
                    <button class="spl_navbutton  spl_btn1" data-wow-delay="1000ms" data-target="#special_req_model"  data-toggle="modal">Special Requirement</button>
                </a>
            </div>
            <div class=" col-xs-12 text-center col-md-6 col-lg-6 col-sm-6 ">
                <a href="free-design.php">
                    <button class="spl_navbutton spl_btn3">Free Design</button>
                </a>
            </div>
            <!-- <div class="col-xs-12 text-center col-md-4 col-lg-4 col-sm-4 ">
                <a>
                    <button class="spl_navbutton  spl_btn3" data-wow-delay="1000ms">Our Package</button>
                </a>
            </div> -->
        </div>
    </div>
</div>

<div id="special_req_model" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="font-weight: bold;">SEND YOUR SPECIAL REQUIRNMENTS</h4>
            </div>
            <div class="modal-body" style="font-size: 14px">
                <?php 

                    if(!empty($_COOKIE['special_requirement_error'])){
                        echo "<div class='alert alert-danger' id='spl-error' style='margin-bottom:20px;'>".$_COOKIE['special_requirement_error']."</div>";
                    }else if(!empty($_COOKIE['special_requirement_success'])){
                        echo "<div class='alert alert-success' id='spl-error' style='margin-bottom:20px;'>".$_COOKIE['special_requirement_success']."</div>";
                    }

                ?>
                <form action="special-requirement-form.php" method="post" enctype = "multipart/form-data">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="spl_req_username" placeholder="Enter name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" name="spl_req_email" placeholder="Enter email address" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" class="form-control" name="spl_req_phone" placeholder="Enter phone number" required>
                    </div>
                    <div class="form-group">
                        <label for="requirement">Your Requirement</label>
                        <textarea  class="form-control" name="spl_req_requirements" placeholder="Enter Your Requirements" row="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="req_file">Chooose File</label>
                        <input type="file" name="spl_req_file" >
                        <span class="text-danger">All Type File Allow, Maximum 4MB File Allow</span>
                    </div>
                    <div class="form-group">
                        
                        <input type="submit" name="spl_req_submit" value="Submit" class="btn btn-success" >
                        
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php 
    if(!empty($_COOKIE['special_requirement_error']) || !empty($_COOKIE['special_requirement_success'])){
?>
    <script>
        
        $("#special_req_model").modal('show');
        
        setTimeout(function() {
            $("#spl-error").remove();
        }, 10000);
    
    </script>
<?php       
    }
?>
<?php if($ads_above_featured_product_on_off == 1): ?>
<div class="ad-section pt_20 pb_20">
    <div class="container">
        <div class="row">
            
                <?php 
                    if($adv_type[1] == 'Adsense Code') {
                        echo '<div class="col-md-12">'.$adv_adsense_code[1].'</div>';
                    } else {
                        
                        if(!empty($adv_photo[1]) && empty($adv_photo2[1]) && empty($adv_photo3[1])){
                            if($adv_url[1]=='') {
                            echo '<div class="col-md-12"><img src="public_files/uploads/'.$adv_photo[1].'" alt="Advertisement"></div>';
                            } else {
                                echo '<div class="col-md-12"><a href="'.$adv_url[1].'"><img src="public_files/uploads/'.$adv_photo[1].'" alt="Advertisement"></a></div>';
                            } 
                        }elseif(!empty($adv_photo[1]) && !empty($adv_photo2[1]) && empty($adv_photo3[1])){
                            $explode_advurl = explode(',',$adv_url[1]);
                            if(isset($explode_advurl[0])){
                                echo '<div class="col-md-6"><a href="'.$explode_advurl[0].'"><img src="public_files/uploads/'.$adv_photo[1].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo[1].'" alt="Advertisement"></div>';
                            }
                            if(isset($explode_advurl[1])){
                                echo '<div class="col-md-6"><a href="'.$explode_advurl[1].'"><img src="public_files/uploads/'.$adv_photo2[1].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo2[1].'" alt="Advertisement"></div>';
                            }
                        }elseif(!empty($adv_photo[1]) && empty($adv_photo2[1]) && !empty($adv_photo3[1])){
                            $explode_advurl = explode(',',$adv_url[1]);
                            if(isset($explode_advurl[0])){
                                echo '<div class="col-md-6"><a href="'.$explode_advurl[0].'"><img src="public_files/uploads/'.$adv_photo[1].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo[1].'" alt="Advertisement"></div>';
                            }
                            if(isset($explode_advurl[1])){
                                echo '<div class="col-md-6"><a href="'.$explode_advurl[1].'"><img src="public_files/uploads/'.$adv_photo3[1].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo3[1].'" alt="Advertisement"></div>';
                            }
                        }
                        elseif(!empty($adv_photo[1]) && !empty($adv_photo2[1]) && !empty($adv_photo3[1])){
                            $explode_advurl = explode(',',$adv_url[1]);
                            if(isset($explode_advurl[0])){
                                echo '<div class="col-md-4"><a href="'.$explode_advurl[0].'"><img src="public_files/uploads/'.$adv_photo[1].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-4"><img src="public_files/uploads/'.$adv_photo[1].'" alt="Advertisement"></div>';
                            }
                            if(isset($explode_advurl[1])){
                                echo '<div class="col-md-4"><a href="'.$explode_advurl[1].'"><img src="public_files/uploads/'.$adv_photo2[1].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-4"><img src="public_files/uploads/'.$adv_photo2[1].'" alt="Advertisement"></div>';
                            }
                            if(isset($explode_advurl[2])){
                                echo '<div class="col-md-4"><a href="'.$explode_advurl[2].'"><img src="public_files/uploads/'.$adv_photo3[1].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-4"><img src="public_files/uploads/'.$adv_photo3[1].'" alt="Advertisement"></div>';
                            }
                        }
                                                       
                    }
                ?>
            
        </div>
    </div>
</div>
<?php endif; ?>

<?php if($home_featured_product_on_off == 1): ?>
<div class="product pt_70 pb_70">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline">
                    <h2><?php echo $featured_product_title; ?></h2>
                    <h3><?php echo $featured_product_subtitle; ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <div class="product-carousel">
                    
                    <?php
                    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_is_featured=? AND p_is_active=? and is_delete = 0 LIMIT ".$total_featured_product_home);
                    $statement->execute(array(1,1));
                    $result = $statement->fetchAll();                            
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
                                    <div class="photo"  style="background-image:url(public_files/uploads/<?php echo $row['p_featured_photo']; ?>);">
                                    
                                    </div>
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
                                        $result1 = $statement1->fetchAll();
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
<?php endif; ?>



<?php if($ads_above_latest_product_on_off == 1): ?>
<div class="ad-section pb_20">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
            <?php 
                    if($adv_type[2] == 'Adsense Code') {
                        echo '<div class="col-md-12">'.$adv_adsense_code[2].'</div>';
                    } else {
                        
                        if(!empty($adv_photo[2]) && empty($adv_photo2[2]) && empty($adv_photo3[2])){
                            if($adv_url[2]=='') {
                            echo '<div class="col-md-12"><img src="public_files/uploads/'.$adv_photo[2].'" alt="Advertisement"></div>';
                            } else {
                                echo '<div class="col-md-12"><a href="'.$adv_url[2].'"><img src="public_files/uploads/'.$adv_photo[2].'" alt="Advertisement"></a></div>';
                            } 
                        }elseif(!empty($adv_photo[2]) && !empty($adv_photo2[2]) && empty($adv_photo3[2])){
                            $explode_advurl = explode(',',$adv_url[2]);
                            if(isset($explode_advurl[0])){
                                echo '<div class="col-md-6"><a href="'.$explode_advurl[0].'"><img src="public_files/uploads/'.$adv_photo[2].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo[2].'" alt="Advertisement"></div>';
                            }
                            if(isset($explode_advurl[1])){
                                echo '<div class="col-md-6"><a href="'.$explode_advurl[1].'"><img src="public_files/uploads/'.$adv_photo2[2].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo2[2].'" alt="Advertisement"></div>';
                            }
                        }elseif(!empty($adv_photo[2]) && empty($adv_photo2[2]) && !empty($adv_photo3[2])){
                            $explode_advurl = explode(',',$adv_url[2]);
                            if(isset($explode_advurl[0])){
                                echo '<div class="col-md-6"><a href="'.$explode_advurl[0].'"><img src="public_files/uploads/'.$adv_photo[2].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo[2].'" alt="Advertisement"></div>';
                            }
                            if(isset($explode_advurl[1])){
                                echo '<div class="col-md-6"><a href="'.$explode_advurl[1].'"><img src="public_files/uploads/'.$adv_photo3[2].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo3[2].'" alt="Advertisement"></div>';
                            }
                        }
                        elseif(!empty($adv_photo[2]) && !empty($adv_photo2[2]) && !empty($adv_photo3[2])){
                            $explode_advurl = explode(',',$adv_url[2]);
                            if(isset($explode_advurl[0])){
                                echo '<div class="col-md-4"><a href="'.$explode_advurl[0].'"><img src="public_files/uploads/'.$adv_photo[2].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-4"><img src="public_files/uploads/'.$adv_photo[2].'" alt="Advertisement"></div>';
                            }
                            if(isset($explode_advurl[1])){
                                echo '<div class="col-md-4"><a href="'.$explode_advurl[1].'"><img src="public_files/uploads/'.$adv_photo2[2].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-4"><img src="public_files/uploads/'.$adv_photo2[2].'" alt="Advertisement"></div>';
                            }
                            if(isset($explode_advurl[2])){
                                echo '<div class="col-md-4"><a href="'.$explode_advurl[2].'"><img src="public_files/uploads/'.$adv_photo3[2].'" alt="Advertisement"></a></div>';
                            }else{
                                echo '<div class="col-md-4"><img src="public_files/uploads/'.$adv_photo3[2].'" alt="Advertisement"></div>';
                            }
                        }
                                                       
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<?php if($home_latest_product_on_off == 1): ?>
<div class="product bg-gray pt_70 pb_30">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline">
                    <h2><?php echo $latest_product_title; ?></h2>
                    <h3><?php echo $latest_product_subtitle; ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <div class="product-carousel">

                    <?php


                    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_is_active=? and is_delete = 0 ORDER BY p_id DESC LIMIT ".$total_latest_product_home);
                    $statement->execute(array(1));
                    $result = $statement->fetchAll();                            
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
                                    <div class="photo"  style="background-image:url(public_files/uploads/<?php echo $row['p_featured_photo']; ?>);">
                                    
                                    </div>
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
                                        $result1 = $statement1->fetchAll();
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
<?php endif; ?>

<?php if($home_welcome_on_off == 1): ?>
<div class="welcome" style="background-image: url('public_files/uploads/<?php echo $cta_photo; ?>');">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?php echo $cta_title; ?></h2>
                <p>
                    <?php echo nl2br($cta_content); ?>
                </p>
                <p class="button"><a href="<?php echo $cta_read_more_url; ?>"><?php echo $cta_read_more_text; ?></a></p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>



<?php if($ads_above_popular_product_on_off == 1): ?>
<div class="ad-section pt_20 bg-gray pb_20">
    <div class="container">
        <div class="row">
            
            <?php 
                if($adv_type[3] == 'Adsense Code') {
                    echo '<div class="col-md-12">'.$adv_adsense_code[3].'</div>';
                } else {
                    
                    if(!empty($adv_photo[3]) && empty($adv_photo2[3]) && empty($adv_photo3[3])){
                        if($adv_url[3]=='') {
                        echo '<div class="col-md-12"><img src="public_files/uploads/'.$adv_photo[3].'" alt="Advertisement"></div>';
                        } else {
                            echo '<div class="col-md-12"><a href="'.$adv_url[3].'"><img src="public_files/uploads/'.$adv_photo[3].'" alt="Advertisement"></a></div>';
                        } 
                    }elseif(!empty($adv_photo[3]) && !empty($adv_photo2[3]) && empty($adv_photo3[3])){
                        $explode_advurl = explode(',',$adv_url[3]);
                        if(isset($explode_advurl[0])){
                            echo '<div class="col-md-6"><a href="'.$explode_advurl[0].'"><img src="public_files/uploads/'.$adv_photo[3].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo[3].'" alt="Advertisement"></div>';
                        }
                        if(isset($explode_advurl[3])){
                            echo '<div class="col-md-6"><a href="'.$explode_advurl[1].'"><img src="public_files/uploads/'.$adv_photo2[3].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo2[3].'" alt="Advertisement"></div>';
                        }
                    }elseif(!empty($adv_photo[3]) && empty($adv_photo2[3]) && !empty($adv_photo3[3])){
                        $explode_advurl = explode(',',$adv_url[3]);
                        if(isset($explode_advurl[0])){
                            echo '<div class="col-md-6"><a href="'.$explode_advurl[0].'"><img src="public_files/uploads/'.$adv_photo[3].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo[3].'" alt="Advertisement"></div>';
                        }
                        if(isset($explode_advurl[1])){
                            echo '<div class="col-md-6"><a href="'.$explode_advurl[1].'"><img src="public_files/uploads/'.$adv_photo3[3].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo3[3].'" alt="Advertisement"></div>';
                        }
                    }
                    elseif(!empty($adv_photo[3]) && !empty($adv_photo2[3]) && !empty($adv_photo3[3])){
                        $explode_advurl = explode(',',$adv_url[3]);
                        if(isset($explode_advurl[0])){
                            echo '<div class="col-md-4"><a href="'.$explode_advurl[0].'"><img src="public_files/uploads/'.$adv_photo[3].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-4"><img src="public_files/uploads/'.$adv_photo[3].'" alt="Advertisement"></div>';
                        }
                        if(isset($explode_advurl[1])){
                            echo '<div class="col-md-4"><a href="'.$explode_advurl[1].'"><img src="public_files/uploads/'.$adv_photo2[3].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-4"><img src="public_files/uploads/'.$adv_photo2[3].'" alt="Advertisement"></div>';
                        }
                        if(isset($explode_advurl[2])){
                            echo '<div class="col-md-4"><a href="'.$explode_advurl[2].'"><img src="public_files/uploads/'.$adv_photo3[3].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-4"><img src="public_files/uploads/'.$adv_photo3[3].'" alt="Advertisement"></div>';
                        }
                    }
                                                    
                }
            ?>
            
        </div>
    </div>
</div>
<?php endif; ?>



<?php if($home_popular_product_on_off == 1): ?>
<div class="product pt_70 pb_70">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline">
                    <h2><?php echo $popular_product_title; ?></h2>
                    <h3><?php echo $popular_product_subtitle; ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <div class="product-carousel">

                    <?php
                    $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_is_active=? and is_delete = 0 ORDER BY p_total_view DESC LIMIT ".$total_popular_product_home);
                    $statement->execute(array(1));
                    $result = $statement->fetchAll();                            
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
                                    <div class="photo"  style="background-image:url(public_files/uploads/<?php echo $row['p_featured_photo']; ?>);">
                                    
                                    </div>
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
                                        $result1 = $statement1->fetchAll();
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
<?php endif; ?>


<?php if($ads_above_testimonial_on_off == 1): ?>
<div class="ad-section pb_20">
    <div class="container">
        <div class="row">
            
            <?php 
                if($adv_type[4] == 'Adsense Code') {
                    echo '<div class="col-md-12">'.$adv_adsense_code[4].'</div>';
                } else {
                    
                    if(!empty($adv_photo[4]) && empty($adv_photo2[4]) && empty($adv_photo3[4])){
                        if($adv_url[4]=='') {
                        echo '<div class="col-md-12"><img src="public_files/uploads/'.$adv_photo[4].'" alt="Advertisement"></div>';
                        } else {
                            echo '<div class="col-md-12"><a href="'.$adv_url[4].'"><img src="public_files/uploads/'.$adv_photo[4].'" alt="Advertisement"></a></div>';
                        } 
                    }elseif(!empty($adv_photo[4]) && !empty($adv_photo2[4]) && empty($adv_photo3[4])){
                        $explode_advurl = explode(',',$adv_url[4]);
                        if(isset($explode_advurl[0])){
                            echo '<div class="col-md-6"><a href="'.$explode_advurl[0].'"><img src="public_files/uploads/'.$adv_photo[4].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo[4].'" alt="Advertisement"></div>';
                        }
                        if(isset($explode_advurl[4])){
                            echo '<div class="col-md-6"><a href="'.$explode_advurl[1].'"><img src="public_files/uploads/'.$adv_photo2[4].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo2[4].'" alt="Advertisement"></div>';
                        }
                    }elseif(!empty($adv_photo[4]) && empty($adv_photo2[4]) && !empty($adv_photo3[4])){
                        $explode_advurl = explode(',',$adv_url[4]);
                        if(isset($explode_advurl[0])){
                            echo '<div class="col-md-6"><a href="'.$explode_advurl[0].'"><img src="public_files/uploads/'.$adv_photo[4].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo[4].'" alt="Advertisement"></div>';
                        }
                        if(isset($explode_advurl[1])){
                            echo '<div class="col-md-6"><a href="'.$explode_advurl[1].'"><img src="public_files/uploads/'.$adv_photo3[4].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-6"><img src="public_files/uploads/'.$adv_photo3[4].'" alt="Advertisement"></div>';
                        }
                    }
                    elseif(!empty($adv_photo[4]) && !empty($adv_photo2[4]) && !empty($adv_photo3[4])){
                        $explode_advurl = explode(',',$adv_url[4]);
                        if(isset($explode_advurl[0])){
                            echo '<div class="col-md-4"><a href="'.$explode_advurl[0].'"><img src="public_files/uploads/'.$adv_photo[4].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-4"><img src="public_files/uploads/'.$adv_photo[4].'" alt="Advertisement"></div>';
                        }
                        if(isset($explode_advurl[1])){
                            echo '<div class="col-md-4"><a href="'.$explode_advurl[1].'"><img src="public_files/uploads/'.$adv_photo2[4].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-4"><img src="public_files/uploads/'.$adv_photo2[4].'" alt="Advertisement"></div>';
                        }
                        if(isset($explode_advurl[2])){
                            echo '<div class="col-md-4"><a href="'.$explode_advurl[2].'"><img src="public_files/uploads/'.$adv_photo3[4].'" alt="Advertisement"></a></div>';
                        }else{
                            echo '<div class="col-md-4"><img src="public_files/uploads/'.$adv_photo3[4].'" alt="Advertisement"></div>';
                        }
                    }
                                                    
                }
            ?>
            
        </div>
    </div>
</div>
<?php endif; ?>



<?php if($home_testimonial_on_off == 1): ?>
<section class="testimonial-v1" style="background-image: url(public_files/uploads/<?php echo $testimonial_photo; ?>);">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline headline-white">
                    <h2><?php echo $testimonial_title; ?></h2>
                    <h3><?php echo $testimonial_subtitle; ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">

                <!-- Testimonial Carousel Start -->
                <div class="testimonial-carousel">
                    <?php
                    $statement = $pdo->prepare("SELECT * FROM tbl_testimonial");
                    $statement->execute();
                    $result = $statement->fetchAll();                            
                    foreach ($result as $row) {
                        ?>
                        <div class="item">
                            <div class="testimonial-wrapper">
                                <div class="content">
                                    <div class="comment">
                                        <p>
                                            <?php echo $row['comment']; ?>
                                        </p>
                                        <div class="icon"></div>
                                    </div>
                                    <div class="author">
                                        <div class="photo">
                                            <img src="public_files/uploads/<?php echo $row['photo']; ?>" alt="">
                                        </div>
                                        <div class="text">
                                            <h3><?php echo $row['name']; ?> </h3>
                                            <h4><?php echo $row['designation']; ?> <span>(<?php echo $row['company']; ?>)</span></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <!-- Testimonial Carousel End -->

            </div>
        </div>
    </div>
</section>
<?php endif; ?>



<?php if($home_blog_on_off == 1): ?>
<div class="home-blog bg-gray">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="headline">
                    <h2><?php echo $blog_title; ?></h2>
                    <h3><?php echo $blog_subtitle; ?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <?php
            $i=0;
            $statement = $pdo->prepare("SELECT * FROM tbl_post ORDER BY post_id DESC");
            $statement->execute();
            $result = $statement->fetchAll();                            
            foreach ($result as $row) {
                $i++;
                if($i>3) {
                    break;
                }
                ?>
                <div class="col-md-4">
                    <div class="item">
                        <div class="photo" style="background-image:url(public_files/uploads/<?php echo $row['photo']; ?>);"></div>
                        <div class="text bg-white">
                            <h3><?php echo $row['post_title']; ?></h3>
                            <p>
                                <?php echo substr($row['post_content'],0,200).' ...'; ?>
                            </p>
                            <p class="button">
                                <a href="blog-single.php?slug=<?php echo $row['post_slug']; ?>">Read More</a>
                            </p>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if($home_service_on_off == 1): ?>
<div class="service bg-gray">
    <div class="container">
        <div class="row">
            <?php
                $statement = $pdo->prepare("SELECT * FROM tbl_service");
                $statement->execute();
                $result = $statement->fetchAll();
                $div_class = '';   
                if(count($result)==1){
                    $div_class = 'col-md-12';
                }elseif(count($result)==2){
                    $div_class = 'col-md-6';
                }elseif(count($result)==3){
                    $div_class = 'col-md-4';
                }elseif(count($result)==4){
                    $div_class = 'col-md-3';
                }elseif(count($result)==4){
                    $div_class = 'col-md-2';
                }else{
                    $div_class = 'col-md-4';
                }                  
                foreach ($result as $row) {
                    ?>
                    <div class="<?php echo $div_class ?> mb_10">
                        <div class="item">
                            <div class="photo"><img src="public_files/uploads/<?php echo $row['photo']; ?>" alt="<?php echo $row['title']; ?>"></div>
                            <h5><?php echo $row['title']; ?></h5>
                            <p>
                                <?php echo nl2br($row['content']); ?>
                            </p>
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>
            </div>
            </div>


<?php endif; ?>

<?php require_once('footer.php'); ?>