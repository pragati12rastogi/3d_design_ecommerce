<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT * FROM tbl_setting_banner WHERE id=1");
$statement->execute();
$result = $statement->fetchAll();                            
foreach ($result as $row) {
    $banner_wishlist = $row['banner_wishlist'];
}

if(isset($_SESSION['customer'])){
    $wishlist_list = $pdo->prepare('SELECT tbl_wishlist.*,tbl_product.p_name,tbl_product.p_current_price,tbl_product.p_featured_photo FROM tbl_wishlist LEFT JOIN tbl_product on tbl_wishlist.product_id = tbl_product.p_id  WHERE customer_id ='.$_SESSION['customer']['cust_id']);
    $wishlist_list->execute();
    $wishlist_list = $wishlist_list->fetchAll(PDO::FETCH_ASSOC);
    
}else{
    $wishlist_list =[];
}

?>

<div class="page-banner" style="background-image: url(public_files/uploads/<?php echo $banner_wishlist; ?>)">
    <div class="overlay"></div>
    <div class="page-banner-inner">
        <h1>Wishlist</h1>
    </div>
</div>

<div class="page">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

                
				<div class="wishlist">
                    <table class="table table-responsive table-bordered">
                        <tr>
                            <th><?php echo SERIAL; ?></th>
                            <th><?php echo PHOTO; ?></th>
                            <th><?php echo PRODUCT_NAME; ?></th>
                            <th><?php echo PRICE; ?></th>
                            <th class="text-center" style="width: 100px;"><?php echo ACTION; ?></th>
                        </tr>

                        <?php 
                        
                        if(count($wishlist_list)>0){
                            foreach($wishlist_list as $wish_key => $wishlist): ?>
                        <tr>
                            <td width="10%"><?php echo $wish_key+1; ?></td>
                            <td width="10%" class="td_center_align">
                                <?php if(file_exists('public_files/uploads/'.$wishlist['p_featured_photo'])){
                                    echo '<img width="50px" src="public_files/uploads/'.$wishlist["p_featured_photo"].'" alt="product photo">';
                                }?>
                                
                            </td>
                            <td><?php echo $wishlist['p_name']; ?></td>
                            <td><?php

                                if(!empty($get_commission)){
                                    if($get_commission['setting_type'] == 'percent'){
                                        
                                        
        
                                        if(!empty($wishlist['p_current_price'])){
                                            $actual_comm = $wishlist['p_current_price'] * ($get_commission['setting_value']/100);
                                            $wishlist['p_current_price'] = $wishlist['p_current_price'] + $actual_comm;
                                        }
        
                                    }else{
                                        
                                        if(!empty($wishlist['p_current_price'])){
                                            
                                            $wishlist['p_current_price'] = $wishlist['p_current_price'] + $get_commission['setting_value'];
                                        }
                                        
                                    }
                                    
                                }

                                if(CURRENCY_POSITION == 'Before') {
                                    echo CURRENCY_SYMBOL;
                                    echo $wishlist['p_current_price'] * CURRENCY_VALUE;
                                } else {
                                    echo $wishlist['p_current_price'] * CURRENCY_VALUE;
                                    echo CURRENCY_SYMBOL;
                                }
                                ?>
                            </td>
                            
                            <td width="30%" class="td_center_align">
                            <a href="product.php?id=<?php echo $wishlist['product_id']; ?>" class="btn btn-sm btn-warning">Add to Cart</a>
                            <a class="wishlist_btn btn btn-sm btn-danger" href="wishlist.php" data-id="<?php echo $wishlist['product_id']; ?>" >Remove</a>
                            </td>
                        </tr>
                        <?php endforeach;
                        }else{
                            echo '<tr><td colspan="5" class="text-center">Nothing is in Wishlist!!</td></tr>';
                        } ?>
                        
                    </table> 
                </div>

			</div>
		</div>
	</div>
</div>

<?php require_once('footer.php'); ?>