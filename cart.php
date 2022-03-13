<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $banner_cart = $row['banner_cart'];
}
?>



<div class="page-banner" style="background-image: url(public_files/uploads/<?php echo $banner_cart; ?>)">
    <div class="overlay"></div>
    <div class="page-banner-inner">
        <h1><?php echo CART; ?></h1>
    </div>
</div>

<div class="page">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

                <?php 
                
                if(!isset($_SESSION['cart_p_id'])): ?>
                    <?php echo "<div class='alert alert-info' id='cart-error' style='margin-bottom:20px;'>Cart is empty</div>"; ?>
                <?php else: ?>
                <form action="" method="post">
                    <?php $csrf->echoInputField(); ?>
                    <?php
                        
                        $table_total_price = 0;
                        $total_sub_price =0;
                        $i=0;
                        foreach($_SESSION['cart_p_id'] as $key => $value) 
                        {
                            $i++;
                            $statement = $pdo->prepare("SELECT * FROM tbl_product WHERE p_id=?");
                            $statement->execute(array($value));
                            $result = $statement->fetch(PDO::FETCH_ASSOC);

                            $arr_cart_p_id[$i] = $value;
                            $arr_cart_sku[$i]  = $result['p_sku'];
                            
                        }
                        $arr_cart_p_old_price = $_SESSION['cart_p_old_price'];
                        $i=0;
                        foreach($_SESSION['cart_p_current_price'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_current_price[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_name'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_name[$i] = $value;
                        }

                        $i=0;
                        foreach($_SESSION['cart_p_featured_photo'] as $key => $value) 
                        {
                            $i++;
                            $arr_cart_p_featured_photo[$i] = $value;
                        }
                    ?>
                    <div class="cart">
                        <div class="row">
                            <div class="col-md-8 p_0">
                                <a href="index.php" class="btn btn-link " style="font-size: 16px;"><i class="fa fa-arrow-circle-o-left"></i> &nbsp;<?php echo CONTINUE_SHOPPING; ?></a>
                                <?php for($i=1;$i<=count($arr_cart_p_id);$i++): ?>
                                <div class="row m_10 card">
                                    
                                    <div class="col-md-2 text-center">
                                        <img class="cart_uploaded_img" src="public_files/uploads/<?php echo $arr_cart_p_featured_photo[$i]; ?>" alt="">
                                    </div>
                                    <div class="col-md-5 col-xs-5 mt_10">
                                        <a href="product.php?id=<?php echo $arr_cart_p_id[$i]; ?>"><h4><?php echo $arr_cart_p_name[$i]; ?></h4></a>
                                        <?php if(!empty($arr_cart_sku[$i])){ ?>
                                        <p><b>SKU: </b><?php echo $arr_cart_sku[$i]; ?></p>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-3 col-xs-7 text-right">
                                        <h3>
                                        
                                        <?php
                                        if(CURRENCY_POSITION == 'Before') {
                                            echo CURRENCY_SYMBOL;
                                            echo number_format($arr_cart_p_current_price[$i] * CURRENCY_VALUE,2);
                                        } else {
                                            echo number_format($arr_cart_p_current_price[$i] * CURRENCY_VALUE,2);
                                            echo CURRENCY_SYMBOL;
                                        }
                                        ?>
                                        <del style="color:lightgrey">
                                        
                                            <?php
                                            if(!empty($arr_cart_p_old_price[$i]) && $arr_cart_p_current_price[$i] != $arr_cart_p_old_price[$i]){
                                                if(CURRENCY_POSITION == 'Before') {
                                                    echo CURRENCY_SYMBOL;
                                                    echo number_format($arr_cart_p_old_price[$i] * CURRENCY_VALUE,2);
                                                } else {
                                                    echo number_format($arr_cart_p_old_price[$i] * CURRENCY_VALUE,2);
                                                    echo CURRENCY_SYMBOL;
                                                }
                                            }
                                            ?>

                                        </del>
                                        
                                
                                        </h3>
                                        
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <h4>
                                            <?php $wishlist_icon = wishlist_function($pdo,$arr_cart_p_id[$i]); ?>
                                            <a style="cursor: pointer;" class="wishlist_btn" data-id="<?php echo $arr_cart_p_id[$i]; ?>"><i class="<?php echo $wishlist_icon; ?>"></i></a> 
                                            &nbsp; 
                                            <a onclick="return confirmDelete();" href="cart-item-delete.php?id=<?php echo $arr_cart_p_id[$i]; ?>" class="trash"><i class="fa fa-close"></i></a>
                                        </h4>
                                        
                                        
                                    </div>
                                    
                                </div>
                                <?php
                                $row_total_price = $arr_cart_p_current_price[$i];
                                $table_total_price = $table_total_price + $row_total_price;

                                if(!empty($arr_cart_p_old_price[$i])){
                                    $total_sub_price = $total_sub_price + $arr_cart_p_old_price[$i];
                                }else{
                                    $total_sub_price = $total_sub_price + $arr_cart_p_current_price[$i];
                                }
                                ?>
                                <?php endfor; ?>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Order Summary</h4>
                                    </div>
                                    <div class="card-body row">
                                        
                                            <div class="col-md-12 pt_10">
                                                <div class="col-md-4">
                                                    Sub Total :
                                                </div>
                                                <div class="col-md-8 text-right">
                                                <?php
                                                    if(CURRENCY_POSITION == 'Before') {
                                                        echo CURRENCY_SYMBOL;
                                                        echo ($total_sub_price) * CURRENCY_VALUE;
                                                    } else {
                                                        echo ($total_sub_price) * CURRENCY_VALUE;
                                                        echo CURRENCY_SYMBOL;
                                                    }
                                                    ?>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-12 pt_10 ">
                                                <div class="col-md-4">
                                                    Discount :
                                                </div>
                                                <div class="col-md-8 text-right">
                                                    <?php
                                                    if(CURRENCY_POSITION == 'Before') {
                                                        echo '(-) '.CURRENCY_SYMBOL;
                                                        echo ($total_sub_price-$table_total_price) * CURRENCY_VALUE;
                                                    } else {
                                                        echo '(-) '.($total_sub_price-$table_total_price) * CURRENCY_VALUE;
                                                        echo CURRENCY_SYMBOL;
                                                    }
                                                    ?>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <hr>
                                            </div>
                                            <div class="col-md-12  pb_20">
                                                <div class="col-md-4">
                                                    <b>Total :</b>
                                                </div>
                                                <div class="col-md-8 text-right">
                                                    <b><?php
                                                    if(CURRENCY_POSITION == 'Before') {
                                                        echo CURRENCY_SYMBOL;
                                                        echo ($table_total_price) * CURRENCY_VALUE;
                                                    } else {
                                                        echo ($table_total_price) * CURRENCY_VALUE;
                                                        echo CURRENCY_SYMBOL;
                                                    }
                                                    ?>
                                                    </b>
                                                </div>
                                            </div>
                                        
                                    </div>
                                </div>
                                <div class="cart-buttons ">  
                                    <a href="checkout.php" class="btn btn-block btn-primary"><?php echo PROCEED_TO_CHECKOUT; ?></a>
                                    
                                </div>
                            </div>
                        </div>
                        
                    </div>

                </form>
                <?php endif; ?>

                

			</div>
		</div>
	</div>
</div>

<style>
    .cart_uploaded_img{
        
        padding: 10px;
        width: 100%;
    }
    .card-header{
        padding: 5px 20px;
        border-bottom: 1px solid #9b9b9b;
    }
</style>
<?php require_once('footer.php'); ?>