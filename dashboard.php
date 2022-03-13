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

<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script>
    window.onload = function () {
        
        <?php 
        $dash_months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        if($_SESSION['customer']['customer_type'] == 'selling' || $_SESSION['customer']['customer_type'] == 'both'){ 

            $monthly_sale_inyear_sql = $pdo->prepare('SELECT MONTHNAME(tbl_payment.payment_date) as label,SUM(tbl_order.unit_price) as y FROM tbl_payment 
            LEFT JOIN tbl_order on tbl_payment.payment_id = tbl_order.payment_id
            LEFT JOIN tbl_product on tbl_product.p_id = tbl_order.product_id
            where tbl_product.user_type = "Customer" and tbl_product.user_id = ? and tbl_payment.payment_status = "Completed" and YEAR(tbl_payment.payment_date) = YEAR(NOW()) GROUP BY MONTH(tbl_payment.payment_date), YEAR(tbl_payment.payment_date) ORDER BY tbl_payment.payment_date ASC');
            $monthly_sale_inyear_sql->execute([$_SESSION['customer']['cust_id']]);
            $monthly_sale_inyear = $monthly_sale_inyear_sql->fetchAll(PDO::FETCH_ASSOC);

            $dataPoints = [];
            
            $empty_arr =[];
            foreach($dash_months as $month){
                $empty_arr[$month]= ['label'=>$month,'y'=>0];
            }
            foreach($monthly_sale_inyear as $month){
                if(isset($empty_arr[$month['label']])){
                    $empty_arr[$month['label']]= ['label'=>$month['label'],'y'=>$month['y']];
                }
                
            }

            foreach($empty_arr as $arr){
                $dataPoints[] = $arr;
            }
           
        ?>
        var chart = new CanvasJS.Chart("chartContainer", {
            animationEnabled: true,
            
            title:{
                text: "Sale Done In "+ new Date().getFullYear()
            },
            axisX:{
                crosshair: {
                    enabled: true,
                    snapToDataPoint: true
                }
            },
            axisY:{
                title: "in USD Currency",
                includeZero: true,
                crosshair: {
                    enabled: true,
                    snapToDataPoint: true
                }
            },
            toolTip:{
                enabled: true
            },
            data: [{
                type: "area",
                dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
            }]
        });
        chart.render();
        <?php }
        
        if($_SESSION['customer']['customer_type'] == 'buying' || $_SESSION['customer']['customer_type'] == 'both'){ 

            $monthly_sale_inyear_sql = $pdo->prepare('SELECT MONTHNAME(tbl_payment.payment_date) as label,SUM(tbl_payment.paid_amount) as y FROM tbl_payment 
            where tbl_payment.customer_id = ? and tbl_payment.payment_status = "Completed" and YEAR(tbl_payment.payment_date) = YEAR(NOW()) GROUP BY MONTH(tbl_payment.payment_date), YEAR(tbl_payment.payment_date) ORDER BY tbl_payment.payment_date ASC');
            $monthly_sale_inyear_sql->execute([$_SESSION['customer']['cust_id']]);
            $monthly_sale_inyear = $monthly_sale_inyear_sql->fetchAll(PDO::FETCH_ASSOC);

            $purchasePoints = [];
            
            $empty_arr =[];
            foreach($dash_months as $month){
                $empty_arr[$month]= ['label'=>$month,'y'=>0];
            }
            foreach($monthly_sale_inyear as $month){
                if(isset($empty_arr[$month['label']])){
                    $empty_arr[$month['label']]= ['label'=>$month['label'],'y'=>$month['y']];
                }
                
            }

            foreach($empty_arr as $arr){
                $purchasePoints[] = $arr;
            }

            
        ?>

        var purchase = new CanvasJS.Chart("purchase_container", {
            animationEnabled: true,
            
            title:{
                text: "Purchase Done In "+ new Date().getFullYear()
            },
            axisX:{
                crosshair: {
                    enabled: true,
                    snapToDataPoint: true
                }
            },
            axisY:{
                title: "in USD Currency",
                includeZero: true,
                crosshair: {
                    enabled: true,
                    snapToDataPoint: true
                }
            },
            toolTip:{
                enabled: true
            },
            data: [{
                type: "area",
                dataPoints: <?php echo json_encode($purchasePoints, JSON_NUMERIC_CHECK); ?>
            }]
        });
        purchase.render();
        <?php } ?>
    }
</script>
<div class="page">
    <div class="container">
        <div class="row">            
            <div class="col-md-12"> 
                <?php require_once('customer-sidebar.php'); ?>
            </div>
            <div class="col-md-12">
                <div class="user-content">
                    <h3 class="text-center">
                        <?php echo WELCOME_TO_THE_DASHBOARD; ?>
                    </h3>
                    <?php if($_SESSION['customer']['customer_type'] == 'selling' || $_SESSION['customer']['customer_type'] == 'both'){ ?>
                        <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                        <br>
                        <br>
                    <?php }
                    
                    if($_SESSION['customer']['customer_type'] == 'buying' || $_SESSION['customer']['customer_type'] == 'both'){ ?>
                    
                        <div id="purchase_container" style="height: 370px; width: 100%;"></div>
                    <?php } ?>
                </div>                
            </div>
            
        </div>
    </div>

</div>



<?php require_once('footer.php'); ?>