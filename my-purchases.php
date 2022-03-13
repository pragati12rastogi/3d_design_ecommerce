<?php require_once('header.php'); ?>

<?php
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    header('location: '.BASE_URL.'logout.php');
    exit;
} else {
    // If customer is logged in, but admin make him inactive, then force logout this user.
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_id=? AND cust_status=?");
    $statement->execute(array($_SESSION['customer']['cust_id'],0));
    $total = $statement->rowCount();
    if($total) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }
}
?>

<div class="page">
    <div class="container">
        <div class="row">            
            <div class="col-md-12"> 
                <?php require_once('customer-sidebar.php'); ?>
            </div>
            <div class="col-md-12">
                <div class="user-content">
                    <table id="sumup_purchase_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Purchase history</th>
                                <th>Original price</th>
                                <th>Money saved</th>
                                <th>Discount</th>
                                <th>Price paid</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $total_purchases = $pdo->prepare('SELECT Sum(tbl_order.actual_price)as original_price, Sum(tbl_order.unit_price)as paid_price  from tbl_payment
                                left join tbl_order on tbl_order.payment_id = tbl_payment.payment_id
                                where tbl_payment.customer_id =? and tbl_payment.payment_status = "Completed"');
                                $total_purchases->execute([$_SESSION['customer']['cust_id']]);
                                $total_purcase_ar = $total_purchases->fetchAll(PDO::FETCH_ASSOC);

                                if(count($total_purcase_ar)>0){
                                foreach($total_purcase_ar as $tp){
                            ?>
                                <tr>
                                    <td><b>TOTAL</b></td>
                                    <td><?php 
                                        if(CURRENCY_POSITION == 'Before') {
                                            echo  CURRENCY_SYMBOL.' '.number_format(($tp['original_price'] * CURRENCY_VALUE),2) ;
                                            
                                        } else {
                                            echo  number_format(($tp['original_price'] * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL ;
                                        
                                        }
                                    ?></td>
                                    <td><?php 
                                        if(CURRENCY_POSITION == 'Before') {
                                            echo  CURRENCY_SYMBOL.' '.number_format((($tp['original_price']-$tp['paid_price']) * CURRENCY_VALUE),2) ;
                                            
                                        } else {
                                            echo  number_format((($tp['original_price']-$tp['paid_price']) * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL ;
                                        
                                        } ?></td>
                                    <td><?php if($tp['original_price']>0 && ((($tp['original_price']-$tp['paid_price'])* 100)/$tp['original_price'])>0){
                                            echo number_format((($tp['original_price']-$tp['paid_price'])* 100)/$tp['original_price'],2).'%' ;
                                        }else{
                                            echo '0'.'%';    
                                        } ?></td>
                                    <td><?php 
                                        if(CURRENCY_POSITION == 'Before') {
                                            echo  CURRENCY_SYMBOL.' '.number_format(($tp['paid_price'] * CURRENCY_VALUE),2) ;
                                            
                                        } else {
                                            echo  number_format(($tp['paid_price'] * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL ;
                                        
                                        }
                                    ?></td>
                                </tr>
                            <?php
                                }
                            }else{
                                echo '<tr><td colspan="5" class="text-center"> No data Available </td></tr>';
                            } ?>
                        </tbody>
                    </table>
                    <br>
                    <br>
                    <table id="purchase_table" class="table table-bordered table-striped ">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Rate</th>
                                <th>Discount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            
                            $purchases_sql = $pdo->prepare('SELECT tbl_order.*,tbl_product.prod_model_file,tbl_product.p_featured_photo,
                                tbl_product.user_type,tbl_product.user_id,tbl_payment.payment_date,tbl_rating.rating  from tbl_order
                                left join tbl_product on tbl_product.p_id = tbl_order.product_id
                                left join tbl_payment on tbl_payment.payment_id = tbl_order.payment_id
                                left join tbl_rating  on  tbl_rating.p_id = tbl_order.product_id and tbl_rating.cust_id = tbl_payment.customer_id
                                where tbl_payment.customer_id =? and tbl_payment.payment_status = "Completed"');
                                $purchases_sql->execute([$_SESSION['customer']['cust_id']]);
                                $purcase_ar = $purchases_sql->fetchAll(PDO::FETCH_ASSOC);

                                
                                if(count($purcase_ar)>0){
                                    foreach($purcase_ar as $purc){ 
                                        
                                        $get_customer_name = check_customer_name($pdo,$purc['user_id']);
                             ?>
                                <tr>
                                    <td style="width: 50%;">
                                        <div class='row'>
                                            <div class="col-md-4">
                                                <?php if(!empty($purc['p_featured_photo'])){
                                                    if(file_exists('public_files/uploads/'.$purc['p_featured_photo'])){
                                                
                                                        echo '<img width="100%" src="public_files/uploads/'.$purc["p_featured_photo"].'" alt="product photo">';

                                                    }
                                                } ?>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="product.php?id=<?php echo $purc['product_id']; ?>"><h4 class="color-red"><?php echo $purc['product_name'] ?></h4></a>
                                                <p class="m_0"><label>Author:</label> <?php echo empty($get_customer_name)?'Admin':$get_customer_name['cust_email']; ?></p>
                                                <p  class="m_0">Purchased on : <?php echo date('d-m-Y',strtotime($purc['payment_date'])); ?> </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="td_center_align">
                                        <?php if(CURRENCY_POSITION == 'Before') {
                                            echo  CURRENCY_SYMBOL.' '.number_format($purc['unit_price'] * CURRENCY_VALUE,2) ;
                                            
                                        } else {
                                            echo  number_format($purc['unit_price'] * CURRENCY_VALUE,2).' '.CURRENCY_SYMBOL ;
                                        
                                        }  ?>
                                    </td>
                                    <td class="td_center_align">
                                        
                                        <?php 
                                            if(!empty($purc['rating'])){
                                                for($ip = 0;$ip < 5;$ip++){
                                                    if($purc['rating']> $ip){
                                                        echo '<span class="fa fa-star color-darkorange"></span>';
                                                    }else{
                                                        echo '<span class="fa fa-star-o color-darkorange"></span>';
                                                    }
                                                } 
                                            } else{
                                                echo '<a href="product.php?id='. $purc['product_id'] .'" class="btn btn-sm btn-warning">Rate Now</a>';
                                            }
                                        ?>

                                    </td>
                                    <td class="td_center_align">
                                        <?php 
                                            if(CURRENCY_POSITION == 'Before') {
                                                echo  CURRENCY_SYMBOL.' '.number_format((($purc['actual_price']-$purc['unit_price']) * CURRENCY_VALUE),2) ;
                                                
                                            } else {
                                                echo  number_format((($purc['actual_price']-$purc['unit_price']) * CURRENCY_VALUE),2).' '.CURRENCY_SYMBOL ;
                                            
                                            } 
                                        ?>

                                    </td>
                                    <td class="td_center_align">
                                        <?php if(file_exists("public_files/uploads/model_product_files/".$purc['prod_model_file'])){
                                        ?>
                                            <a href="download-product.php?zip_product=<?php echo $purc['product_id']; ?>" class="btn btn-success btn-sm"><i class="fa fa-download"></i> &nbsp; Download</a>
                                        
                                        <?php } ?>
                                        <a href="pdf_invoice.php?id=<?php echo $purc['id']; ?>" class="btn btn-dark btn-sm">Invoice</a>                                   
                                    </td>
                                </tr>
                            <?php
                                    }
                                }else{
                                    echo '<tr><td colspan="5" class="text-center"> No data Available </td></tr>';
                                }
                            ?>
                        </tbody>
                    </table>
                </div>                
            </div>
            
        </div>
    </div>

</div>

<?php require_once('footer.php'); ?>